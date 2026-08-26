<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);
        $nonce = Vite::cspNonce();

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "script-src 'self' 'unsafe-eval' 'nonce-{$nonce}' https://challenges.cloudflare.com",
            "script-src-attr 'none'",
            "style-src 'self' 'unsafe-inline' https://challenges.cloudflare.com",
            "font-src 'self' data:",
            "img-src 'self' data: blob: https://api.qrserver.com",
            "connect-src 'self' https://challenges.cloudflare.com",
            "frame-src https://challenges.cloudflare.com",
            "media-src 'self' blob:",
            "worker-src 'self' blob:",
            "manifest-src 'self'",
        ];

        if (app()->isProduction()) {
            $directives[] = 'upgrade-insecure-requests';
        }

        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', $directives)
        );

        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        return $response;
    }
}