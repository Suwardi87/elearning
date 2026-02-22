<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\IndexCourseTutorialRequest;
use App\Http\Requests\Course\ShowCourseTutorialRequest;
use App\Services\CourseService;
use App\Services\CourseTutorialService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CourseTutorialController extends Controller
{
    public function __construct(
        private readonly CourseService $courseService,
        private readonly CourseTutorialService $courseTutorialService
    ) {
    }

    /**
     * Menampilkan daftar tutorial untuk 1 course.
     */
    public function index(IndexCourseTutorialRequest $request): Response
    {
        $courseId = (int) $request->validated('id');
        $course = $this->courseService->getCourseById($courseId);
        $tutorials = $this->courseTutorialService->getTutorialsByCourseId($courseId);
        $completedTutorialIds = $this->getCompletedTutorialIds($request, $courseId);
        $completedCount = $tutorials->filter(
            fn ($tutorial): bool => in_array($tutorial->id, $completedTutorialIds, true)
        )->count();
        $progressPercentage = $tutorials->count() > 0
            ? (int) round(($completedCount / $tutorials->count()) * 100)
            : 0;

        return Inertia::render('CourseTutorials', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'total_lessons' => $course->total_lessons,
            ],
            'progress' => [
                'completed_lessons' => $completedCount,
                'total_lessons' => $tutorials->count(),
                'percentage' => $progressPercentage,
            ],
            'tutorials' => $tutorials->map(fn ($tutorial) => [
                'id' => $tutorial->id,
                'lesson_number' => $tutorial->lesson_number,
                'title' => $tutorial->title,
                'estimated_minutes' => $this->estimateMinutes($tutorial->lesson_number),
                'is_completed' => in_array($tutorial->id, $completedTutorialIds, true),
            ])->values()->all(),
        ]);
    }

    /**
     * Menampilkan detail 1 tutorial.
     */
    public function show(ShowCourseTutorialRequest $request): Response
    {
        $courseId = (int) $request->validated('id');
        $tutorialId = (int) $request->validated('tutorial_id');

        $course = $this->courseService->getCourseById($courseId);
        $tutorial = $this->courseTutorialService->getTutorialByCourseAndId($courseId, $tutorialId);
        $previousTutorial = $this->courseTutorialService->getPreviousTutorial($courseId, $tutorial->lesson_number);
        $nextTutorial = $this->courseTutorialService->getNextTutorial($courseId, $tutorial->lesson_number);
        $completedTutorialIds = $this->getCompletedTutorialIds($request, $courseId);

        return Inertia::render('CourseTutorialDetail', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'total_lessons' => $course->total_lessons,
            ],
            'tutorial' => [
                'id' => $tutorial->id,
                'lesson_number' => $tutorial->lesson_number,
                'title' => $tutorial->title,
                'content' => $tutorial->content,
                'estimated_minutes' => $this->estimateMinutes($tutorial->lesson_number),
                'is_completed' => in_array($tutorial->id, $completedTutorialIds, true),
            ],
            'previousTutorial' => $previousTutorial ? [
                'id' => $previousTutorial->id,
                'lesson_number' => $previousTutorial->lesson_number,
            ] : null,
            'nextTutorial' => $nextTutorial ? [
                'id' => $nextTutorial->id,
                'lesson_number' => $nextTutorial->lesson_number,
            ] : null,
        ]);
    }

    /**
     * Simpan status selesai tutorial ke session.
     */
    public function complete(ShowCourseTutorialRequest $request): RedirectResponse
    {
        $courseId = (int) $request->validated('id');
        $tutorialId = (int) $request->validated('tutorial_id');

        // Pastikan tutorial memang milik course terkait.
        $this->courseTutorialService->getTutorialByCourseAndId($courseId, $tutorialId);

        $progress = (array) $request->session()->get('course_tutorial_progress', []);
        $progress[$courseId] = $this->normalizeTutorialIds([
            ...($progress[$courseId] ?? []),
            $tutorialId,
        ]);

        $request->session()->put('course_tutorial_progress', $progress);

        return redirect()
            ->route('courses.tutorials.show', ['id' => $courseId, 'tutorialId' => $tutorialId])
            ->with('success', 'Tutorial berhasil ditandai selesai.');
    }

    /**
     * Batalkan status selesai tutorial dari session.
     */
    public function uncomplete(ShowCourseTutorialRequest $request): RedirectResponse
    {
        $courseId = (int) $request->validated('id');
        $tutorialId = (int) $request->validated('tutorial_id');

        // Pastikan tutorial memang milik course terkait.
        $this->courseTutorialService->getTutorialByCourseAndId($courseId, $tutorialId);

        $progress = (array) $request->session()->get('course_tutorial_progress', []);
        $progress[$courseId] = array_values(array_filter(
            $this->normalizeTutorialIds($progress[$courseId] ?? []),
            static fn (int $id): bool => $id !== $tutorialId
        ));

        $request->session()->put('course_tutorial_progress', $progress);

        return redirect()
            ->route('courses.tutorials.show', ['id' => $courseId, 'tutorialId' => $tutorialId])
            ->with('success', 'Status tutorial dikembalikan ke belum selesai.');
    }

    /**
     * Estimasi durasi belajar per lesson (menit).
     */
    private function estimateMinutes(int $lessonNumber): int
    {
        if ($lessonNumber <= 3) {
            return 15;
        }

        if ($lessonNumber <= 7) {
            return 20;
        }

        return 25;
    }

    /**
     * Ambil daftar tutorial yang sudah selesai dari session per course.
     *
     * @return array<int, int>
     */
    private function getCompletedTutorialIds(IndexCourseTutorialRequest|ShowCourseTutorialRequest $request, int $courseId): array
    {
        $progress = (array) $request->session()->get('course_tutorial_progress', []);

        return $this->normalizeTutorialIds($progress[$courseId] ?? []);
    }

    /**
     * Rapikan data id tutorial agar unik dan bertipe integer.
     *
     * @param array<int, mixed> $tutorialIds
     * @return array<int, int>
     */
    private function normalizeTutorialIds(array $tutorialIds): array
    {
        return array_values(array_unique(array_map(static fn ($id): int => (int) $id, $tutorialIds)));
    }
}
