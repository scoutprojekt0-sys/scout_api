<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    public function publicIndex(Request $request): JsonResponse
    {
        $query = Lawyer::query()->with('user');

        if ($request->has('specialization')) {
            $query->where('specialization', $request->input('specialization'));
        }

        if ($request->boolean('verified_only')) {
            $query->where('is_verified', true);
        }

        if ($request->has('min_experience')) {
            $query->where('years_experience', '>=', $request->input('min_experience'));
        }

        $lawyers = $query
            ->where('is_active', true)
            ->orderByDesc('id')
            ->paginate(30);

        return response()->json([
            'ok' => true,
            'data' => $lawyers,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Lawyer::query()->with('user');

        if ($request->has('specialization')) {
            $query->where('specialization', $request->input('specialization'));
        }

        if ($request->boolean('verified_only')) {
            $query->where('is_verified', true);
        }

        if ($request->has('min_experience')) {
            $query->where('years_experience', '>=', $request->input('min_experience'));
        }

        $lawyers = $query->where('is_active', true)
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $lawyers,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_number' => ['required', 'unique:lawyers', 'string', 'max:100'],
            'specialization' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'office_name' => ['nullable', 'string', 'max:150'],
            'office_address' => ['nullable', 'string', 'max:255'],
            'office_phone' => ['nullable', 'string', 'max:30'],
            'office_email' => ['nullable', 'email', 'max:120'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'contract_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        $lawyer = Lawyer::create([
            'user_id' => $request->user()->id,
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Avukat profili oluşturuldu. Doğrulama bekliyor.',
            'data' => $lawyer,
        ], 201);
    }

    public function show(int $lawyerId): JsonResponse
    {
        $lawyer = Lawyer::with(['user', 'contracts', 'reviews'])
            ->findOrFail($lawyerId);

        return response()->json([
            'ok' => true,
            'data' => $lawyer,
        ]);
    }

    public function update(Request $request, int $lawyerId): JsonResponse
    {
        $lawyer = Lawyer::findOrFail($lawyerId);

        if ($lawyer->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'bio' => ['nullable', 'string'],
            'office_name' => ['nullable', 'string', 'max:150'],
            'office_address' => ['nullable', 'string'],
            'office_phone' => ['nullable', 'string', 'max:30'],
            'office_email' => ['nullable', 'email'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'contract_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        $lawyer->update($validated);

        return response()->json([
            'ok' => true,
            'message' => 'Profil güncellendi.',
            'data' => $lawyer,
        ]);
    }
}
