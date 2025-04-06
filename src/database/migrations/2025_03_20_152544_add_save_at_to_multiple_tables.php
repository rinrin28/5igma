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
        Schema::table('expectations', function (Blueprint $table) {
            $table->dateTime('save_at')->nullable()->after('score');
        });

        Schema::table('satisfactions', function (Blueprint $table) {
            $table->dateTime('save_at')->nullable()->after('score');
        });

        Schema::table('sub_responses', function (Blueprint $table) {
            $table->dateTime('save_at')->nullable()->after('score');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expectations', function (Blueprint $table) {
            $table->dropColumn('save_at');
        });

        Schema::table('satisfactions', function (Blueprint $table) {
            $table->dropColumn('save_at');
        });

        Schema::table('sub_responses', function (Blueprint $table) {
            $table->dropColumn('save_at');
        });
    }
};
