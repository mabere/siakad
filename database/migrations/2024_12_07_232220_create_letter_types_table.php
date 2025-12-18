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
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('required_fields')->nullable();
            $table->boolean('needs_approval')->default(true);
            $table->enum('level', ['university', 'faculty', 'department'])->default('university');
            $table->integer('processing_time')->nullable();
            $table->json('metadata')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->boolean('for_dosen')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_types');

    }
};