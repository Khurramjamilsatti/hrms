<?php

namespace App\Http\Middleware;

use App\Models\CmsUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCmsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user instanceof CmsUser) {
            return response()->json([
                'message' => 'CMS authentication required.',
            ], 401);
        }

        if (!$user->is_active || !$user->canManageContent()) {
            return response()->json([
                'message' => 'CMS account is not allowed to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
