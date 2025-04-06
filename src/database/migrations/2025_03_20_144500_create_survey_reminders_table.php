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
        Schema::create('survey_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys');
            $table->dateTime('reminder_date');
            $table->enum('reminder_type', ['initial', 'follow_up', 'final']);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_reminders');
    }
};
