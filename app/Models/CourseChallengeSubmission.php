<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseChallengeSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_challenge_id',
        'submitter_name',
        'answer_text',
        'status',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(CourseChallenge::class, 'course_challenge_id');
    }
}
