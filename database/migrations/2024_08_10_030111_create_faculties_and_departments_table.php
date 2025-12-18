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
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dekan_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->index('dekan_user_id', 'faculties_dekan_user_idx');
            $table->string('nama');
            $table->string('dekan');
            $table->string('nip')->nullable()->unique();
            $table->text('visi');
            $table->text('misi');
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('code')->nullable();
            $table->string('kaprodi');
            $table->string('nip', 12)->default('0901028002')->unique();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->string('email')->nullable();
            $table->string('jenjang')->default('S-1');
            $table->string('sk')->default('SK');
            $table->date('tanggal_sk')->nullable();
            $table->string('akreditasi')->default('BAIK');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
    }
};
