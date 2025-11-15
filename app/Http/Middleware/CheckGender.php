<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGender
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Example: Block request if gender != male
        if ($request->gender !== 'male') {
            return response()->json(['error' => 'Access denied. Only males allowed.'], 403);
        }

        return $next($request);
    }
}
