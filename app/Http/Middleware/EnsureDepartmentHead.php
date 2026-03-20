<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDepartmentHead
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($user->account_type === 'admin') {
            return $next($request);
        }

        if ($user->account_type === 'faculty') {
            $position = $user->facultyPosition;

            if ($position && strtoupper($position->code) === 'DEPARTMENT_HEAD') {
                return $next($request);
            }
        }

        abort(403);
    }
}
