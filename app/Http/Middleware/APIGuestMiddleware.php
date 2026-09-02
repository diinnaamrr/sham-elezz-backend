<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIGuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $this->extractBearerToken($request);

        if ($token && $this->isPlausibleJwt($token)) {
            try {
                $user = auth('api')->user();
                if ($user) {
                    $request->merge(['user' => $user]);
                    return $next($request);
                }
            } catch (\Throwable $e) {
                // Invalid or expired token — fall through to guest handling.
            }
        }

        if ($request->guest_id) {
            return $next($request);
        }

        return response()->json(['errors' => 'Unauthorized'], 401);
    }

    protected function extractBearerToken(Request $request): ?string
    {
        $auth = $request->header('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($auth, 7));

        return $token !== '' ? $token : null;
    }

    protected function isPlausibleJwt(string $token): bool
    {
        if (in_array(strtolower($token), ['null', 'undefined', 'none'], true)) {
            return false;
        }

        return substr_count($token, '.') === 2;
    }
}
