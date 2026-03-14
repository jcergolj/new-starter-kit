<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! User::orderBy('id')->first()->is($user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
