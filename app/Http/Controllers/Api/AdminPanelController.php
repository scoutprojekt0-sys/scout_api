<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminLog;
use App\Models\SystemStatistics;
use App\Models\SupportTicket;
use App\Models\UserReport;
use App\Models\ContentModeration;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    // Admin Kontrolü Middleware'i
    public function __construct()
    {
        // Admin yetki kontrolü - routes'da middleware eklenecek
    }

    // ═════════════════════════════════════════════
    // DASHBOARD
    // ═════════════════════════════════════════════

    public function getAdminDashboard(): JsonResponse
    {
        // Günlük İstatistikler
        $today = SystemStatistics::whereDate('date', today())->first();

        $stats = [
            'total_users' => User::count(),
            'total_players' => User::where('role', 'player')->count(),
            'total_managers' => User::where('role', 'manager')->count(),
            'total_coaches' => User::where('role', 'coach')->count(),
            'active_today' => User::where('last_active_at', '>=', now()->subHours(24))->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
        ];

        $reports = [
            'pending_reports' => UserReport::where('status', 'pending')->count(),
            'pending_moderation' => ContentModeration::where('status', 'pending')->count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'critical_tickets' => SupportTicket::where('priority', 'urgent')
                ->where('status', '!=', 'resolved')
                ->count(),
        ];

        $recentActivity = AdminLog::latest()
            ->limit(10)
            ->with('admin')
            ->get();

        return response()->json([
            'ok' => true,
            'data' => [
                'statistics' => $stats,
                'pending' => $reports,
                'recent_activity' => $recentActivity,
            ],
        ]);
    }

    // ═════════════════════════════════════════════
    // KULLANICILAR
    // ═════════════════════════════════════════════

    public function getUsers(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($status === 'unverified') {
                $query->whereNull('email_verified_at');
            } elseif ($status === 'banned') {
                $query->where('is_banned', true);
            }
        }

        $users = $query->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $users,
        ]);
    }

    public function banUser(Request $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $reason = $request->input('reason', 'No reason provided');

        $user->update(['is_banned' => true]);

        AdminLog::create([
            'admin_id' => $request->user()->id,
            'action' => 'user_banned',
            'target_type' => 'User',
            'target_id' => $userId,
            'description' => "User banned. Reason: $reason",
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Kullanıcı engellendi.',
        ]);
    }

    public function unbanUser(Request $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $user->update(['is_banned' => false]);

        AdminLog::create([
            'admin_id' => $request->user()->id,
            'action' => 'user_updated',
            'target_type' => 'User',
            'target_id' => $userId,
            'description' => 'User unbanned',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Kullanıcı engeli kaldırıldı.',
        ]);
    }

    public function verifyUser(Request $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $user->update(['email_verified_at' => now()]);

        AdminLog::create([
            'admin_id' => $request->user()->id,
            'action' => 'user_verified',
            'target_type' => 'User',
            'target_id' => $userId,
            'description' => 'User email verified',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Kullanıcı doğrulandı.',
        ]);
    }

    // ═════════════════════════════════════════════
    // RAPORLAR
    // ═════════════════════════════════════════════

    public function getUserReports(Request $request): JsonResponse
    {
        $reports = UserReport::where('status', $request->input('status', 'pending'))
            ->with('reporter', 'reportedUser')
            ->paginate(15);

        return response()->json([
            'ok' => true,
            'data' => $reports,
        ]);
    }

    public function handleReport(Request $request, int $reportId): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:dismiss,warn,suspend,ban'],
            'notes' => ['nullable', 'string'],
        ]);

        $report = UserReport::findOrFail($reportId);
        $report->update([
            'status' => 'resolved',
            'admin_notes' => $validated['notes'],
            'handled_by' => $request->user()->id,
            'handled_at' => now(),
        ]);

        // Eylemi gerçekleştir
        if ($validated['action'] === 'ban') {
            User::find($report->reported_user_id)
                ->update(['is_banned' => true]);
        }

        AdminLog::create([
            'admin_id' => $request->user()->id,
            'action' => 'report_handled',
            'target_type' => 'UserReport',
            'target_id' => $reportId,
            'description' => "Report handled with action: {$validated['action']}",
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Rapor işlendi.',
        ]);
    }

    // ═════════════════════════════════════════════
    // DESTEK TALEPLERİ
    // ═════════════════════════════════════════════

    public function getSupportTickets(Request $request): JsonResponse
    {
        $tickets = SupportTicket::where('status', $request->input('status', 'open'))
            ->orderByDesc('priority')
            ->paginate(15);

        return response()->json([
            'ok' => true,
            'data' => $tickets,
        ]);
    }

    public function assignTicket(Request $request, int $ticketId): JsonResponse
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update([
            'assigned_to' => $request->input('assigned_to'),
            'status' => 'in_progress',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Destek talebi atandı.',
        ]);
    }

    public function resolveTicket(Request $request, int $ticketId): JsonResponse
    {
        $validated = $request->validate([
            'notes' => ['required', 'string'],
        ]);

        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update([
            'status' => 'resolved',
            'resolution_notes' => $validated['notes'],
            'resolved_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Destek talebi çözüldü.',
        ]);
    }

    // ═════════════════════════════════════════════
    // SİSTEM AYARLARI
    // ═════════════════════════════════════════════

    public function getSettings(): JsonResponse
    {
        $settings = SystemSetting::first() ?? new SystemSetting();

        return response()->json([
            'ok' => true,
            'data' => $settings,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:100'],
            'support_email' => ['nullable', 'email'],
            'max_upload_size' => ['nullable', 'integer'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string'],
            'enable_user_registration' => ['nullable', 'boolean'],
            'enable_direct_messaging' => ['nullable', 'boolean'],
        ]);

        $settings = SystemSetting::first() ?? new SystemSetting();
        $settings->fill($validated)->save();

        AdminLog::create([
            'admin_id' => $request->user()->id,
            'action' => 'settings_changed',
            'description' => 'System settings updated',
            'changes' => $validated,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Sistem ayarları güncellendi.',
            'data' => $settings,
        ]);
    }

    // ═════════════════════════════════════════════
    // İÇERİK MODERASYONU
    // ═════════════════════════════════════════════

    public function getContentForModeration(): JsonResponse
    {
        $content = ContentModeration::where('status', 'pending')
            ->with('user')
            ->paginate(15);

        return response()->json([
            'ok' => true,
            'data' => $content,
        ]);
    }

    public function moderateContent(Request $request, int $contentId): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,removed'],
            'reason' => ['required_if:status,rejected', 'string'],
        ]);

        $content = ContentModeration::findOrFail($contentId);
        $content->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['reason'] ?? null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        AdminLog::create([
            'admin_id' => $request->user()->id,
            'action' => 'content_removed',
            'target_type' => $content->content_type,
            'target_id' => $content->content_id,
            'description' => "Content {$validated['status']}",
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'İçerik moderasyonu tamamlandı.',
        ]);
    }

    // ═════════════════════════════════════════════
    // LOGLAR
    // ═════════════════════════════════════════════

    public function getAdminLogs(Request $request): JsonResponse
    {
        $logs = AdminLog::with('admin')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $logs,
        ]);
    }
}
