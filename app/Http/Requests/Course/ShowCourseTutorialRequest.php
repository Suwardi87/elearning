<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class ShowCourseTutorialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:courses,id'],
            'tutorial_id' => ['required', 'integer', 'exists:course_tutorials,id'],
        ];
    }

    /**
     * Ambil parameter dari route agar tervalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
            'tutorial_id' => $this->route('tutorialId'),
        ]);
    }
}
