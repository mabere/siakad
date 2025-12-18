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
        Schema::create('thesis_exam_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('thesis_exam_criteria')->cascadeOnDelete();
            $table->float('score');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['thesis_exam_id', 'lecturer_id', 'criteria_id'], 'unique_exam_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_exam_scores');
    }
};
