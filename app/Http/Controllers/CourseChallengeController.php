<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\IndexCourseChallengeRequest;
use App\Http\Requests\Course\ShowCourseChallengeRequest;
use App\Http\Requests\Course\StoreCourseChallengeSubmissionRequest;
use App\Services\CourseChallengeService;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CourseChallengeController extends Controller
{
    public function __construct(
        private readonly CourseService $courseService,
        private readonly CourseChallengeService $courseChallengeService
    ) {
    }

    /**
     * Menampilkan daftar tantangan untuk 1 course.
     */
    public function index(IndexCourseChallengeRequest $request): Response
    {
        $courseId = (int) $request->validated('id');
        $course = $this->courseService->getCourseById($courseId);
        $challenges = $this->courseChallengeService->getChallengesByCourseId($courseId);

        return Inertia::render('CourseChallenges', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'challenge_count' => $course->challenge_count,
            ],
            'challenges' => $challenges->map(static fn ($challenge) => [
                'id' => $challenge->id,
                'step_number' => $challenge->step_number,
                'title' => $challenge->title,
                'instruction' => $challenge->instruction,
            ])->values()->all(),
        ]);
    }

    /**
     * Menampilkan detail 1 tantangan.
     */
    public function show(ShowCourseChallengeRequest $request): Response
    {
        $courseId = (int) $request->validated('id');
        $challengeId = (int) $request->validated('challenge_id');

        $course = $this->courseService->getCourseById($courseId);
        $challenge = $this->courseChallengeService->getChallengeByCourseAndId($courseId, $challengeId);

        return Inertia::render('CourseChallengeDetail', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
            ],
            'challenge' => [
                'id' => $challenge->id,
                'step_number' => $challenge->step_number,
                'title' => $challenge->title,
                'instruction' => $challenge->instruction,
            ],
        ]);
    }

    /**
     * Simpan jawaban tantangan.
     */
    public function store(StoreCourseChallengeSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $courseId = (int) $validated['id'];
        $challengeId = (int) $validated['challenge_id'];

        // Pastikan challenge memang milik course yang diminta.
        $challenge = $this->courseChallengeService->getChallengeByCourseAndId($courseId, $challengeId);

        $this->courseChallengeService->createSubmission($challenge->id, [
            'submitter_name' => $validated['submitter_name'],
            'answer_text' => $validated['answer_text'],
        ]);

        return redirect()
            ->route('courses.challenges.show', ['id' => $courseId, 'challengeId' => $challengeId])
            ->with('success', 'Jawaban tantangan berhasil dikirim.');
    }
}
