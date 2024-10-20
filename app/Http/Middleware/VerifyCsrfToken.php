<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/pay-via-ajax', '/success','/cancel','/fail','/ipn'
    ];
    protected $except_urls = [
        '/pay-via-ajax', '/success','/cancel','/fail','/ipn'
    ];
    // public function handle($request, Closure $next)
    // {
    //     $regex = '#' . implode('|', $this->except_urls) . '#';

    //     if ($this->isReading($request) || $this->tokensMatch($request) || preg_match($regex, $request->path())) {
    //         return $this->addCookieToResponse($request, $next($request));
    //     }

    //     throw new TokenMismatchException;
    // }
    
}
