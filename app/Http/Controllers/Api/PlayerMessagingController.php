<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerMessage;
use App\Models\AnonymousNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerMessagingController extends Controller
{
    // Direkt mesaj gönder (Anonim seçeneği ile)
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'to_user_id' => ['required', 'exists:users,id'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
            'is_anonymous' => ['boolean'],
            'anonymous_name' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:direct_message,inquiry,offer,feedback'],
        ]);

        $message = PlayerMessage::create([
            'from_user_id' => $request->user()->id,
            ...$validated,
        ]);

        // Eğer anonim mesaj ise, hint bilgileri ekle
        if ($validated['is_anonymous']) {
            AnonymousNotification::create([
                'player_user_id' => $validated['to_user_id'],
                'triggered_by_user_id' => $request->user()->id,
                'notification_type' => 'anonymous_message',
                'message' => '💌 Sana gizli bir mesaj geldi! Kimden olduğunu bilemezsin... Merak mı ediyor musun?',
                'emoji' => '💌',
                'hint' => 'Birisinden sana özel bir mesaj',
                'is_mystery' => true,
                'mystery_level' => 5,
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj gönderildi.',
            'data' => $message,
        ], 201);
    }

    // Mesaj kutusunu getir
    public function inbox(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $unreadOnly = $request->boolean('unread_only', false);

        $query = PlayerMessage::where('to_user_id', $request->user()->id)
            ->where('archived_by_recipient', false)
            ->when($unreadOnly, fn($q) => $q->where('is_read', false));

        $messages = $query->latest('created_at')->paginate($perPage);

        return response()->json([
            'ok' => true,
            'unread_count' => PlayerMessage::where('to_user_id', $request->user()->id)
                ->where('is_read', false)
                ->count(),
            'data' => $messages->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'from' => [
                        'name' => $msg->getSenderNameAttribute(),
                        'is_anonymous' => $msg->is_anonymous,
                    ],
                    'subject' => $msg->subject,
                    'message' => $msg->message,
                    'type' => $msg->type,
                    'is_read' => $msg->is_read,
                    'received_at' => $msg->created_at,
                ];
            }),
        ]);
    }

    // Mesajı oku
    public function readMessage(int $messageId, Request $request): JsonResponse
    {
        $message = PlayerMessage::findOrFail($messageId);

        if ($message->to_user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu mesajı okuma yetkiniz yok.',
            ], 403);
        }

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj okundu olarak işaretlendi.',
            'data' => $message,
        ]);
    }

    // Tüm mesajları okundu işaretle
    public function markAllAsRead(Request $request): JsonResponse
    {
        PlayerMessage::where('to_user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'ok' => true,
            'message' => 'Tüm mesajlar okundu olarak işaretlendi.',
        ]);
    }

    // Gönderilen mesajlar
    public function sent(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);

        $messages = PlayerMessage::where('from_user_id', $request->user()->id)
            ->where('archived_by_sender', false)
            ->latest('created_at')
            ->paginate($perPage);

        return response()->json([
            'ok' => true,
            'data' => $messages,
        ]);
    }

    // Mesajı arşivle
    public function archiveMessage(int $messageId, Request $request): JsonResponse
    {
        $message = PlayerMessage::findOrFail($messageId);

        if ($message->to_user_id === $request->user()->id) {
            $message->update(['archived_by_recipient' => true]);
        } elseif ($message->from_user_id === $request->user()->id) {
            $message->update(['archived_by_sender' => true]);
        } else {
            return response()->json([
                'ok' => false,
                'message' => 'Bu işlemi yapma yetkiniz yok.',
            ], 403);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj arşivlendi.',
        ]);
    }
}
