<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * A middleware which ensures that a JSON response is returned if the incoming HTTP
 * request expects responses of the content type "application/json".
 */
class JsonifyResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->expectsJson() && !($response instanceof JsonResponse)) {
            return response()
                    ->json(
                        [ 'displayMessage' => $response->content() ],
                        $response->status(),
                        $response->headers->all()
                    );
        }

        return $response;
    }
}
