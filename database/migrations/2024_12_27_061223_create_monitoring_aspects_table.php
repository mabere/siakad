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
        Schema::create('monitoring_aspects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_id')->constrained('learning_monitorings')->cascadeOnDelete();
            $table->string('aspect_name');
            $table->integer('score');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_monitoring_aspects');
    }
};