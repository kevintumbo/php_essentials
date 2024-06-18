<?php

namespace App\Http\Middleware;

use Closure;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\RenderTextFormat;

class ThroughputMiddleware
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
            ['method', 'status_code'] // labels
        );

        $counter->inc([
            'method' => $request->method(),
            'status_code' => $response->getStatusCode(),
        ]);

        return $response;
    }
}

// php artisan make:middleware ThroughputMiddleware