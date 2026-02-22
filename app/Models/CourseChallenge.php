<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'step_number',
        'title',
        'instruction',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(CourseChallengeSubmission::class, 'course_challenge_id');
    }
}
