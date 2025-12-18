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
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->unique()->cascadeOnDelete();
            $table->unsignedInteger('graduation_year'); // Tahun lulus
            $table->string('job_title')->nullable(); // Jabatan pekerjaan (opsional)
            $table->string('company')->nullable(); // Nama perusahaan (opsional)
            $table->string('industry')->nullable(); // Sektor industri (opsional)
            $table->string('salary_range')->nullable(); // Rentang gaji (opsional)
            $table->string('further_education')->nullable(); // Pendidikan lanjutan (opsional)
            $table->text('contribution')->nullable(); // Kontribusi alumni (opsional)
            $table->timestamp('last_updated')->nullable(); // Waktu update terakhir
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif'); // Status alumni
            $table->enum('visibility', ['public', 'internal', 'private'])->default('internal'); // Visibilitas data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};