<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar course menggunakan data statis (dummy).
     */
    public function index(): Response
    {
        $courses = $this->getCourses();
        $levels = array_values(array_unique(array_column($courses, 'level')));

        return Inertia::render('Courses', [
            'courses' => $courses,
            'courseCount' => count($courses),
            'levels' => $levels,
        ]);
    }

    /**
     * Menampilkan detail 1 course berdasarkan id.
     */
    public function show(int $id): Response
    {
        // Cari course dari data dummy yang sama.
        $course = collect($this->getCourses())->firstWhere('id', $id);

        abort_unless($course, 404);

        return Inertia::render('CourseDetail', [
            'course' => $course,
        ]);
    }

    /**
     * Sumber data statis (tanpa database).
     */
    private function getCourses(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Laravel Dasar untuk Project Nyata',
                'description' => 'Belajar pondasi Laravel dengan studi kasus aplikasi e-learning sederhana.',
                'level' => 'Beginner',
                'total_lessons' => 12,
            ],
            [
                'id' => 2,
                'title' => 'Inertia.js + Vue 3 untuk Laravel',
                'description' => 'Membangun halaman interaktif tanpa perlu API REST terpisah.',
                'level' => 'Intermediate',
                'total_lessons' => 10,
            ],
            [
                'id' => 3,
                'title' => 'Mini LMS: Dari Fitur ke Produk',
                'description' => 'Merangkai modul belajar menjadi produk e-learning yang siap dipresentasikan.',
                'level' => 'Advanced',
                'total_lessons' => 14,
            ],
        ];
    }
}
