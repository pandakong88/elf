<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Percayai reverse proxy (Cloudflare, Nginx, Load Balancer) agar mendeteksi HTTPS dengan benar
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'payment/doku/notification',
            'api/payment/doku/notification',
            'duitku/callback',
        ]);
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Mencegah error 405 saat endpoint Livewire update diakses via GET (browser back/refresh/redirect)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, Request $request) {
            if (str_contains($request->path(), 'update') || str_contains($request->path(), 'livewire')) {
                $prev = url()->previous();
                if ($prev && !str_contains($prev, 'livewire') && !str_contains($prev, 'update')) {
                    return redirect()->to($prev);
                }
                return redirect()->to('/');
            }
        });
    })->create();
