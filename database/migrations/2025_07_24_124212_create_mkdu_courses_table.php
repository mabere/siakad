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
        Schema::create('mkdu_courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedTinyInteger('sks');
            $table->unsignedTinyInteger('semester_number')->check('semester_number BETWEEN 1 AND 8');
            $table->string('category')->nullable()->default('Wajib');
            $table->string('syllabus_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('curriculum_mkdu', function (Blueprint $table) {
            $table->foreignId('curriculum_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mkdu_course_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('semester_number')->nullable()->check('semester_number BETWEEN 1 AND 8');
            $table->primary(['curriculum_id', 'mkdu_course_id']);
            $table->unique(['curriculum_id', 'mkdu_course_id'], 'curriculum_mkdu_unique');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('curriculum_mkdu');
        Schema::dropIfExists('mkdu_courses');
    }
};
