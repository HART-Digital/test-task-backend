<?php

namespace App\Http\Middleware;

use Closure;
use Gate;
use Illuminate\Http\Request;

class RoleHasRouteAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();

        if (Gate::allows($routeName)) {
            return $next($request);
        }

        return response()->json(
            [
                'message' => 'У вас нет доступа к этому действию'
            ],
            403
        );
    }
}
