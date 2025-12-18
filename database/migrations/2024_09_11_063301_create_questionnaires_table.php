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
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('period_id')->nullable()->constrained('academic_years')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['GENERAL', 'EDOM'])->default('GENERAL');
            $table->enum('status', ['DRAFT', 'ACTIVE', 'INACTIVE'])->default('DRAFT');
            $table->text('description')->default('Keterangan Survei');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};