<?php

namespace App\Services;

use App\Models\CourseChallenge;
use App\Models\CourseChallengeSubmission;
use Illuminate\Database\Eloquent\Collection;

class CourseChallengeService
{
    /**
     * Ambil semua tantangan berdasarkan course id.
     *
     * @return Collection<int, CourseChallenge>
     */
    public function getChallengesByCourseId(int $courseId): Collection
    {
        return CourseChallenge::query()
            ->where('course_id', $courseId)
            ->orderBy('step_number')
            ->get();
    }

    /**
     * Ambil 1 challenge berdasarkan course id dan challenge id.
     */
    public function getChallengeByCourseAndId(int $courseId, int $challengeId): CourseChallenge
    {
        return CourseChallenge::query()
            ->where('course_id', $courseId)
            ->where('id', $challengeId)
            ->firstOrFail();
    }

    /**
     * Simpan jawaban tantangan.
     *
     * @param  array<string, mixed>  $payload
     */
    public function createSubmission(int $challengeId, array $payload): CourseChallengeSubmission
    {
        return CourseChallengeSubmission::query()->create([
            'course_challenge_id' => $challengeId,
            'submitter_name' => $payload['submitter_name'],
            'answer_text' => $payload['answer_text'],
            'status' => 'pending',
        ]);
    }
}
