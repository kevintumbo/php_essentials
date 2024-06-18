// Example using php-prometheus-client
<?php

use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\RenderTextFormat;

// Initialize a registry using APC for storage
$registry = new CollectorRegistry(new APC());

// Define a counter metric for HTTP request duration
$requestDuration = $registry->registerHistogram(
    'http_request_duration_seconds', // metric name
    'HTTP request duration in seconds', // metric description
    ['method', 'route', 'status_code'] // labels
);

// In your request handler, record the duration
$start = microtime(true);

// Your application logic here...

$end = microtime(true);
$duration = $end - $start;

// Record the duration into the histogram
$requestDuration->observe($duration, [
    'method' => $request->method(),
    'route' => $request->route()->uri(),
    'status_code' => $response->status(),
]);

// Expose metrics endpoint
Route::get('/metrics', function () use ($registry) {
    header('Content-Type: ' . RenderTextFormat::MIME_TYPE);
    echo $registry->render();
});


// http_request_duration_seconds is a histogram metric that tracks HTTP request durations.
// The /metrics route exposes Prometheus-compatible metrics.
// Ensure Prometheus is configured to scrape this endpoint (/metrics) to collect and store these metrics.

// consider redis instead of APC