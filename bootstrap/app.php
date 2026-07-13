<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(except: [
            '/logout',
            'api/logout'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            \Illuminate\Support\Facades\Log::warning('[DEBUG] 401 AuthenticationException — auth:sanctum gagal', [
                'url'                      => $request->fullUrl(),
                'method'                   => $request->method(),
                'request_host'             => $request->getHost(),
                'request_origin'           => $request->header('Origin'),
                'has_authorization_header' => $request->hasHeader('Authorization'),
                'cookies_received'         => array_keys($request->cookies->all()),
                'session_id'               => $request->session()->getId(),
                'session_has_data'         => !empty($request->session()->all()),
                'guard_web_check'          => \Illuminate\Support\Facades\Auth::guard('web')->check(),
                'sanctum_stateful_domains' => config('sanctum.stateful'),
                'iam_enabled'              => config('iam.enabled'),
                'exception_message'        => $e->getMessage(),
                'guards'                   => $e->guards(),
            ]);
            // Biarkan Laravel yang render response aslinya
        });

        $exceptions->shouldRenderJsonWhen(function (\Illuminate\Http\Request $request, \Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });
    })->create();