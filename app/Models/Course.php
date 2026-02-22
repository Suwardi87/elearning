<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'level',
        'total_lessons',
        'challenge_count',
    ];

    public function challenges(): HasMany
    {
        return $this->hasMany(CourseChallenge::class);
    }

    public function tutorials(): HasMany
    {
        return $this->hasMany(CourseTutorial::class);
    }
}
