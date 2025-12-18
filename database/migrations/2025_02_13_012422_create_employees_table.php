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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('email')->nullable()->unique();
            $table->enum('position', ['KTU', 'Feeder', 'Admin'])->default('Feeder');
            $table->enum('level', ['department', 'faculty'])->default('department');
            $table->enum('gender', ['Laki-Laki', 'Perempuan'])->default('Laki-Laki');
            $table->string('nip')->nullable()->unique();
            $table->string('tpl')->nullable();
            $table->date('tgl')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
