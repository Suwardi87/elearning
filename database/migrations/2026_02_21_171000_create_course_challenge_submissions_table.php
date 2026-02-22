<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_challenge_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_challenge_id')->constrained('course_challenges')->cascadeOnDelete();
            $table->string('submitter_name');
            $table->longText('answer_text');
            $table->string('status', 30)->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_challenge_submissions');
    }
};
