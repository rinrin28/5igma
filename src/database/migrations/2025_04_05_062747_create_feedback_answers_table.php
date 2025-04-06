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
        Schema::create('feedback_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('feedback_question_id')->constrained()->onDelete('cascade');
            $table->integer('answer')->comment('五段階評価（1-5）');
            $table->boolean('is_completed')->default(false);
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_answers');
    }
};
