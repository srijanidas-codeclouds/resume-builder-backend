<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'title' => $this->title,
                'summary' => $this->summary,
                'skills' => $this->skills ?? [],
                'languages' => $this->languages ?? [],
                'accent_color' => $this->accent_color ?? '#2563eb',
                'template' => $this->template ?? 'classic',
                'version' => $this->version ?? 1, // Added version for conflict detection
                'status' => $this->status ?? 'draft',
                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),

                'personal_details' => $this->personalDetails ? [
                    'full_name' => $this->personalDetails->full_name ?? '',
                    'designation' => $this->personalDetails->designation ?? '',
                    'email' => $this->personalDetails->email ?? '',
                    'phone' => $this->personalDetails->phone ?? '',
                    'location' => $this->personalDetails->location ?? '',
                ] : [
                    'full_name' => '',
                    'designation' => '',
                    'email' => '',
                    'phone' => '',
                    'location' => '',
                ],

                'projects' => $this->whenLoaded('projects', function () {
                    return $this->projects->map(function ($project) {
                        return [
                            'name' => $project->name ?? '',
                            'description' => $project->description ?? '',
                            'tech_stack' => $project->tech_stack ?? '', // Already a string from DB
                            'start_date' => $project->start_date ? $project->start_date->format('d M Y') : null,
                            'end_date' => $project->end_date ? $project->end_date->toDateString() : null,
                            'live_link' => $project->live_link ?? '',
                            'github_link' => $project->github_link ?? '',
                        ];
                    })->toArray();
                }, []),

                'experiences' => $this->whenLoaded('experiences', function () {
                    return $this->experiences->map(function ($exp) {
                        return [
                            'organization' => $exp->organization ?? '',
                            'position' => $exp->position ?? '',
                            'location' => $exp->location ?? '', // Added location field
                            'description' => $exp->description ?? '',
                            'start_date' => $exp->start_date ? $exp->start_date->toDateString() : null,
                            'end_date' => $exp->end_date ? $exp->end_date->toDateString() : null,
                            'is_current' => (bool) ($exp->is_current ?? false),
                        ];
                    })->toArray();
                }, []),

                'education' => $this->whenLoaded('education', function () {
                    return $this->education->map(function ($edu) {
                        return [
                            'institution' => $edu->institution ?? '',
                            'degree' => $edu->degree ?? '',
                            'field' => $edu->field ?? '',
                            'grade' => $edu->grade ?? '',
                            'start_date' => $edu->start_date ? $edu->start_date->toDateString() : null,
                            'end_date' => $edu->end_date ? $edu->end_date->toDateString() : null,
                        ];
                    })->toArray();
                }, []),

                'certifications' => $this->whenLoaded('certifications', function () {
                    return $this->certifications->map(function ($cert) {
                        return [
                            'title' => $cert->title ?? '',
                            'issuer' => $cert->issuer ?? '',
                            'issued_date' => $cert->issued_date ? $cert->issued_date->toDateString() : null,
                            'url' => $cert->url ?? '',
                        ];
                    })->toArray();
                }, []),

                'socials' => $this->whenLoaded('socials', function () {
                    return $this->socials ? [
                        'linkedIn'  => $this->socials->linkedIn ?? '',
                        'github'    => $this->socials->github ?? '',
                        'portfolio' => $this->socials->portfolio ?? '',
                        'twitter'   => $this->socials->twitter ?? '',
                    ] : [
                        'linkedIn'  => '',
                        'github'    => '',
                        'portfolio' => '',
                        'twitter'   => '',
                    ];
                }, [
                    'linkedIn'  => '',
                    'github'    => '',
                    'portfolio' => '',
                    'twitter'   => '',
                ]),
            ]
        ];
    }
}