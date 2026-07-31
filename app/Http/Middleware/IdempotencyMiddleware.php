<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('Idempotency-Key');

        //se cliente não pediu chave, segue normal
        if (!$idempotencyKey) {
            return $next($request);
        }

        //prepara chave identificadora para redis
        $cacheKey = 'idempotency:' . $idempotencyKey;

        //usa o remember para buscar do cache ou executar a requisição e salvar automaticamente
        $cachedData = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($next, $request) {

            //controller processa o request e tras o retorno em $response
            $response = $next($request);

            //salva no redis
            return [
                'data' => json_decode($response->getContent(), true),
                'status' => $response->getStatusCode()
            ];
        });

        //devolve resposta salva ou recuperada
        return response()->json($cachedData['data'], $cachedData['status']);
    }
}
