// Define a route for Prometheus metrics
<?php

Route::get('/metrics', function () {
    // Retrieve the Prometheus registry
    $registry = app('prometheus_registry');
    
    // Render metrics and return as response
    return response($registry->render())->header('Content-Type', \Prometheus\RenderTextFormat::MIME_TYPE);
});

// Register this middleware in your app/Http/Kernel.php:

// php
// Copy code
// protected $middleware = [
//     // Other middleware entries
//     \App\Http\Middleware\ResponseTimeMiddleware::class,
// ];