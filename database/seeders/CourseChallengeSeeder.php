<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseChallenge;
use Illuminate\Database\Seeder;

class CourseChallengeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();
        $payloads = [];

        Course::query()->select(['id', 'title', 'challenge_count'])->get()->each(function (Course $course) use (&$payloads, $now): void {
            for ($step = 1; $step <= $course->challenge_count; $step++) {
                $payloads[] = [
                    'course_id' => $course->id,
                    'step_number' => $step,
                    'title' => "Tantangan {$step}",
                    'instruction' => "Selesaikan tantangan {$step} untuk course {$course->title}. Fokus pada implementasi fitur sesuai materi step ini.",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        });

        CourseChallenge::query()->upsert(
            $payloads,
            ['course_id', 'step_number'],
            ['title', 'instruction', 'updated_at']
        );
    }
}
