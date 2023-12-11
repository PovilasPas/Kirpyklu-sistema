<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\JsonException;

class CheckIfJsonIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            $content = $request->getContent();
            if ($content) {
                json_decode($content, false, 512, JSON_THROW_ON_ERROR);
            }
        } catch (\JsonException $exception) {
            return response(['message' => 'invalid JSON format'], 400);
        }

        return $next($request);
    }
}
