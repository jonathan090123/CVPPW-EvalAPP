<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak. Hanya Owner yang dapat mengatur data master.');
        }

        return $next($request);
    }
}