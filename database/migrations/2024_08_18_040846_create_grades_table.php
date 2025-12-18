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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->integer('attendance')->nullable()->default(0);
            $table->integer('participation')->nullable();
            $table->integer('assignment')->nullable()->default(0);
            $table->integer('mid')->nullable()->default(0);
            $table->integer('final')->nullable()->default(0);
            $table->integer('total')->nullable()->default(0);
            $table->string('nhuruf')->nullable();
            $table->timestamp('validation_deadline')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->enum('validation_status', ['pending', 'dosen_validated', 'kaprodi_approved', 'locked'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
