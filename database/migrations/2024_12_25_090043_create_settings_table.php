<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->string('group')->index();
            $table->json('value')->nullable(); // Use JSON for flexible value storage
            $table->timestamps();

            $table->unique(['key', 'group']);
        });

        // Insert default EDOM settings
        $defaultSettings = [
            ['key' => 'edom_active', 'group' => 'edom', 'value' => json_encode(false)],
            ['key' => 'min_respondents', 'group' => 'edom', 'value' => json_encode(1)],
            ['key' => 'min_response_percentage', 'group' => 'edom', 'value' => json_encode(50)],
            ['key' => 'allow_comments', 'group' => 'edom', 'value' => json_encode(true)],
            ['key' => 'show_lecturer_name', 'group' => 'edom', 'value' => json_encode(false)],
            ['key' => 'auto_publish_results', 'group' => 'edom', 'value' => json_encode(false)],
            ['key' => 'notification_enabled', 'group' => 'edom', 'value' => json_encode(true)],
            ['key' => 'reminder_days', 'group' => 'edom', 'value' => json_encode(7)],
            ['key' => 'submission_deadline', 'group' => 'edom', 'value' => json_encode(now()->toDateTimeString())],
        ];

        DB::table('settings')->insert($defaultSettings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};