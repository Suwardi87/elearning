<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    /**
     * Ambil daftar course dengan filter sederhana.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Course>
     */
    public function getCourseList(array $filters = []): Collection
    {
        $query = Course::query()->select([
            'id',
            'title',
            'description',
            'level',
            'total_lessons',
            'challenge_count',
        ]);

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        if (!empty($filters['level']) && $filters['level'] !== 'All') {
            $query->where('level', $filters['level']);
        }

        $sort = $filters['sort'] ?? 'default';

        if ($sort === 'title_asc') {
            $query->orderBy('title');
        } elseif ($sort === 'lessons_desc') {
            $query->orderByDesc('total_lessons');
        } elseif ($sort === 'lessons_asc') {
            $query->orderBy('total_lessons');
        } else {
            $query->orderBy('id');
        }

        return $query->get();
    }

    /**
     * Ambil daftar level unik untuk filter.
     *
     * @return array<int, string>
     */
    public function getAvailableLevels(): array
    {
        return Course::query()
            ->orderBy('level')
            ->distinct()
            ->pluck('level')
            ->values()
            ->all();
    }

    /**
     * Total seluruh course.
     */
    public function getTotalCourseCount(): int
    {
        return Course::query()->count();
    }

    /**
     * Ambil 1 course, gagal 404 jika tidak ditemukan.
     */
    public function getCourseById(int $id): Course
    {
        return Course::query()->findOrFail($id);
    }
}
