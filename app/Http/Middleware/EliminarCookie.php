<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EliminarCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Eliminar cookie de la respuesta
        // Se supone que la cookie se ha insertado previamente por el backend
        // Especifica el nombre de la cookie que deseas eliminar
        if ($request->hasCookie('atemporal_token')) {
            // Eliminar la cookie de la respuesta
            Cookie::queue(Cookie::forget('atemporal_token'));
        }

        // Pasar la solicitud al siguiente middleware o controlador
        return $next($request);

    }
}
