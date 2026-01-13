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
        return [

            /* =====================
               CORE RESUME
            ===================== */
            'title' => 'required|string|min:5|max:255',
            'summary' => 'required|string|min:50|max:1500',
            'skills' => 'required|array|min:1',
            'skills.*' => 'string|min:2|max:50',
            'languages' => 'required|array|min:1',
            'languages.*.name' => 'required|string|min:2',
            'languages.*.level' => 'required|string|in:basic,intermediate,fluent,native',
            'accent_color' => 'required|string',
            'template' => 'required|string',

            /* =====================
               PERSONAL DETAILS
            ===================== */
            'personal_details' => 'required|array',
            'personal_details.full_name' => 'required|string|max:255',
            'personal_details.designation' => 'required|string|max:255',
            'personal_details.email' => 'required|email',
            'personal_details.phone' => 'nullable|string|max:20',
            'personal_details.location' => 'nullable|string|max:255',

            /* =====================
               PROJECTS
            ===================== */
            'projects' => 'nullable|array',
            'projects.*.name' => 'required|string|max:255',
            'projects.*.description' => 'required|string',
            'projects.*.tech_stack' => 'required|array|min:1',
            'projects.*.tech_stack.*' => 'string|max:50',
            'projects.*.start_date' => 'required|date',
            'projects.*.end_date' => 'nullable|date|after_or_equal:projects.*.start_date',
            'projects.*.live_link' => 'nullable|url',
            'projects.*.github_link' => 'nullable|url',

            /* =====================
               EXPERIENCES
            ===================== */
            'experiences' => 'nullable|array',
            'experiences.*.organization' => 'required|string|max:255',
            'experiences.*.position' => 'required|string|max:255',
            'experiences.*.description' => 'required|string',
            'experiences.*.start_date' => 'required|date',
            'experiences.*.end_date' => 'nullable|date|after_or_equal:experiences.*.start_date',
            'experiences.*.is_current' => 'boolean',

            /* =====================
               EDUCATION
            ===================== */
            'education' => 'nullable|array',
            'education.*.institution' => 'required|string|max:255',
            'education.*.degree' => 'required|string|max:255',
            'education.*.field' => 'nullable|string|max:255',
            'education.*.grade' => 'nullable|string|max:20',
            'education.*.start_date' => 'required|date',
            'education.*.end_date' => 'nullable|date|after_or_equal:education.*.start_date',

            /* =====================
               CERTIFICATIONS
            ===================== */
            'certifications' => 'nullable|array',
            'certifications.*.title' => 'required|string|max:255',
            'certifications.*.issuer' => 'nullable|string|max:255',
            'certifications.*.issued_date' => 'nullable|date',
            'certifications.*.url' => 'nullable|url',

            /* =====================
               SOCIALS
            ===================== */
            'socials' => 'nullable|array',
            'socials.linkedin' => 'nullable|url',
            'socials.github' => 'nullable|url',
            'socials.portfolio' => 'nullable|url',
            'socials.twitter' => 'nullable|url',
        ];
    }
}

