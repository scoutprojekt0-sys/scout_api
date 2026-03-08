<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class LocalizationApiController extends Controller
{
    private array $supportedLocales = ['tr', 'en', 'es', 'de', 'fr', 'it', 'pt', 'ar'];

    /**
     * Get supported languages
     */
    public function getSupportedLanguages(): JsonResponse
    {
        $languages = [
            ['code' => 'tr', 'name' => 'Türkçe', 'flag' => '🇹🇷'],
            ['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧'],
            ['code' => 'es', 'name' => 'Español', 'flag' => '🇪🇸'],
            ['code' => 'de', 'name' => 'Deutsch', 'flag' => '🇩🇪'],
            ['code' => 'fr', 'name' => 'Français', 'flag' => '🇫🇷'],
            ['code' => 'it', 'name' => 'Italiano', 'flag' => '🇮🇹'],
            ['code' => 'pt', 'name' => 'Português', 'flag' => '🇵🇹'],
            ['code' => 'ar', 'name' => 'العربية', 'flag' => '🇸🇦'],
        ];

        return response()->json([
            'ok' => true,
            'data' => $languages,
        ]);
    }

    /**
     * Change user language
     */
    public function changeLanguage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'locale' => 'required|in:tr,en,es,de,fr,it,pt,ar',
        ]);

        $user = $request->user();
        if ($user) {
            $user->update(['locale' => $validated['locale']]);
        }

        App::setLocale($validated['locale']);

        return response()->json([
            'ok' => true,
            'message' => 'Dil değiştirildi',
            'locale' => $validated['locale'],
        ]);
    }

    /**
     * Get translations for current locale
     */
    public function getTranslations(Request $request): JsonResponse
    {
        $locale = $request->get('locale', 'tr');

        // Load translation file
        $path = resource_path("lang/{$locale}.json");

        if (!file_exists($path)) {
            return response()->json([
                'ok' => false,
                'message' => 'Translation file not found',
            ], 404);
        }

        $translations = json_decode(file_get_contents($path), true);

        return response()->json([
            'ok' => true,
            'locale' => $locale,
            'data' => $translations,
        ]);
    }
}
