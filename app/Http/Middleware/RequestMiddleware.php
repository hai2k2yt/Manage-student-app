<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $message = [
            'message' =>__('common.unauthorized')
        ];
        if(!$request->user()) {
            return response()->json($message, Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
