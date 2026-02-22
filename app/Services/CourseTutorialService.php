<?php

namespace App\Services;

use App\Models\CourseTutorial;
use Illuminate\Database\Eloquent\Collection;

class CourseTutorialService
{
    /**
     * Ambil semua tutorial berdasarkan course id.
     *
     * @return Collection<int, CourseTutorial>
     */
    public function getTutorialsByCourseId(int $courseId): Collection
    {
        return CourseTutorial::query()
            ->where('course_id', $courseId)
            ->orderBy('lesson_number')
            ->get();
    }

    /**
     * Ambil 1 tutorial berdasarkan course id dan tutorial id.
     */
    public function getTutorialByCourseAndId(int $courseId, int $tutorialId): CourseTutorial
    {
        return CourseTutorial::query()
            ->where('course_id', $courseId)
            ->where('id', $tutorialId)
            ->firstOrFail();
    }

    /**
     * Ambil tutorial sebelumnya berdasarkan lesson number.
     */
    public function getPreviousTutorial(int $courseId, int $currentLessonNumber): ?CourseTutorial
    {
        return CourseTutorial::query()
            ->where('course_id', $courseId)
            ->where('lesson_number', '<', $currentLessonNumber)
            ->orderByDesc('lesson_number')
            ->first();
    }

    /**
     * Ambil tutorial berikutnya berdasarkan lesson number.
     */
    public function getNextTutorial(int $courseId, int $currentLessonNumber): ?CourseTutorial
    {
        return CourseTutorial::query()
            ->where('course_id', $courseId)
            ->where('lesson_number', '>', $currentLessonNumber)
            ->orderBy('lesson_number')
            ->first();
    }
}
