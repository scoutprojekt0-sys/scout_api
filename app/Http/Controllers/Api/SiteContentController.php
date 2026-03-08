<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteContentController extends Controller
{
    public function footerPages(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => [
                'terms' => [
                    'title' => 'Kullanim Sartlari',
                    'html' => '<p>Platformu kullanirken tum kullanicilar su temel kosullari kabul eder:</p><ol><li>Paylasilan profil ve performans verileri gercek ve guncel olmali.</li><li>Hakaret, nefret soylemi veya manipule edici icerik yasaktir.</li><li>Hesap guvenligi kullanicinin sorumlulugundadir.</li><li>Kurallara aykiri durumlarda hesap gecici veya kalici askiya alinabilir.</li></ol>',
                ],
                'privacy' => [
                    'title' => 'Gizlilik Politikasi',
                    'html' => '<p>Kisisel veriler yalnizca platform isleyisi ve eslesme surecleri icin kullanilir.</p><ul><li>Veriler izinsiz ucuncu taraflarla paylasilmaz.</li><li>Profil alanlari kullanici kontrolunde guncellenebilir.</li><li>Yasal yukumluluk disinda veriler ticari olarak satilmaz.</li></ul>',
                ],
                'helpcenter' => [
                    'title' => 'Yardim Merkezi',
                    'html' => '<p>Sik sorulan konular icin hizli yardim adimlari:</p><ol><li>Hesap ve giris sorunlari</li><li>Profil dogrulama ve performans verisi guncelleme</li><li>Menajer-kulup iletisim akisinda destek</li></ol><p>Daha detayli destek icin Iletisim formundan mesaj birakabilirsin.</p>',
                ],
            ],
        ]);
    }

    public function storeContactMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc,dns', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contact = SiteContactMessage::create([
            'name' => trim((string) $validated['name']),
            'email' => trim((string) $validated['email']),
            'message' => trim((string) $validated['message']),
            'status' => 'new',
            'meta' => [
                'ip' => $request->ip(),
                'ua' => substr((string) $request->userAgent(), 0, 500),
            ],
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Mesajiniz alindi.',
            'data' => $contact,
        ], 201);
    }

    public function adminContactMessages(Request $request): JsonResponse
    {
        $status = (string) $request->query('status', '');
        $perPage = (int) $request->query('per_page', 50);
        $perPage = max(1, min($perPage, 200));

        $query = SiteContactMessage::query()->orderByDesc('created_at');
        if ($status !== '') {
            $query->where('status', $status);
        }

        $rows = $query->paginate($perPage);

        return response()->json([
            'ok' => true,
            'data' => $rows,
        ]);
    }
}

