<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\IndexCourseRequest;
use App\Http\Requests\Course\ShowCourseRequest;
use App\Models\Course;
use App\Services\CourseService;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function __construct(
        private readonly CourseService $courseService
    ) {
    }

    /**
     * Menampilkan daftar course dari database.
     */
    public function index(IndexCourseRequest $request): Response
    {
        $filters = $request->validated();
        $courses = $this->courseService->getCourseList($filters);
        $levels = $this->courseService->getAvailableLevels();

        return Inertia::render('Courses', [
            'courses' => $courses->map(fn (Course $course) => $this->toCoursePayload($course))->values()->all(),
            'courseCount' => $this->courseService->getTotalCourseCount(),
            'levels' => $levels,
        ]);
    }

    /**
     * Menampilkan detail 1 course berdasarkan id.
     */
    public function show(ShowCourseRequest $request): Response
    {
        $courseId = (int) $request->validated('id');
        $course = $this->courseService->getCourseById($courseId);
        $completedTutorialIds = $this->getCompletedTutorialIds($request, $courseId);
        $completedLessons = min(count($completedTutorialIds), $course->total_lessons);
        $progressPercentage = $course->total_lessons > 0
            ? (int) round(($completedLessons / $course->total_lessons) * 100)
            : 0;

        return Inertia::render('CourseDetail', [
            'course' => $this->toCoursePayload($course),
            'progress' => [
                'completed_lessons' => $completedLessons,
                'total_lessons' => $course->total_lessons,
                'percentage' => $progressPercentage,
            ],
        ]);
    }

    /**
     * Normalisasi payload supaya field yang dikirim ke frontend konsisten.
     *
     * @return array<string, mixed>
     */
    private function toCoursePayload(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'level' => $course->level,
            'total_lessons' => $course->total_lessons,
            'challenge_count' => $course->challenge_count,
        ];
    }

    /**
     * Ambil daftar tutorial yang sudah selesai dari session per course.
     *
     * @return array<int, int>
     */
    private function getCompletedTutorialIds(ShowCourseRequest $request, int $courseId): array
    {
        $progress = (array) $request->session()->get('course_tutorial_progress', []);
        $tutorialIds = (array) ($progress[$courseId] ?? []);

        return array_values(array_unique(array_map(static fn ($id): int => (int) $id, $tutorialIds)));
    }
}
