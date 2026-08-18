<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend([
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->web(append: [
            // Livewire handles its own middleware
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'role'       => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'no-cache'   => \App\Http\Middleware\PreventBackHistoryCache::class,
        ]);

        $middleware->throttleApi('60,1');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // PHP rejects an oversized upload before any of the app's own
        // attachment validation can run, which otherwise surfaces to the user
        // as a bare 413 page. Send them back to the form with a message that
        // actually says what the limit is.
        // Note: this fires from ValidatePostSize, which runs before the session
        // is started - so the message cannot be flashed and must be rendered
        // directly into the response.
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            $perFile = ini_get('upload_max_filesize') ?: 'the server limit';
            $perRequest = ini_get('post_max_size') ?: 'the server limit';

            $message = "Your upload was too large to accept. Each file must be under {$perFile}, and everything in one submission must total less than {$perRequest}. Please upload fewer or smaller files.";

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 413);
            }

            return response()->view('errors.upload-too-large', [
                'message' => $message,
                'backUrl' => url()->previous(),
            ], 413);
        });
    })->create();
