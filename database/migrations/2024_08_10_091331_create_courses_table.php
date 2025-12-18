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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->unsignedTinyInteger('sks');
            $table->unsignedTinyInteger('semester_number')->check('semester_number BETWEEN 1 AND 8');
            $table->enum('kategori', ['Wajib', 'Pilihan', 'Tugas Akhir'])->default('Wajib');
            $table->json('prerequisites')->nullable();
            $table->string('syllabus_path')->nullable();
            $table->boolean('is_final_project')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};