<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatMessageRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerChatController extends Controller
{
    // Chat odası oluştur (Direkt mesaj)
    public function createDirectChat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'other_user_id' => ['required', 'exists:users,id'],
        ]);

        $userId = $request->user()->id;
        $otherUserId = $validated['other_user_id'];

        // Zaten mevcut oda var mı kontrol et
        $existingRoom = PlayerChatRoom::where('type', 'direct')
            ->whereJsonContains('participant_ids', $userId)
            ->whereJsonContains('participant_ids', $otherUserId)
            ->first();

        if ($existingRoom) {
            return response()->json([
                'ok' => true,
                'message' => 'Chat odası zaten mevcut.',
                'data' => $existingRoom,
            ]);
        }

        $room = PlayerChatRoom::create([
            'participant_ids' => [$userId, $otherUserId],
            'type' => 'direct',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Chat odası oluşturuldu.',
            'data' => $room,
        ], 201);
    }

    // Mesaj gönder
    public function sendMessage(Request $request, int $roomId): JsonResponse
    {
        $room = PlayerChatRoom::findOrFail($roomId);

        // Koda katılımcı mı kontrol et
        if (!in_array($request->user()->id, $room->participant_ids)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu sohbete katılım yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array'],
        ]);

        $message = ChatMessage::create([
            'room_id' => $roomId,
            'sender_id' => $request->user()->id,
            ...$validated,
        ]);

        // Odanın son mesajını güncelle
        $room->update([
            'last_message' => $validated['message'],
            'last_message_time' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj gönderildi.',
            'data' => $message,
        ], 201);
    }

    // Chat geçmişini al
    public function getChatHistory(int $roomId, Request $request): JsonResponse
    {
        $room = PlayerChatRoom::findOrFail($roomId);

        if (!in_array($request->user()->id, $room->participant_ids)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu sohbete erişim yetkiniz yok.',
            ], 403);
        }

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 50);

        $messages = ChatMessage::where('room_id', $roomId)
            ->where('is_deleted', false)
            ->latest('created_at')
            ->paginate($perPage);

        return response()->json([
            'ok' => true,
            'data' => $messages,
        ]);
    }

    // Oyuncunun sohbetleri
    public function getMyChatRooms(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $rooms = PlayerChatRoom::whereJsonContains('participant_ids', $userId)
            ->where('is_active', true)
            ->latest('last_message_time')
            ->get()
            ->map(function($room) use ($userId) {
                $otherParticipants = array_filter($room->participant_ids, fn($id) => $id !== $userId);

                return [
                    'id' => $room->id,
                    'type' => $room->type,
                    'last_message' => $room->last_message,
                    'last_message_time' => $room->last_message_time,
                    'participants' => count($room->participant_ids),
                    'other_participants' => $otherParticipants,
                ];
            });

        return response()->json([
            'ok' => true,
            'data' => $rooms,
        ]);
    }

    // Mesajı sil
    public function deleteMessage(int $messageId, Request $request): JsonResponse
    {
        $message = ChatMessage::findOrFail($messageId);

        if ($message->sender_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu mesajı silme yetkiniz yok.',
            ], 403);
        }

        $message->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj silindi.',
        ]);
    }

    // Mesajı düzenle
    public function editMessage(Request $request, int $messageId): JsonResponse
    {
        $message = ChatMessage::findOrFail($messageId);

        if ($message->sender_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu mesajı düzenleme yetkiniz yok.',
            ], 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message->update([
            'message' => $validated['message'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj düzenlendi.',
            'data' => $message,
        ]);
    }

    // Mesajı okundu işaretle
    public function markAsRead(int $messageId, Request $request): JsonResponse
    {
        $message = ChatMessage::findOrFail($messageId);

        ChatMessageRead::updateOrCreate(
            [
                'message_id' => $messageId,
                'user_id' => $request->user()->id,
            ],
            [
                'read_at' => now(),
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj okundu olarak işaretlendi.',
        ]);
    }

    // Tepki ekle (Emoji)
    public function addReaction(Request $request, int $messageId): JsonResponse
    {
        $message = ChatMessage::findOrFail($messageId);

        $validated = $request->validate([
            'emoji' => ['required', 'string', 'max:10'],
        ]);

        $reactions = $message->reactions ?? [];
        $reactions[$request->user()->id] = $validated['emoji'];

        $message->update(['reactions' => $reactions]);

        return response()->json([
            'ok' => true,
            'message' => 'Tepki eklendi.',
        ]);
    }
}
