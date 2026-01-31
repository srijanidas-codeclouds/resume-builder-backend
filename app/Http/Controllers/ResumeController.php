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

        $resume = $request->user()->resumes()->create([
        'title'        => $request->title ?? 'Untitled Resume',
        'summary'      => null,
        'skills'       => [],
        'languages'    => [],
        'accent_color' => $request->accent_color ?? '#2563eb',
        'template'     => $request->template ?? 'classic',
        'status'       => 'draft',
    ]);

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

    /* =====================
       CONFLICT DETECTION
    ===================== */
    if (
        $request->filled('version') &&
        (int) $request->version !== (int) $resume->version
    ) {
        return response()->json([
            'success' => false,
            'message' => 'Conflict detected. Resume was updated elsewhere.',
            'server_version' => $resume->version,
            'server_data' => new ResumeResource($resume->load([
                'personalDetails',
                'socials',
                'projects',
                'experiences',
                'education',
                'certifications',
            ])),
        ], 409);
    }

    $data = $request->validated();

    DB::transaction(function () use ($resume, $data) {

        /* =====================
           CORE (partial-safe)
        ===================== */
        $resume->update(array_filter([
            'title'        => $data['title']        ?? null,
            'summary'      => $data['summary']      ?? null,
            'skills'       => $data['skills']       ?? null,
            'languages'    => $data['languages']    ?? null,
            'accent_color' => $data['accent_color'] ?? null,
            'template'     => $data['template']     ?? null,
            'version'      => $resume->version + 1,
        ], fn ($v) => !is_null($v)));

        /* =====================
           HAS ONE
        ===================== */
        if (array_key_exists('personal_details', $data)) {
            $resume->personalDetails()->updateOrCreate(
                ['resume_id' => $resume->id],
                $data['personal_details'] ?? []
            );
        }

        if (array_key_exists('socials', $data)) {
            $resume->socials()->updateOrCreate(
                ['resume_id' => $resume->id],
                $data['socials'] ?? []
            );
        }

        /* =====================
           HAS MANY
        ===================== */
        if (array_key_exists('projects', $data)) {
            $this->syncHasMany($resume, 'projects', $data['projects'] ?? []);
        }

        if (array_key_exists('experiences', $data)) {
            $this->syncHasMany($resume, 'experiences', $data['experiences'] ?? []);
        }

        if (array_key_exists('education', $data)) {
            $this->syncHasMany($resume, 'education', $data['education'] ?? []);
        }

        if (array_key_exists('certifications', $data)) {
            $this->syncHasMany($resume, 'certifications', $data['certifications'] ?? []);
        }
    });

    return response()->json([
        'success' => true,
        'version' => $resume->fresh()->version,
        'status'  => $resume->status,
        'message' => 'Resume autosaved',
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
