<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;


class CheckApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $user = User::where('api_token', hash('sha256', $token))->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
