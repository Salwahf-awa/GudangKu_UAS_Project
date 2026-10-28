<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanitasi input (lapisan pertahanan XSS ke-1):
 * membuang tag HTML/script dari semua input teks sebelum divalidasi.
 * Lapisan ke-2: Blade {{ }} otomatis meng-escape output saat ditampilkan.
 * Field password dilewati agar karakter khusus pada password tidak rusak.
 */
class SanitizeInput
{
    public function handle(Request $request, Closure $next): Response
    {
        $clean = $this->clean($request->all());
        $request->merge($clean);

        return $next($request);
    }

    private function clean(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($key) && str_contains(strtolower($key), 'password')) {
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->clean($value);
            } elseif (is_string($value)) {
                $data[$key] = trim(strip_tags($value));
            }
        }

        return $data;
    }
}
