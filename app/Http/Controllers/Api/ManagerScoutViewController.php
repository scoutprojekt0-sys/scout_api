<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManagerScoutView;
use App\Models\AnonymousNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManagerScoutViewController extends Controller
{
    // Menajerin profile bakışını kaydet (Anonim bildirim gönder)
    public function recordProfileView(int $playerUserId, Request $request): JsonResponse
    {
        $managerId = $request->user()->id;

        // Bakış kaydı oluştur
        $view = ManagerScoutView::create([
            'player_user_id' => $playerUserId,
            'manager_scout_id' => $managerId,
            'view_time' => now(),
            'view_type' => 'profile_view',
            'is_anonymous' => true,
            'viewer_display_name' => 'Scout ⭐',
        ]);

        // Anonim bildirim gönder
        AnonymousNotification::create([
            'player_user_id' => $playerUserId,
            'triggered_by_user_id' => $managerId,
            'notification_type' => 'anonymous_profile_view',
            'message' => '👀 Birisi senin profilini inceliyor! Kimdir acaba?',
            'emoji' => '👀',
            'hint_location' => $this->generateRandomHint(),
            'is_mystery' => true,
            'mystery_level' => 3,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Bildirim gönderildi.',
        ]);
    }

    // Anonim bildirimler
    public function getAnonymousNotifications(Request $request): JsonResponse
    {
        $notifications = AnonymousNotification::where('player_user_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'unread_count' => AnonymousNotification::where('player_user_id', $request->user()->id)
                ->where('is_read', false)
                ->count(),
            'data' => $notifications->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->notification_type,
                    'message' => $notif->message,
                    'emoji' => $notif->emoji,
                    'hint' => $notif->hint,
                    'is_mystery' => $notif->is_mystery,
                    'mystery_level' => $notif->mystery_level,
                    'is_read' => $notif->is_read,
                    'created_at' => $notif->created_at,
                ];
            }),
        ]);
    }

    // Anonim bildirimi oku
    public function readAnonymousNotification(int $notificationId, Request $request): JsonResponse
    {
        $notification = AnonymousNotification::findOrFail($notificationId);

        if ($notification->player_user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu bildirimi okuma yetkiniz yok.',
            ], 403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Bildirim okundu.',
        ]);
    }

    // Menajerin bakışlarını getir (Kendi menajeri tarafından)
    public function myViewsHistory(Request $request): JsonResponse
    {
        $views = ManagerScoutView::where('manager_scout_id', $request->user()->id)
            ->with(['player.playerProfile'])
            ->latest('view_time')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $views,
        ]);
    }

    // Gizli ilgi bildirimi oluştur (İleri seviye)
    public function sendSecretInterestNotification(int $playerUserId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
            'icon' => ['required', 'in:👀,💌,⭐,🎯,🚀,❓'],
            'mystery_level' => ['integer', 'min:1', 'max:5'],
        ]);

        \App\Models\SecretInterestNotification::create([
            'player_user_id' => $playerUserId,
            'triggered_by_user_id' => $request->user()->id,
            ...$validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Gizli ilgi bildirimi gönderildi.',
        ], 201);
    }

    // Gizli ilgi bildirimlerini al
    public function getSecretInterests(Request $request): JsonResponse
    {
        $notifications = \App\Models\SecretInterestNotification::where('player_user_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $notifications,
        ]);
    }

    private function generateRandomHint(): string
    {
        $hints = [
            'Avrupa\'dan birisi',
            'Türkiye\'nin büyük şehirlerinden',
            'Uluslararası bir scout',
            'Profesyonel bir kulüpten',
            'Tanınmış bir menajerden',
            'Gizli bir yetenek avcısı',
            'Başarılı bir ekibin göz açısı',
        ];

        return $hints[array_rand($hints)];
    }
}
