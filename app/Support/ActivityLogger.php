<?php

namespace App\Support;

use App\Models\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, string $description, ?Model $subject = null, array $properties = []): void
    {
        $request = app()->bound('request') ? app(Request::class) : null;

        Log::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent() ? substr((string) $request->userAgent(), 0, 2000) : null,
            'properties' => $properties ?: null,
        ]);
    }
}
