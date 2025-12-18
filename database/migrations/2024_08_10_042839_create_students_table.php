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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('nama_mhs');
            $table->string('nim')->nullable()->unique();
            $table->foreignId('kelas_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('advisor_id')->nullable()->constrained('lecturers')->cascadeOnDelete();
            $table->year('entry_year')->default('2024');
            $table->string('entry_semester')->default('1');
            $table->enum('kategori', ['Baru', 'Pindahan'])->default('Baru');
            $table->string('address')->nullable();
            $table->string('telp')->nullable();
            $table->string('email')->nullable();
            $table->enum('gender', ['Laki-Laki', 'Perempuan'])->default('Laki-Laki');
            $table->string('tpl')->nullable();
            $table->date('tgl')->nullable();
            $table->integer('total_sks')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};