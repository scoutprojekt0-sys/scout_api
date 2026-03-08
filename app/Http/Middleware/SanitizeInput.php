<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * @var array<string>
     */
    private array $exceptKeys = [
        'password',
        'password_confirmation',
        'current_password',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethodSafe()) {
            $request->merge($this->sanitizeArray($request->all()));
        }

        return $next($request);
    }

    /**
     * @param array<mixed> $data
     * @return array<mixed>
     */
    private function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array((string) $key, $this->exceptKeys, true)) {
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
                continue;
            }

            if (is_string($value)) {
                $data[$key] = trim(strip_tags($value));
            }
        }

        return $data;
    }
}
