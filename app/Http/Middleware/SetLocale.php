<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['tr', 'en', 'de', 'es']);
        $fallback = config('app.fallback_locale', 'en');

        $locale = $request->query('lang');
        if (is_string($locale) && in_array($locale, $supported, true)) {
            $request->session()->put('locale', $locale);
        } else {
            $locale = $request->session()->get('locale');
        }

        if (!is_string($locale) || !in_array($locale, $supported, true)) {
            $preferred = $request->getPreferredLanguage($supported);
            $locale = is_string($preferred) ? $preferred : $fallback;
        }

        App::setLocale($locale);

        return $next($request);
    }
}
