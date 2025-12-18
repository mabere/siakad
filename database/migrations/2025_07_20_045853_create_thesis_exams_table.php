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
        Schema::create('thesis_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained()->cascadeOnDelete();
            $table->foreignId('thesis_exam_criteria_id')->nullable()->constrained('thesis_exam_criteria')->cascadeOnDelete();
            $table->foreignId('chairman_id')->nullable()->constrained('lecturers')->cascadeOnDelete(); // ketua panitia
            $table->foreignId('secretary_id')->nullable()->constrained('lecturers')->cascadeOnDelete(); // sekretaris panitia
            $table->dateTime('scheduled_at')->nullable();
            $table->string('location')->nullable();
            $table->enum('exam_type', ['proposal', 'hasil', 'skripsi'])->default('proposal');
            $table->enum('status', [
                'diajukan',
                'revisi',
                'terverifikasi',
                'penguji_ditetapkan',
                'revisi_dekan',
                'disetujui_dekan',
                'dijadwalkan',
                'pelaksanaan',
                'selesai',
                'lulus',
                'lulus_revisi',
                'ditolak'
            ])->default('diajukan');
            $table->text('revisi_notes')->nullable();
            $table->float('final_score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_exams');
    }
};