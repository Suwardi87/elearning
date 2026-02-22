<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();

        Course::query()->upsert(
            [
                [
                    'title' => 'PHP Dasar by Project',
                    'description' => 'Belajar syntax, variabel, kondisi, loop, dan function PHP melalui mini project bertahap.',
                    'level' => 'Beginner',
                    'total_lessons' => 10,
                    'challenge_count' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'title' => 'OOP PHP Dasar by Project',
                    'description' => 'Memahami class, object, inheritance, dan interface dengan studi kasus aplikasi sederhana.',
                    'level' => 'Beginner',
                    'total_lessons' => 9,
                    'challenge_count' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'title' => 'MVC Dasar (PHP Native) by Project',
                    'description' => 'Menyusun alur Model, View, dan Controller secara manual agar paham pondasi framework.',
                    'level' => 'Beginner',
                    'total_lessons' => 8,
                    'challenge_count' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'title' => 'Vue 3 Dasar by Project',
                    'description' => 'Belajar component, props, event, dan state lokal Vue 3 lewat pembuatan fitur interaktif.',
                    'level' => 'Intermediate',
                    'total_lessons' => 10,
                    'challenge_count' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'title' => 'Laravel Dasar by Project',
                    'description' => 'Masuk ke Laravel dari routing, controller, model, migration, sampai struktur project nyata.',
                    'level' => 'Intermediate',
                    'total_lessons' => 11,
                    'challenge_count' => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'title' => 'Inertia.js Dasar untuk Laravel',
                    'description' => 'Memahami flow Laravel ke Vue tanpa API terpisah: Inertia::render, props, dan page navigation.',
                    'level' => 'Intermediate',
                    'total_lessons' => 8,
                    'challenge_count' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'title' => 'Full Project: Laravel + Inertia + Vue',
                    'description' => 'Merangkai semua materi jadi produk e-learning utuh dari requirement sampai deploy.',
                    'level' => 'Advanced',
                    'total_lessons' => 14,
                    'challenge_count' => 6,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['title'],
            ['description', 'level', 'total_lessons', 'challenge_count', 'updated_at']
        );
    }
}
