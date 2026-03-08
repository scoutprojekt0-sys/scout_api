<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegalServicesController extends Controller
{
    /**
     * Yasal hizmetler listesi
     */
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('legal_services')
            ->join('lawyers', 'legal_services.lawyer_id', '=', 'lawyers.id')
            ->join('users', 'lawyers.user_id', '=', 'users.id')
            ->where('legal_services.is_active', true)
            ->select('legal_services.*', 'lawyers.specialization', 'users.name as lawyer_name');

        if ($request->has('type')) {
            $query->where('legal_services.service_type', $request->type);
        }

        if ($request->has('specialization')) {
            $query->where('lawyers.specialization', $request->specialization);
        }

        $services = $query->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $services,
        ]);
    }

    /**
     * Popüler yasal hizmetler
     */
    public function popular(): JsonResponse
    {
        $popular = DB::table('legal_services')
            ->where('is_active', true)
            ->orderByDesc('views_count')
            ->orderByDesc('bookings_count')
            ->limit(10)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $popular,
        ]);
    }

    /**
     * Transfer sözleşmeleri
     */
    public function transferContracts(Request $request): JsonResponse
    {
        $contracts = DB::table('legal_contracts')
            ->where('contract_type', 'transfer')
            ->where('is_active', true)
            ->select('*')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $contracts,
        ]);
    }

    /**
     * Sponsorluk sözleşmeleri
     */
    public function sponsorshipContracts(Request $request): JsonResponse
    {
        $contracts = DB::table('legal_contracts')
            ->where('contract_type', 'sponsorship')
            ->where('is_active', true)
            ->select('*')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $contracts,
        ]);
    }

    /**
     * İş hukuku danışmanlığı
     */
    public function laborConsultation(Request $request): JsonResponse
    {
        $consultations = DB::table('legal_consultations')
            ->where('consultation_type', 'labor')
            ->where('is_active', true)
            ->select('*')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $consultations,
        ]);
    }

    /**
     * Veraset hukuku danışmanlığı
     */
    public function inheritanceConsultation(Request $request): JsonResponse
    {
        $consultations = DB::table('legal_consultations')
            ->where('consultation_type', 'inheritance')
            ->where('is_active', true)
            ->select('*')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $consultations,
        ]);
    }

    /**
     * Vergi danışmanlığı
     */
    public function taxConsultation(Request $request): JsonResponse
    {
        $consultations = DB::table('legal_consultations')
            ->where('consultation_type', 'tax')
            ->where('is_active', true)
            ->select('*')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $consultations,
        ]);
    }

    /**
     * Avukat profili detayları
     */
    public function lawyerDetails(int $lawyerId): JsonResponse
    {
        $lawyer = DB::table('lawyers')
            ->join('users', 'lawyers.user_id', '=', 'users.id')
            ->where('lawyers.id', $lawyerId)
            ->where('lawyers.is_active', true)
            ->select('lawyers.*', 'users.name', 'users.email', 'users.avatar')
            ->first();

        if (!$lawyer) {
            return response()->json(['ok' => false, 'message' => 'Avukat bulunamadı'], 404);
        }

        // Get lawyer's services
        $services = DB::table('legal_services')
            ->where('lawyer_id', $lawyerId)
            ->where('is_active', true)
            ->get();

        // Get lawyer's reviews
        $reviews = DB::table('legal_reviews')
            ->where('lawyer_id', $lawyerId)
            ->where('is_approved', true)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => [
                'lawyer' => $lawyer,
                'services' => $services,
                'reviews' => $reviews,
            ],
        ]);
    }

    /**
     * Hizmet talebinde bulun
     */
    public function requestService(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'service_type' => 'required|in:contract,consultation,review',
            'description' => 'required|string|max:2000',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
        ]);

        $requestId = DB::table('legal_service_requests')->insertGetId(array_merge($validated, [
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return response()->json([
            'ok' => true,
            'message' => 'Talep oluşturuldu',
            'data' => ['id' => $requestId],
        ], 201);
    }

    /**
     * Yasal belge şablonları
     */
    public function documentTemplates(): JsonResponse
    {
        $templates = DB::table('legal_document_templates')
            ->where('is_active', true)
            ->select('id', 'name', 'category', 'description', 'price')
            ->orderBy('category')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $templates,
        ]);
    }

    /**
     * Başarılı davaları listele
     */
    public function successCases(): JsonResponse
    {
        $cases = DB::table('legal_success_cases')
            ->where('is_published', true)
            ->select('id', 'title', 'description', 'outcome', 'year', 'lawyer_id')
            ->orderByDesc('year')
            ->limit(15)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $cases,
        ]);
    }
}
