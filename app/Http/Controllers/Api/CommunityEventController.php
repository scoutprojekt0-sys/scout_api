<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityEvent;
use App\Models\EventParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityEventController extends Controller
{
    private const TRIAL_TYPES = ['trial_day', 'training_session', 'trial_match'];

    public function index(Request $request): JsonResponse
    {
        $query = CommunityEvent::query()->with('organizer');

        if ($request->has('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->has('event_type')) {
            $query->where('event_type', $request->input('event_type'));
        }

        if ($request->boolean('trial_only')) {
            $query->whereIn('event_type', self::TRIAL_TYPES);
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->whereIn('status', ['upcoming', 'registration_open']);
        }

        if ($request->boolean('free_only')) {
            $query->where('is_free', true);
        }

        $events = $query->latest('event_date')->paginate(20);

        return response()->json([
            'ok' => true,
            'data' => $events,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = strtolower((string) ($user->role ?? ''));
        $allowedRoles = ['coach', 'scout', 'team', 'club', 'admin'];

        if (!in_array($role, $allowedRoles, true)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bu ilan tipini sadece antrenor/scout/kulup hesaplari olusturabilir.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'event_type' => ['required', 'in:pickup_game,tournament,training_camp,social,charity,trial_day,training_session,trial_match'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'venue' => ['required', 'string', 'max:120'],
            'event_date' => ['required', 'date', 'after:now'],
            'max_participants' => ['nullable', 'integer', 'min:2'],
            'entry_fee' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['boolean'],
            'skill_level' => ['required', 'in:all_levels,beginner,intermediate,advanced'],
            'contact_info' => ['nullable', 'string', 'max:120'],
        ]);

        $isTrialEvent = in_array($validated['event_type'], self::TRIAL_TYPES, true);
        $status = $isTrialEvent ? 'registration_open' : 'upcoming';

        $event = CommunityEvent::create([
            'organizer_user_id' => $user->id,
            ...$validated,
            'status' => $status,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Etkinlik olusturuldu.',
            'data' => $event,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $event = CommunityEvent::with(['organizer', 'participants.user'])
            ->findOrFail($id);

        return response()->json([
            'ok' => true,
            'data' => $event,
        ]);
    }

    public function register(Request $request, int $id): JsonResponse
    {
        $event = CommunityEvent::findOrFail($id);

        if ($event->isFull()) {
            return response()->json([
                'ok' => false,
                'message' => 'Etkinlik dolu.',
            ], 400);
        }

        $existing = EventParticipant::where('event_id', $id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($existing) {
            return response()->json([
                'ok' => false,
                'message' => 'Zaten bu etkinlige kayitlisiniz.',
            ], 400);
        }

        EventParticipant::create([
            'event_id' => $id,
            'user_id' => $request->user()->id,
            'status' => 'registered',
        ]);

        $event->increment('current_participants');

        return response()->json([
            'ok' => true,
            'message' => 'Etkinlige kayit olundu.',
        ]);
    }

    public function myEvents(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $organized = CommunityEvent::where('organizer_user_id', $userId)
            ->latest()
            ->get();

        $participating = CommunityEvent::query()
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with('organizer')
            ->latest()
            ->get();

        return response()->json([
            'ok' => true,
            'data' => [
                'organized' => $organized,
                'participating' => $participating,
            ],
        ]);
    }

    public function adminTrialQueue(Request $request): JsonResponse
    {
        $events = CommunityEvent::query()
            ->with('organizer')
            ->whereIn('event_type', self::TRIAL_TYPES)
            ->whereIn('status', ['pending', 'upcoming', 'registration_open', 'cancelled'])
            ->latest('event_date')
            ->paginate(50);

        return response()->json([
            'ok' => true,
            'data' => $events,
        ]);
    }

    public function adminModerateTrial(Request $request, int $id): JsonResponse
    {
        $event = CommunityEvent::query()->whereIn('event_type', self::TRIAL_TYPES)->findOrFail($id);

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,cancel'],
        ]);

        $status = match ($validated['action']) {
            'approve' => 'registration_open',
            'cancel' => 'cancelled',
            default => 'cancelled',
        };

        $event->update(['status' => $status]);

        return response()->json([
            'ok' => true,
            'message' => 'Ilan durumu guncellendi.',
            'data' => $event->fresh(),
        ]);
    }
}
