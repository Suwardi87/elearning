<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseChallengeSubmissionRequest extends FormRequest
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
            'challenge_id' => ['required', 'integer', 'exists:course_challenges,id'],
            'submitter_name' => ['required', 'string', 'max:100'],
            'answer_text' => ['required', 'string', 'min:20'],
        ];
    }

    /**
     * Ambil parameter route agar ikut tervalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
            'challenge_id' => $this->route('challengeId'),
        ]);
    }
}
