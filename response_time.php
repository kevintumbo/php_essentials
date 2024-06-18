<?php

namespace App\Http\Middleware;

use Closure;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\RenderTextFormat;

class ResponseTimeMiddleware
{
    protected $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function handle($request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $end = microtime(true);
        $durationInSeconds = $end - $start;

        $histogram = $this->registry->getOrRegisterHistogram(
            'http_response_duration_seconds', // metric name
            'HTTP response duration in seconds', // metric description
            ['method', 'status_code'], // labels
            null, // buckets (optional)
            APC::class // storage (optional)
        );

        $histogram->observe($durationInSeconds, [
            'method' => $request->method(),
            'status_code' => $response->getStatusCode(),
        ]);

        return $response;
    }
}

// php artisan make:middleware ResponseTimeMiddleware

// consider redis instead of APC