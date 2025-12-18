<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class EdomActive
{
    public function handle(Request $request, Closure $next)
    {
        if (!Setting::isEdomActive()) {
            return redirect()->route('admin.edom.questionnaire.index')
                ->with('error', 'EDOM sedang tidak aktif');
        }

        return $next($request);
    }
}