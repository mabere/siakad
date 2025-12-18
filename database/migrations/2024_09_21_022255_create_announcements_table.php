<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('category');
            $table->boolean('is_active')->nullable()->default(true);
            $table->string('target_role')->default('semua');
            $table->index('target_role', 'announcements_target_role_idx');
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->index(['target_role', 'faculty_id', 'department_id', 'kelas_id'], 'announcements_target_idx');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
