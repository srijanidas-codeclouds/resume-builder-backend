<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResumeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add auth logic if needed
    }

    public function rules(): array
    {
        $isCreating = $this->isMethod('post');

        return [

            /* =====================
               CORE RESUME
            ===================== */
            'version' => 'sometimes|nullable|integer|min:0',
            'title' => 'sometimes|nullable|string|max:255',
            'summary' => 'sometimes|nullable|string|max:2000',

            'skills' => 'sometimes|nullable|array',
            'skills.*' => 'string|min:1|max:50',

            'languages' => 'sometimes|nullable|array',
            'languages.*.name' => 'required_with:languages.*|string|min:2|max:100',
            'languages.*.level' => 'nullable|string|in:basic,intermediate,fluent,native,beginner,advanced,professional',

            'accent_color' => 'sometimes|nullable|string|regex:/^#([A-Fa-f0-9]{6})$/',
            'template' => 'sometimes|nullable|in:classic,modern,professional',
            'status' => 'sometimes|nullable|in:draft,published',

            /* =====================
               PERSONAL DETAILS
            ===================== */
            'personal_details' => 'sometimes|nullable|array',
            'personal_details.full_name' => 'nullable|string|max:255',
            'personal_details.designation' => 'nullable|string|max:255',
            'personal_details.email' => 'nullable|email|max:255',
            'personal_details.phone' => 'nullable|string|max:20',
            'personal_details.location' => 'nullable|string|max:255',

            /* =====================
               PROJECTS - FIXED: tech_stack as comma-separated string
            ===================== */
            'projects' => 'sometimes|nullable|array',
            'projects.*.name' => 'nullable|string|max:255',
            'projects.*.description' => 'nullable|string|max:2000',
            'projects.*.tech_stack' => 'nullable|string|max:1000', // Changed from array to string
            'projects.*.start_date' => 'nullable|date_format:Y-m-d',
            'projects.*.end_date' => 'nullable|date_format:Y-m-d|after_or_equal:projects.*.start_date',
            'projects.*.live_link' => 'nullable|url|max:500',
            'projects.*.github_link' => 'nullable|url|max:500',

            /* =====================
               EXPERIENCES - FIXED: Added location field
            ===================== */
            'experiences' => 'sometimes|nullable|array',
            'experiences.*.organization' => 'nullable|string|max:255',
            'experiences.*.position' => 'nullable|string|max:255',
            'experiences.*.location' => 'nullable|string|max:255', // Added location
            'experiences.*.description' => 'nullable|string|max:2000',
            'experiences.*.start_date' => 'nullable|date_format:Y-m-d',
            'experiences.*.end_date' => 'nullable|date_format:Y-m-d|after_or_equal:experiences.*.start_date',
            'experiences.*.is_current' => 'nullable|boolean',

            /* =====================
               EDUCATION
            ===================== */
            'education' => 'sometimes|nullable|array',
            'education.*.institution' => 'nullable|string|max:255',
            'education.*.degree' => 'nullable|string|max:255',
            'education.*.field' => 'nullable|string|max:255',
            'education.*.grade' => 'nullable|string|max:20',
            'education.*.start_date' => 'nullable|date_format:Y-m-d',
            'education.*.end_date' => 'nullable|date_format:Y-m-d|after_or_equal:education.*.start_date',

            /* =====================
               CERTIFICATIONS
            ===================== */
            'certifications' => 'sometimes|nullable|array',
            'certifications.*.title' => 'nullable|string|max:255',
            'certifications.*.issuer' => 'nullable|string|max:255',
            'certifications.*.issued_date' => 'nullable|date_format:Y-m-d',
            'certifications.*.url' => 'nullable|url|max:500',

            /* =====================
               SOCIALS
            ===================== */
            'socials' => 'sometimes|nullable|array',
            'socials.linkedIn' => 'nullable|url|max:500',
            'socials.github' => 'nullable|url|max:500',
            'socials.portfolio' => 'nullable|url|max:500',
            'socials.twitter' => 'nullable|url|max:500',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.max' => 'Resume title cannot exceed 255 characters',
            'summary.max' => 'Summary cannot exceed 2000 characters',
            'accent_color.regex' => 'Accent color must be a valid hex color code',
            'template.in' => 'Template must be classic, modern, or professional',
            'personal_details.email.email' => 'Please provide a valid email address',
            'skills.*.max' => 'Each skill cannot exceed 50 characters',
            'languages.*.name.required_with' => 'Language name is required',
            'projects.*.tech_stack.string' => 'Technology stack must be a comma-separated string',
            'experiences.*.end_date.after_or_equal' => 'End date must be after or equal to start date',
            'education.*.end_date.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }

    /**
     * Handle a failed validation attempt - Return JSON for API
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Prepare the data for validation
     * Convert empty strings to null for optional fields
     */
    protected function prepareForValidation()
    {
        // Convert is_current string to boolean if needed
        if ($this->has('experiences')) {
            $experiences = $this->input('experiences', []);
            foreach ($experiences as $key => $exp) {
                if (isset($exp['is_current'])) {
                    $experiences[$key]['is_current'] = filter_var(
                        $exp['is_current'], 
                        FILTER_VALIDATE_BOOLEAN, 
                        FILTER_NULL_ON_FAILURE
                    );
                }
            }
            $this->merge(['experiences' => $experiences]);
        }
    }
}