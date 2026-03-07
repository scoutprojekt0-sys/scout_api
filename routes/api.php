<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateMe']);
    });
});

Route::get('/news/live', [NewsController::class, 'live']);
Route::get('/contracts/live', function (\Illuminate\Http\Request $request) {
    $limit = max(1, min((int) $request->query('limit', 12), 50));
    $statusesRaw = (string) $request->query('statuses', 'expired,suspended');
    $requested = collect(explode(',', $statusesRaw))
        ->map(fn ($s) => trim(strtolower($s)))
        ->filter(fn ($s) => in_array($s, ['expired', 'suspended'], true))
        ->values();
    if ($requested->isEmpty()) {
        $requested = collect(['expired', 'suspended']);
    }

    if (!Schema::hasTable('users') || !Schema::hasTable('player_profiles')) {
        return response()->json(['ok' => true, 'data' => [], 'meta' => ['count' => 0]]);
    }

    $hasContractExpires = Schema::hasColumn('player_profiles', 'contract_expires');
    $hasContracts = Schema::hasTable('contracts') && Schema::hasColumn('contracts', 'status');
    $today = now()->toDateString();

    $rows = DB::table('users as u')
        ->leftJoin('player_profiles as pp', 'pp.user_id', '=', 'u.id')
        ->where('u.role', 'player')
        ->select([
            'u.id as player_id',
            'u.name as player_name',
            DB::raw("COALESCE(pp.position, '-') as position"),
            DB::raw("COALESCE(pp.current_team, '-') as club_name"),
            DB::raw($hasContractExpires ? 'pp.contract_expires' : 'NULL as contract_expires'),
            DB::raw($hasContracts
                ? "EXISTS(SELECT 1 FROM contracts c WHERE c.player_user_id = u.id AND c.status IN ('under_negotiation','disputed','terminated')) as has_suspended_contract"
                : '0 as has_suspended_contract'),
        ])
        ->orderByDesc('u.updated_at')
        ->limit($limit)
        ->get();

    $items = $rows->map(function ($row) use ($requested, $today, $hasContractExpires, $hasContracts) {
        $isExpired = $hasContractExpires && !empty($row->contract_expires) && substr((string) $row->contract_expires, 0, 10) < $today;
        $isSuspended = $hasContracts && (int) ($row->has_suspended_contract ?? 0) === 1;

        $status = '';
        if ($isSuspended && $requested->contains('suspended')) $status = 'suspended';
        elseif ($isExpired && $requested->contains('expired')) $status = 'expired';
        if ($status === '') return null;

        return [
            'player_id' => (int) $row->player_id,
            'player_name' => (string) $row->player_name,
            'position' => (string) ($row->position ?? '-'),
            'club_name' => (string) ($row->club_name ?? '-'),
            'status' => $status,
            'note' => $status === 'suspended'
                ? 'Sozlesme sureci askida.'
                : 'Sozlesmesi sona erdi.',
        ];
    })->filter()->values();

    return response()->json([
        'ok' => true,
        'data' => $items,
        'meta' => [
            'count' => $items->count(),
            'statuses' => $requested->all(),
        ],
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('players', PlayerController::class)->only(['index', 'show', 'update']);
    Route::apiResource('teams', TeamController::class)->only(['index', 'show', 'update']);
    Route::apiResource('staff', StaffController::class)->only(['index', 'show', 'update']);

    Route::post('/media', [MediaController::class, 'store']);
    Route::get('/users/{id}/media', [MediaController::class, 'indexByUser']);
    Route::delete('/media/{id}', [MediaController::class, 'destroy']);

    Route::apiResource('opportunities', OpportunityController::class);

    Route::post('/opportunities/{id}/apply', [ApplicationController::class, 'apply']);
    Route::get('/applications/incoming', [ApplicationController::class, 'incoming']);
    Route::get('/applications/outgoing', [ApplicationController::class, 'outgoing']);
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'changeStatus']);

    Route::post('/contacts', [ContactController::class, 'store']);
    Route::get('/contacts/inbox', [ContactController::class, 'inbox']);
    Route::get('/contacts/sent', [ContactController::class, 'sent']);
    Route::patch('/contacts/{id}/status', [ContactController::class, 'changeStatus']);
});
