<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tickets = SupportTicket::query()
            ->where('user_id', $request->user()->id)
            ->with(['assignedTo'])
            ->withCount('messages')
            ->latest()
            ->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $tickets,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['sometimes', 'in:low,medium,high,urgent'],
            'category' => ['required', 'in:technical,account,billing,general'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            ...$validated,
            'status' => 'open',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Destek talebi oluşturuldu.',
            'data' => $ticket,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $ticket = SupportTicket::with(['messages.user', 'assignedTo'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'ok' => true,
            'data' => $ticket,
        ]);
    }

    public function addMessage(Request $request, int $id): JsonResponse
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message = SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_staff_reply' => false,
        ]);

        // Ticket'ı yeniden aç
        if ($ticket->status === 'resolved' || $ticket->status === 'closed') {
            $ticket->update(['status' => 'in_progress']);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj eklendi.',
            'data' => $message,
        ], 201);
    }

    public function close(Request $request, int $id): JsonResponse
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $ticket->update([
            'status' => 'closed',
            'resolved_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Destek talebi kapatıldı.',
        ]);
    }
}
