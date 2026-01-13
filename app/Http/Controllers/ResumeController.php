<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResumeFormRequest;
use App\Http\Resources\ResumeResource;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResumeController extends Controller
{
    /* ======================
        LIST RESUMES
    ====================== */
    public function index(Request $request)
    {
        $resumes = $request->user()
            ->resumes()
            ->latest('updated_at')
            ->get(['id', 'title', 'template', 'updated_at']);

        return response()->json([
            'success' => true,
            'data' => $resumes
        ]);
    }

    /* ======================
        SHOW RESUME
    ====================== */
    public function show(Request $request, string $id)
    {
        $resume = $request->user()
            ->resumes()
            ->with([
                'personalDetails',
                'projects',
                'experiences',
                'education',
                'certifications',
                'socials',
            ])
            ->findOrFail($id);

        return new ResumeResource($resume);
    }

    /* ======================
        CREATE RESUME
    ====================== */
    public function store(ResumeFormRequest $request)
    {
        $user = $request->user();

        $resume = $user->resumes()->create(
            $request->only([
                'title',
                'summary',
                'skills',
                'languages',
                'accent_color',
                'template',
            ])
        );

        return new ResumeResource($resume);
    }

    /* ======================
        UPDATE RESUME
    ====================== */
    public function update(ResumeFormRequest $request, string $id)
    {
        $resume = $request->user()
            ->resumes()
            ->findOrFail($id);

        $data = $request->validated();

        DB::transaction(function () use ($resume, $data) {

            /* ---------- CORE ---------- */
            $resume->update([
                'title'        => $data['title'],
                'summary'      => $data['summary'],
                'skills'       => $data['skills'],
                'languages'    => $data['languages'],
                'accent_color' => $data['accent_color'],
                'template'     => $data['template'],
            ]);

            /* ---------- PERSONAL (hasOne) ---------- */
            if (!empty($data['personal_details'])) {
                $resume->personalDetails()->updateOrCreate(
                    [],
                    $data['personal_details']
                );
            }

            /* ---------- HAS MANY HELPERS ---------- */
            $this->syncHasMany($resume, 'projects', $data['projects'] ?? []);
            $this->syncHasMany($resume, 'experiences', $data['experiences'] ?? []);
            $this->syncHasMany($resume, 'education', $data['education'] ?? []);
            $this->syncHasMany($resume, 'certifications', $data['certifications'] ?? []);

            /* ---------- SOCIALS (hasOne) ---------- */
            if (!empty($data['socials'])) {
                $resume->socials()->updateOrCreate(
                    [],
                    $data['socials']
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Resume updated successfully'
        ]);
    }

    /* ======================
        DELETE RESUME
    ====================== */
    public function destroy(Request $request, string $id)
    {
        $resume = $request->user()
            ->resumes()
            ->findOrFail($id);

        $resume->delete();

        return response()->json(['success' => true]);
    }

    /* ======================
        SAFE HAS-MANY SYNC
    ====================== */
    protected function syncHasMany($resume, string $relation, array $items): void
    {
        if (!is_array($items)) {
            return;
        }

        $resume->$relation()->delete();

        if (count($items) === 0) {
            return;
        }

        $resume->$relation()->createMany(
            collect($items)
                ->filter(fn($item) => is_array($item))
                ->values()
                ->toArray()
        );
    }
}
