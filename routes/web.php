<?php

use App\Http\Controllers\CourseChallengeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseTutorialController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route utama e-learning.
// Alur request: Route -> Controller -> Inertia::render -> Vue Page.
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route daftar course sebagai pintu masuk belajar.
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

// Route detail course berdasarkan id.
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');

// Route daftar dan detail tutorial per course.
Route::get('/courses/{id}/tutorials', [CourseTutorialController::class, 'index'])->name('courses.tutorials.index');
Route::get('/courses/{id}/tutorials/{tutorialId}', [CourseTutorialController::class, 'show'])->name('courses.tutorials.show');
Route::post('/courses/{id}/tutorials/{tutorialId}/complete', [CourseTutorialController::class, 'complete'])->name('courses.tutorials.complete');
Route::delete('/courses/{id}/tutorials/{tutorialId}/complete', [CourseTutorialController::class, 'uncomplete'])->name('courses.tutorials.uncomplete');

// Route daftar tantangan per course.
Route::get('/courses/{id}/challenges', [CourseChallengeController::class, 'index'])->name('courses.challenges.index');

// Route detail dan submit jawaban tantangan.
Route::get('/courses/{id}/challenges/{challengeId}', [CourseChallengeController::class, 'show'])->name('courses.challenges.show');
Route::post('/courses/{id}/challenges/{challengeId}/submit', [CourseChallengeController::class, 'store'])->name('courses.challenges.submit');
