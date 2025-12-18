<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('thesis_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained()->nullable()->default(1);
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order');
            $table->dateTime('deadline')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->dateTime('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_milestones');
    }
};