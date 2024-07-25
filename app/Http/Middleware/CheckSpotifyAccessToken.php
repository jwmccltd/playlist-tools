<?php

namespace App\Http\Middleware;

use Auth;
use App\Services\SpotifyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSpotifyAccessToken
{
    public function __construct(protected SpotifyService $spotifyService)
    {
        // Constructor.
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (!$this->spotifyService->getSetAccessToken()) {
                Auth::logout();
                return redirect()->route('connect');
            }
        }

        return $next($request);
    }
}
