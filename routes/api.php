<?php

use Illuminate\Support\Facades\Route;

$versionsDir = scandir(__DIR__ . "/api");

foreach ($versionsDir as $content) {

    if (preg_match('/^v\d+(\.\d+)*$/', $content) == 1) {//parou em v<num>

        $routesFiles = glob(__DIR__ . "/api/$content/*.php");//pegou todos arquivos de rota dentro de v<num>

        Route::prefix($content)->group(function () use ($routesFiles) {//libera todas as rotas com o prefixo v<num>

            foreach ($routesFiles as $filename) {

                require_once $filename;

            }

        });

    }

}
