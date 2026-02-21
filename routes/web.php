<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route utama e-learning.
// Alur request: Route -> Controller -> Inertia::render -> Vue Page.
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route daftar course sebagai pintu masuk belajar.
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

// Route detail course berdasarkan id.
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
