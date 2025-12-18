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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('media');
            $table->string('media_name')->nullable();
            $table->text('abstract')->nullable();
            $table->string('issue');
            $table->year('year');
            $table->string('page')->nullable();
            $table->string('citation')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('pendanaan');
            $table->string('bukti')->nullable();
            $table->year('year');
            $table->timestamps();
        });

        Schema::create('penunjangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('peran');
            $table->string('proof')->nullable();
            $table->date('date');
            $table->string('organizer')->default('Nama Penyelenggara');
            $table->string('proof_url')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('level', ['Lokal', 'Regional', 'Nasional', 'Internasional'])->default('Nasional');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->unsignedBigInteger('validated_by')->nullable();
            $table->foreign('validated_by')->references('id')->on('users');

            $table->timestamps();
        });

        Schema::create('lecturer_publication', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('lecturer_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_service');
        Schema::dropIfExists('lecturer_publication');
        Schema::dropIfExists('penunjangs');
        Schema::dropIfExists('services');
        Schema::dropIfExists('publications');
    }
};
