<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSharedTesterAccountChanges
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->email === User::SHARED_TESTER_EMAIL) {
            abort(403, 'Akun tester bersama tidak dapat diubah.');
        }

        return $next($request);
    }
}
