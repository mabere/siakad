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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('units');
            $table->foreignId('faculty_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('code')->unique();
            $table->string('nip_kepala')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('level', ['Universitas', 'Fackultas', 'Program Studi', 'Lainnya']);
            $table->string('signature_path')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
