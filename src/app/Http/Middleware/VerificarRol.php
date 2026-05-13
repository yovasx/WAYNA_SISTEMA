<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (! $usuario) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No autenticado.',
                ], 401);
            }

            return redirect()->route('login');
        }

        if ($roles !== [] && ! $usuario->tieneRol(...$roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta accion.',
                ], 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
