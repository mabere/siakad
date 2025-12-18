<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    public function logActivity(string $action, array $context = [])
    {
        Log::info("User {$action}", array_merge([
            'user_id' => auth()->id(),
            'model' => get_class($this),
            'model_id' => $this->id,
        ], $context));
    }
}
