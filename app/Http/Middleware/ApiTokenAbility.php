<?php

namespace App\Http\Middleware;

use App\Models\ApplicationApi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiTokenAbility
{
    public function handle(Request $request, Closure $next, string $ability)
    {
        /** @var ApplicationApi|null $token */
        $token = $request->attributes->get('application_api_token');
        if (!$token instanceof ApplicationApi) {
            return response()->json(['message' => 'Invalid Authorization token'], 401);
        }

        if (!$token->allowsAbility($ability)) {
            Log::warning('API token denied due to missing scope.', [
                'token_fingerprint' => $this->fingerprintToken($token->token),
                'ability' => $ability,
                'method' => $request->method(),
                'route' => optional($request->route())->getName(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Forbidden. Missing required API token ability.',
                'required_ability' => $ability,
            ], 403);
        }

        if ($request->method() !== 'GET') {
            Log::info('API token mutation authorized.', [
                'token_fingerprint' => $this->fingerprintToken($token->token),
                'ability' => $ability,
                'method' => $request->method(),
                'route' => optional($request->route())->getName(),
                'ip' => $request->ip(),
            ]);
        }

        return $next($request);
    }

    private function fingerprintToken(string $token): string
    {
        return substr(hash('sha256', $token), 0, 16);
    }
}
