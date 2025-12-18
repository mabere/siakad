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
        Schema::create('thesis_supervisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('lecturers')->cascadeOnDelete();
            $table->enum('supervisor_role', ['pembimbing_1', 'pembimbing_2'])->index();
            $table->enum('status', ['active', 'completed', 'terminated'])->default('active');
            $table->timestamp('assigned_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_supervisions');
    }
};
