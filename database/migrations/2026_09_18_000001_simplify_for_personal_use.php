<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Simplify users table for personal use (remove role and onboarding_completed)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'onboarding_completed']);
        });

        // Simplify boards table for personal use (remove team_id)
        if (Schema::hasColumn('boards', 'team_id')) {
            Schema::table('boards', function (Blueprint $table) {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            });
        }

        // Simplify shifts table for personal use (remove team_id and user_id, make user_id non-nullable)
        if (Schema::hasColumn('shifts', 'team_id')) {
            Schema::table('shifts', function (Blueprint $table) {
                $table->dropForeign(['team_id']);
                $table->dropForeign(['user_id']);
                $table->dropColumn(['team_id']);
                // Make user_id non-nullable and required
                $table->foreignId('user_id')->nullable(false)->constrained('users')->onDelete('cascade')->change();
            });
        }

        // Drop unnecessary tables for personal use
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This is a one-way migration for simplicity
        // In a real scenario, you would rebuild the dropped tables and columns
    }
};