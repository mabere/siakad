<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('curriculum_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained()->onDelete('cascade');
            $table->date('evaluated_at');
            $table->text('notes')->nullable();
            $table->enum('status', ['satisfactory', 'needs_revision', 'critical'])->default('satisfactory');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('curriculum_evaluations');
    }
};
