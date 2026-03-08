<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\LanguageTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    // Tüm ülkeleri listele
    public function countries(Request $request): JsonResponse
    {
        $query = Country::query();

        if ($request->has('region')) {
            $query->where('region', $request->input('region'));
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        $countries = $query->latest()
            ->paginate(50);

        return response()->json([
            'ok' => true,
            'data' => $countries,
        ]);
    }

    // Belirli ülkeyi getir
    public function getCountry(string $countryCode): JsonResponse
    {
        $country = Country::where('code', strtoupper($countryCode))
            ->with([
                'legalRequirements',
                'sportRules',
                'localizedProfessionals',
            ])
            ->firstOrFail();

        return response()->json([
            'ok' => true,
            'data' => $country,
        ]);
    }

    // Kullanıcının lokalisasyon ayarlarını güncelle
    public function setUserLocalization(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'language' => ['required', 'string', 'max:10'],
            'currency_code' => ['required', 'string', 'max:3'],
            'timezone' => ['nullable', 'string'],
            'time_format' => ['nullable', 'in:12h,24h'],
            'date_format' => ['nullable', 'in:DD/MM/YYYY,MM/DD/YYYY,YYYY-MM-DD'],
            'height_unit' => ['nullable', 'in:cm,ft'],
            'weight_unit' => ['nullable', 'in:kg,lbs'],
        ]);

        $localization = $request->user()->localizationSettings()
            ->updateOrCreate(
                ['user_id' => $request->user()->id],
                $validated
            );

        return response()->json([
            'ok' => true,
            'message' => 'Lokalisasyon ayarları kaydedildi.',
            'data' => $localization,
        ]);
    }

    // Kullanıcının lokalisasyon ayarlarını getir
    public function getUserLocalization(Request $request): JsonResponse
    {
        $localization = $request->user()->localizationSettings ??
            $request->user()->localizationSettings()->create([
                'language' => 'tr',
                'currency_code' => 'TRY',
                'country_id' => 1, // Türkiye
            ]);

        return response()->json([
            'ok' => true,
            'data' => $localization,
        ]);
    }

    // Çeviriler al
    public function getTranslations(string $language, ?string $category = null): JsonResponse
    {
        $query = LanguageTranslation::where('language_code', $language);

        if ($category) {
            $query->where('category', $category);
        }

        $translations = $query->get()->pluck('value', 'key');

        return response()->json([
            'ok' => true,
            'language' => $language,
            'category' => $category,
            'data' => $translations,
        ]);
    }

    // Para birimi dönüştür
    public function convertCurrency(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'from_currency' => ['required', 'string', 'max:3'],
            'to_currency' => ['required', 'string', 'max:3'],
        ]);

        // Basit dönüşüm (gerçek uygulamada API kullan)
        $currencies = [
            'TRY' => 1,
            'USD' => 33.50,
            'EUR' => 36.50,
            'GBP' => 42.00,
            'JPY' => 0.225,
            'CHF' => 37.50,
            'CAD' => 24.50,
            'AUD' => 22.00,
            'INR' => 0.40,
            'BRL' => 6.80,
            'MXN' => 1.96,
            'SGD' => 24.80,
            'HKD' => 4.28,
            'CNY' => 4.62,
            'KRW' => 0.0254,
        ];

        $fromRate = $currencies[$validated['from_currency']] ?? 1;
        $toRate = $currencies[$validated['to_currency']] ?? 1;

        $convertedAmount = ($validated['amount'] / $fromRate) * $toRate;

        return response()->json([
            'ok' => true,
            'amount' => $validated['amount'],
            'from_currency' => $validated['from_currency'],
            'to_currency' => $validated['to_currency'],
            'converted_amount' => round($convertedAmount, 2),
            'rate' => round(($toRate / $fromRate), 6),
        ]);
    }

    // Bölgeleri getir
    public function getRegions(): JsonResponse
    {
        $regions = Country::distinct()
            ->pluck('region')
            ->sort()
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $regions,
        ]);
    }

    // Ülkeleri bölgeye göre getir
    public function getCountriesByRegion(string $region): JsonResponse
    {
        $countries = Country::where('region', $region)
            ->where('is_active', true)
            ->get(['id', 'code', 'name', 'currency_code', 'timezone']);

        return response()->json([
            'ok' => true,
            'region' => $region,
            'data' => $countries,
        ]);
    }
}
