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
        Schema::table('proposals', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->after('id'); // 部署IDを追加
            $table->unsignedBigInteger('survey_id')->after('is_active')->nullable(); // 全社アンケートのIDを追加（NULL許容）

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['survey_id']);
            $table->dropColumn(['department_id', 'survey_id']);
        });
    }
};
