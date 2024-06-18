<?php

namespace App\Http\Middleware;

use Closure;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\RenderTextFormat;

class TrafficMiddleware
{
    protected $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $counter = $this->registry->getOrRegisterCounter(
            'http_requests_total', // metric name
            'Total number of HTTP requests', // metric description
            ['method', 'route'] // labels
        );

        $counter->inc([
            'method' => $request->method(),
            'route' => $request->route()->uri(),
        ]);

        return $response;
    }
}

// php artisan make:middleware TrafficMiddleware

// Register this middleware in your app/Http/Kernel.php:

// php
// Copy code
// protected $middleware = [
//     // Other middleware entries
//     \App\Http\Middleware\TrafficMiddleware::class,
// ];