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
        Schema::create('learning_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitor_id')->constrained('users')->cascadeOnDelete();
            $table->integer('meeting_number');
            $table->date('monitoring_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('attendance_count');
            $table->boolean('material_conformity');
            $table->string('learning_method');
            $table->string('media_used');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified', 'revised', 'completed']);
            $table->text('verification_notes')->nullable();
            $table->text('revision_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_monitorings');
    }
};