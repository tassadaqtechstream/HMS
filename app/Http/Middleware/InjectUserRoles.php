<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class InjectUserRoles
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $userRole = auth()->user()->getRoleNames();
            Inertia::share([
                'auth' => [
                    'user' => [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'roles' => $userRole// ✅ Inject roles
                    ],
                ],
            ]);
        }

        return $next($request);
    }
}
