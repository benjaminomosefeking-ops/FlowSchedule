<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSessionId
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession()) {
            Log::info('Session ID: '.$request->session()->getId());
        }

        return $next($request);
    }
}