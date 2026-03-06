<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function unreadCount(Request $request)
    {
        $response = $this->getCount($request)->getData(true);

        return response()->json([
            'success' => $response['success'] ?? true,
            'unread_count' => $response['count'] ?? 0,
        ]);
    }

    public function getUnreadCount(Request $request)
    {
        return $this->unreadCount($request);
    }

    public function getCount(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => true,
                    'count' => 0,
                    'has_notifications' => false,
                ]);
            }

            $userId = (int) Auth::id();
            $query = DB::table('notifications')->where('user_id', $userId);

            if (Schema::hasColumn('notifications', 'is_read')) {
                $query->where('is_read', false);
            }

            $unreadCount = $query->count();

            return response()->json([
                'success' => true,
                'count' => $unreadCount,
                'has_notifications' => $unreadCount > 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bildirimler alinirken hata olustu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giris yapmaniz gerekiyor',
                ], 401);
            }

            $userId = (int) Auth::id();
            $limit = max(1, min((int) $request->integer('limit', 30), 100));
            $rows = DB::table('notifications')
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

            $notifications = $rows->map(function ($row): array {
                $payload = [];
                if (isset($row->payload)) {
                    $decoded = json_decode((string) $row->payload, true);
                    if (is_array($decoded)) {
                        $payload = $decoded;
                    }
                } elseif (isset($row->data)) {
                    $decoded = json_decode((string) $row->data, true);
                    if (is_array($decoded)) {
                        $payload = $decoded;
                    }
                }

                $title = $row->title ?? ($payload['title'] ?? 'Bildirim');
                $message = $row->message ?? ($payload['message'] ?? '');
                $timeText = isset($row->created_at) ? (string) $row->created_at : now()->toDateTimeString();

                return [
                    'id' => (int) $row->id,
                    'type' => (string) ($row->type ?? 'system'),
                    'title' => (string) $title,
                    'message' => (string) $message,
                    'icon' => (string) ($payload['icon'] ?? 'bell'),
                    'color' => (string) ($payload['color'] ?? 'blue'),
                    'time' => $timeText,
                    'read' => (bool) ($row->is_read ?? false),
                    'action_url' => $row->action_url ?? ($payload['action_url'] ?? null),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'total' => $notifications->count(),
                'unread' => $notifications->where('read', false)->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bildirimler alinirken hata olustu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function publicList(Request $request)
    {
        try {
            $rows = DB::table('notifications')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $notifications = $rows->map(function ($row): array {
                $payload = [];
                if (isset($row->payload)) {
                    $decoded = json_decode((string) $row->payload, true);
                    if (is_array($decoded)) {
                        $payload = $decoded;
                    }
                } elseif (isset($row->data)) {
                    $decoded = json_decode((string) $row->data, true);
                    if (is_array($decoded)) {
                        $payload = $decoded;
                    }
                }

                return [
                    'id' => (int) $row->id,
                    'type' => (string) ($row->type ?? 'system'),
                    'title' => (string) ($row->title ?? ($payload['title'] ?? 'Bildirim')),
                    'message' => (string) ($row->message ?? ($payload['message'] ?? 'Guncel sistem bildirimi.')),
                    'icon' => (string) ($payload['icon'] ?? 'bell'),
                    'color' => (string) ($payload['color'] ?? 'blue'),
                    'time' => isset($row->created_at) ? (string) $row->created_at : now()->toDateTimeString(),
                    'read' => (bool) ($row->is_read ?? false),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'total' => $notifications->count(),
                'unread' => $notifications->where('read', false)->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Public notifications alinamadi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giris yapmaniz gerekiyor',
                ], 401);
            }

            $query = DB::table('notifications')
                ->where('id', (int) $id)
                ->where('user_id', (int) Auth::id());

            $updates = ['updated_at' => now()];
            if (Schema::hasColumn('notifications', 'is_read')) {
                $updates['is_read'] = true;
            }
            if (Schema::hasColumn('notifications', 'read_at')) {
                $updates['read_at'] = now();
            }

            $affected = $query->update($updates);
            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bildirim bulunamadi',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bildirim okundu olarak isaretlendi',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bildirim guncellenirken hata olustu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giris yapmaniz gerekiyor',
                ], 401);
            }

            $query = DB::table('notifications')->where('user_id', (int) Auth::id());
            if (Schema::hasColumn('notifications', 'is_read')) {
                $query->where('is_read', false);
            }

            $updates = ['updated_at' => now()];
            if (Schema::hasColumn('notifications', 'is_read')) {
                $updates['is_read'] = true;
            }
            if (Schema::hasColumn('notifications', 'read_at')) {
                $updates['read_at'] = now();
            }

            $updated = $query->update($updates);

            return response()->json([
                'success' => true,
                'message' => 'Tum bildirimler okundu olarak isaretlendi',
                'updated' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bildirimler guncellenirken hata olustu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giris yapmaniz gerekiyor',
                ], 401);
            }

            $deleted = DB::table('notifications')
                ->where('id', (int) $id)
                ->where('user_id', (int) Auth::id())
                ->delete();

            if ($deleted === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bildirim bulunamadi',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bildirim silindi',
                'id' => (int) $id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bildirim silinirken hata olustu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
