<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->plan_id && ! $request->routeIs('checkout.*') && ! $request->routeIs('stripe.*') && ! $request->routeIs('logout')) {
            return redirect()->route('checkout.redirect');
        }

        if ($user && ! $user->onboarding_completed && ! $request->routeIs('onboarding.*') && ! $request->routeIs('checkout.*') && ! $request->routeIs('stripe.*') && ! $request->routeIs('logout')) {
            return redirect()->route('onboarding.step', 1);
        }

        return $next($request);
    }
}
