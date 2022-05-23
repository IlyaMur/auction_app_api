<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response =  $next($request);

        // Check if debugbar is enabled
        if (!app()->bound('debugbar') || !app('debugbar')->isEnabled()) {
            return $response;
        }

        // Profile the json reponse
        if ($response instanceof JsonResponse && $request->has('_debug')) {
            $response->setData(array_merge($response->getData(true), [
                '_debugbar' => Arr::only(app('debugbar')->getData(true), "queries")
            ]));
        }

        return $response;
    }
}
