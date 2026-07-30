<?php

use App\Enums\AppErrorType;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e) {

            $excArr = explode('\\', $e::class);
            $className = $excArr[count($excArr) - 1];

            $message = $e->getMessage();
            $status = match (true) {
                $e instanceof \Illuminate\Validation\ValidationException => 422,
                $e instanceof \Illuminate\Auth\AuthenticationException => 401,
                $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => 404,
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 404,
                $e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException => 404,
                default => 500,
            };

            // casos especiais

            // SQL: recuperar só a mensagem, sem detalhes sensíveis
            if ($className === 'QueryException') {
                $message = trim(explode('(', $message)[0]);
            }

            // RouteNotFoundException disparada pelo fluxo padrão de auth
            // (ele tenta redirecionar pra route('login'), que não existe numa API)
            if ($className === 'RouteNotFoundException' && str_contains($message, 'login')) {
                $className = 'AuthenticationException';
                $message = 'Para acessar esta rota é necessário estar autenticado.';
                $status = 401;
            }

            return response()->json([
                'type' => AppErrorType::SYSTEM->value,
                'exception' => $className,
                'error' => $message,
            ], $status);
        });
    })->create();
