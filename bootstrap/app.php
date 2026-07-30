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
        $exceptions->render(function(Throwable $e){


            $excArr = explode('\\', $e::class);
            $className = $excArr[count($excArr) - 1];

            $message = $e->getMessage();

            //casos especiais

            //caso a exceção for de SQL recuperar so mensagem e não detalhes pois há dados sensíveis
            if ($className === "QueryException") {
                $message = trim(explode("(", $message)[0]);
            }

            //caso não tenha se autenticado
            if (strpos($message, 'login')) {
                $message = "Para acessar esta rota é necessário estar autenticado.";
            }

            return response()->json(
                [
                    'type' => AppErrorType::SYSTEM->value,
                    'exception' => $className,
                    'error' => $message,
                ],
                match (true) {
                    $e instanceof \Illuminate\Validation\ValidationException => 422,
                    $e instanceof \Illuminate\Auth\AuthenticationException => 401,
                    $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => 404,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 404,
                    $e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException => 404,
                    default => 500,
                }
            );
        });
    })->create();
