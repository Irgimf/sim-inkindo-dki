<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SecurityHeadersFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        //
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->setHeader('X-XSS-Protection', '1; mode=block');

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com https://cdn.datatables.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net",
            "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net",
            "img-src 'self' data: blob: https: http:",
            "connect-src 'self' https://cdn.jsdelivr.net",
            "frame-src 'self' blob: data:",
            "object-src 'self' blob:",
            "frame-ancestors 'self'",
            "base-uri 'self'",
        ]);
        $response->setHeader('Content-Security-Policy', $csp);

        return $response;
    }
}
