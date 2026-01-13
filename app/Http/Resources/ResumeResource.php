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
                'skills' => $this->skills,
                'languages' => $this->languages,
                'accent_color' => $this->accent_color,
                'template' => $this->template,

                'personal_details' => $this->personalDetails ? [
                    'full_name' => $this->personalDetails->full_name,
                    'designation' => $this->personalDetails->designation,
                    'email' => $this->personalDetails->email,
                    'phone' => $this->personalDetails->phone,
                    'location' => $this->personalDetails->location,
                ] : null,

                'projects' => $this->projects ? $this->projects->map(function ($project) {
                    return [
                        'name' => $project->name,
                        'description' => $project->description,
                        'tech_stack' => $project->tech_stack,
                        'start_date' => $project->start_date->toDateString(),
                        'end_date' => optional($project->end_date)->toDateString(),
                        'live_link' => $project->live_link,
                        'github_link' => $project->github_link,
                    ];
                }) : [],

                'experiences' => $this->experiences ? $this->experiences->map(function ($exp) {
                    return [
                        'organization' => $exp->organization,
                        'position' => $exp->position,
                        'description' => $exp->description,
                        'start_date' => $exp->start_date->toDateString(),
                        'end_date' => optional($exp->end_date)->toDateString(),
                        'is_current' => $exp->is_current,
                    ];
                }) : [],

                'education' => $this->education ? $this->education->map(function ($edu) {
                    return [
                        'institution' => $edu->institution,
                        'degree' => $edu->degree,
                        'field' => $edu->field,
                        'grade' => $edu->grade,
                        'start_date' => $edu->start_date->toDateString(),
                        'end_date' => optional($edu->end_date)->toDateString(),
                    ];
                }) : [],

                'certifications' => $this->certifications ? $this->certifications->map(function ($cert) {
                    return [
                        'title' => $cert->title,
                        'issuer' => $cert->issuer,
                        'issued_date' => optional($cert->issued_date)->toDateString(),
                        'url' => $cert->url,
                    ];
                }) : [],

                'socials' => $this->socials ? [
                    'linkedIn'  => $this->socials->linkedIn,
                    'github'    => $this->socials->github,
                    'portfolio' => $this->socials->portfolio,
                    'twitter'   => $this->socials->twitter,
                ] : null,
            ]
        ];
    }
}
