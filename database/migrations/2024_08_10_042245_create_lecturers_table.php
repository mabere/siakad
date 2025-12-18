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
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('nama_dosen');
            $table->string('nidn')->nullable()->unique();
            $table->enum('jafung', ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'])->default('Lektor')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('scholar_google', 80)->nullable();
            $table->string('sinta_profile', 55)->nullable();
            $table->string('scopus_id', 55)->nullable();
            $table->string('address')->nullable();
            $table->string('telp')->nullable();
            $table->string('email')->nullable()->unique();
            $table->enum('gender', ['Laki-Laki', 'Perempuan'])->default('Laki-Laki');
            $table->string('tpl')->nullable();
            $table->date('tgl')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('head_id')->nullable()->constrained('lecturers')->cascadeOnDelete()->after('faculty_id');

        });

    }

    public function down(): void
    {
        Schema::dropIfExists('lecturers');
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['head_id']);
            $table->dropColumn('head_id');
        });
    }
};
