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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['management', 'executive', 'employee', 'admin']);
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('client_company_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('client_company_id')->references('id')->on('client_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['client_company_id']);
            $table->dropColumn(['role', 'department_id', 'client_company_id']);
        });
    }
};
