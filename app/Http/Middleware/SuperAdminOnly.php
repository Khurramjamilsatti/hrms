<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOnly
{
    /**
     * Handle an incoming request.
     * Only allows system administrators (super_admin role)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ($user->role !== 'super_admin' || !$user->isSuperAdmin()) {
            return response()->json([
                'message' => 'This action is restricted to system administrators only.'
            ], 403);
        }

        return $next($request);
    }
}
