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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('ta');
            $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('status')->default(false);
            $table->date('krs_open_date')->nullable();
            $table->date('krs_close_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
