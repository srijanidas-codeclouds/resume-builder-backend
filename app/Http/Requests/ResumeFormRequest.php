<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResumeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add auth logic if needed
    }

    public function rules(): array
{
    $isCreating = $this->isMethod('post');
    $templates = implode(',', array_keys(config('resume_templates')));

    return [

        /* =====================
           CORE RESUME
        ===================== */
        'title' => 'sometimes|nullable|string|min:5|max:255',
        'summary' => 'sometimes|nullable|string|max:1500',

        'skills' => 'sometimes|nullable|array',
        'skills.*' => 'sometimes|nullable|string|min:2|max:50',

        'languages' => 'sometimes|nullable|array',
        'languages.*.name' => 'sometimes|nullable|string|min:2',
        'languages.*.level' => 'sometimes|nullable|string|in:basic,intermediate,fluent,native',

        'accent_color' => 'sometimes|nullable|string|regex:/^#([A-Fa-f0-9]{6})$/',
        'template' => 'sometimes|nullable|in:classic,modern',
        'status' => 'sometimes|nullable|in:draft,published',

        /* =====================
           PERSONAL DETAILS
        ===================== */
        'personal_details' => 'sometimes|nullable|array',
        'personal_details.full_name' => 'sometimes|nullable|string|max:255',
        'personal_details.designation' => 'sometimes|nullable|string|max:255',
        'personal_details.email' => 'sometimes|nullable|email',
        'personal_details.phone' => 'sometimes|nullable|string|max:20',
        'personal_details.location' => 'sometimes|nullable|string|max:255',

        /* =====================
           PROJECTS
        ===================== */
        'projects' => 'sometimes|nullable|array',
        'projects.*.name' => 'sometimes|nullable|string|max:255',
        'projects.*.description' => 'sometimes|nullable|string',
        'projects.*.tech_stack' => 'sometimes|nullable|array',
        'projects.*.tech_stack.*' => 'sometimes|nullable|string|max:50',
        'projects.*.start_date' => 'sometimes|nullable|date',
        'projects.*.end_date' => 'sometimes|nullable|date',
        'projects.*.live_link' => 'sometimes|nullable|url',
        'projects.*.github_link' => 'sometimes|nullable|url',

        /* =====================
           EXPERIENCES
        ===================== */
        'experiences' => 'sometimes|nullable|array',
        'experiences.*.organization' => 'sometimes|nullable|string|max:255',
        'experiences.*.position' => 'sometimes|nullable|string|max:255',
        'experiences.*.description' => 'sometimes|nullable|string',
        'experiences.*.start_date' => 'sometimes|nullable|date',
        'experiences.*.end_date' => 'sometimes|nullable|date',
        'experiences.*.is_current' => 'sometimes|nullable|boolean',

        /* =====================
           EDUCATION
        ===================== */
        'education' => 'sometimes|nullable|array',
        'education.*.institution' => 'sometimes|nullable|string|max:255',
        'education.*.degree' => 'sometimes|nullable|string|max:255',
        'education.*.field' => 'sometimes|nullable|string|max:255',
        'education.*.grade' => 'sometimes|nullable|string|max:20',
        'education.*.start_date' => 'sometimes|nullable|date',
        'education.*.end_date' => 'sometimes|nullable|date',

        /* =====================
           CERTIFICATIONS
        ===================== */
        'certifications' => 'sometimes|nullable|array',
        'certifications.*.title' => 'sometimes|nullable|string|max:255',
        'certifications.*.issuer' => 'sometimes|nullable|string|max:255',
        'certifications.*.issued_date' => 'sometimes|nullable|date',
        'certifications.*.url' => 'sometimes|nullable|url',

        /* =====================
           SOCIALS
        ===================== */
        'socials' => 'sometimes|nullable|array',
        'socials.linkedIn' => 'sometimes|nullable|url',
        'socials.github' => 'sometimes|nullable|url',
        'socials.portfolio' => 'sometimes|nullable|url',
        'socials.twitter' => 'sometimes|nullable|url',
    ];
}

}

