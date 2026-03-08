<?php

use App\Http\Controllers\Api\AmateurTeamController;
use App\Http\Controllers\Api\AmateurMarketController;
use App\Http\Controllers\Api\AdminPanelController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClubController;
use App\Http\Controllers\Api\CommunityEventController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ContractNegotiationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\FreeAgentController;
use App\Http\Controllers\Api\HelpController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\HomePageTabController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\LawyerController;
use App\Http\Controllers\Api\LegalServicesController;
use App\Http\Controllers\Api\LeagueController;
use App\Http\Controllers\Api\LiveMatchController;
use App\Http\Controllers\Api\LocalizationController;
use App\Http\Controllers\Api\ManagerScoutViewController;
use App\Http\Controllers\Api\MarketValueController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\PlayerChatController;
use App\Http\Controllers\Api\PlayerComparisonController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PlayerMessagingController;
use App\Http\Controllers\Api\PlayerSearchController;
use App\Http\Controllers\Api\PlayerStatisticController;
use App\Http\Controllers\Api\ProfileCardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProfileViewController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ScoutReportController;
use App\Http\Controllers\Api\SportsController;
use App\Http\Controllers\Api\SportStatsController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\StaffProfileController;
use App\Http\Controllers\Api\SiteContentController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\SuccessStoryController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\TeamStatsController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\TrialRequestController;
use App\Http\Controllers\Api\VideoPortfolioController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth')->name('register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateMe']);
    });
});

// API health check endpoint
Route::get('/ping', function () {
    return response()->json([
        'ok' => true,
        'message' => 'API is reachable',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Extended health check for monitoring probes.
Route::get('/health', function () {
    $checks = [
        'app' => true,
        'db' => false,
        'cache' => false,
    ];
    $details = [];
    $status = 200;

    try {
        DB::select('SELECT 1');
        $checks['db'] = true;
    } catch (\Throwable $e) {
        $details['db_error'] = $e->getMessage();
        $status = 503;
    }

    try {
        $probeKey = 'health_probe_' . now()->timestamp;
        Cache::put($probeKey, 'ok', 5);
        $checks['cache'] = Cache::get($probeKey) === 'ok';
        if (!$checks['cache']) {
            $status = 503;
        }
    } catch (\Throwable $e) {
        $details['cache_error'] = $e->getMessage();
        $status = 503;
    }

    return response()->json([
        'ok' => $status === 200,
        'checks' => $checks,
        'details' => $details,
        'timestamp' => now()->toIso8601String(),
        'env' => app()->environment(),
    ], $status);
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
Route::get('/success-stories', [SuccessStoryController::class, 'index']);
Route::get('/users', [AuthController::class, 'users']);
Route::get('/public/players', [ProfileController::class, 'publicPlayers']);
Route::get('/public/players/{userId}/profile', [ProfileController::class, 'publicPlayerProfile']);
Route::get('/public/players/quality-summary', [ProfileController::class, 'publicQualitySummary']);
Route::get('/public/staff/{userId}/profile', [StaffProfileController::class, 'publicProfile']);
Route::get('/public/lawyers', [LawyerController::class, 'publicIndex']);

// ========== PUBLIC ROUTES ==========
Route::get('/', [HomeController::class, 'getPublicHome']);
Route::get('/home', [HomeController::class, 'getPublicHome']);
Route::get('/news', [HomeController::class, 'getNews']);

// ========== ANASAYFA TAB YAPISI ==========
Route::get('/homepage/tabs', [HomePageTabController::class, 'getHomePageAllTabs']);
Route::get('/homepage/tabs/scout', [HomePageTabController::class, 'getScoutPlatformTab']);
Route::get('/homepage/tabs/radar', [HomePageTabController::class, 'getRadarTab']);
Route::get('/homepage/tabs/transfermarket', [HomePageTabController::class, 'getTransferMarketTab']);

// ========== CANLI MAÇLAR API ==========
Route::get('/live-matches', [LiveMatchController::class, 'liveMatches']);
Route::post('/live-matches', [LiveMatchController::class, 'store']);
Route::get('/matches/recent', [LiveMatchController::class, 'recentResults']);
Route::get('/matches/upcoming', [LiveMatchController::class, 'upcomingMatches']);
Route::get('/matches/{matchId}', [LiveMatchController::class, 'matchDetails']);
Route::get('/matches/{matchId}/scorers', [LiveMatchController::class, 'matchScorers']);

// ========== ANASAYFA - 11 BUTTON YAPISI (Admin Paneli AYRI!) ==========
Route::get('/homepage/complete', [HomepageController::class, 'getHomepageButtons']);
Route::get('/homepage/button/{buttonId}', [HomepageController::class, 'getButtonDetails']);
Route::get('/community-events', [CommunityEventController::class, 'index']);
Route::get('/community-events/{id}', [CommunityEventController::class, 'show']);
Route::get('/amateur/standings', [LeagueController::class, 'amateurStandings']);
Route::get('/public/footer-pages', [SiteContentController::class, 'footerPages']);
Route::post('/public/contact-messages', [SiteContentController::class, 'storeContactMessage'])->middleware('throttle:30,1');

Route::middleware('auth:sanctum')->group(function () {
    // ========== AUTHENTICATED HOME (Sidebar + Partial) ==========
    Route::get('/dashboard-lite', [HomeController::class, 'getAuthenticatedDashboard']);

    // ========== FULL DASHBOARD ==========
    Route::get('/dashboard', [HomeController::class, 'getFullDashboard']);

    Route::apiResource('players', PlayerController::class)->only(['index', 'show', 'update']);
    Route::apiResource('teams', TeamController::class)->only(['index', 'show', 'update']);
    Route::apiResource('staff', StaffController::class)->only(['index', 'show', 'update']);
    Route::get('/staff-profiles/me', [StaffProfileController::class, 'me']);
    Route::put('/staff-profiles/me', [StaffProfileController::class, 'updateMe']);

    Route::post('/media', [MediaController::class, 'store']);
    Route::get('/users/{id}/media', [MediaController::class, 'indexByUser']);
    Route::delete('/media/{id}', [MediaController::class, 'destroy']);

    Route::apiResource('opportunities', OpportunityController::class);

    Route::post('/opportunities/{id}/apply', [ApplicationController::class, 'apply']);

    // ========== AMATÖR FUTBOL PİYASA SİSTEMİ ==========
    Route::get('/market/amateur/player/{playerId}', [AmateurMarketController::class, 'getPlayerMarketValue']);
    Route::post('/market/amateur/player/{playerId}/view', [AmateurMarketController::class, 'recordProfileView']);
    Route::post('/market/amateur/player/{playerId}/engagement', [AmateurMarketController::class, 'recordEngagement']);
    Route::post('/market/amateur/player/{playerId}/performance', [AmateurMarketController::class, 'recordMatchPerformance']);
    Route::post('/market/amateur/player/{playerId}/scout-interest', [AmateurMarketController::class, 'recordScoutInterest']);
    Route::get('/market/amateur/leaderboard', [AmateurMarketController::class, 'getMarketLeaderboard']);
    Route::get('/market/amateur/trending', [AmateurMarketController::class, 'getWeeklyTrending']);
    Route::get('/market/amateur/player/{playerId}/history', [AmateurMarketController::class, 'getPlayerPointsHistory']);
    Route::get('/market/amateur/statistics', [AmateurMarketController::class, 'getMarketStatistics']);
    Route::post('/market/amateur/transfer-offer/{playerId}', [AmateurMarketController::class, 'sendTransferOffer']);
    Route::post('/market/amateur/transfer-offer/{offerId}/respond', [AmateurMarketController::class, 'respondToTransferOffer']);
    Route::get('/applications/incoming', [ApplicationController::class, 'incoming']);
    Route::get('/applications/outgoing', [ApplicationController::class, 'outgoing']);
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'changeStatus']);

    Route::post('/contacts', [ContactController::class, 'store']);
    Route::get('/contacts/inbox', [ContactController::class, 'inbox']);
    Route::get('/contacts/sent', [ContactController::class, 'sent']);
    Route::patch('/contacts/{id}/status', [ContactController::class, 'changeStatus']);

    // Favoriler
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{targetUserId}/toggle', [FavoriteController::class, 'toggle']);
    Route::get('/favorites/{targetUserId}/check', [FavoriteController::class, 'check']);

    // Bildirimler
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete']);

    // Profil görüntüleme
    Route::post('/profile-views/{userId}/track', [ProfileViewController::class, 'track']);
    Route::get('/profile-views/my-views', [ProfileViewController::class, 'myViews']);
    Route::get('/profile-views/{userId}/count', [ProfileViewController::class, 'viewCount']);

    // Scout Raporları
    Route::get('/scout-reports', [ScoutReportController::class, 'index']);
    Route::post('/scout-reports', [ScoutReportController::class, 'store']);
    Route::get('/scout-reports/{id}', [ScoutReportController::class, 'show']);
    Route::put('/scout-reports/{id}', [ScoutReportController::class, 'update']);
    Route::delete('/scout-reports/{id}', [ScoutReportController::class, 'destroy']);

    // Oyuncu İstatistikleri
    Route::get('/players/{playerUserId}/statistics', [PlayerStatisticController::class, 'index']);
    Route::post('/player-statistics', [PlayerStatisticController::class, 'store']);
    Route::put('/player-statistics/{id}', [PlayerStatisticController::class, 'update']);
    Route::delete('/player-statistics/{id}', [PlayerStatisticController::class, 'destroy']);

    // Sözleşmeler
    Route::get('/contracts', [ContractController::class, 'index']);
    Route::post('/contracts', [ContractController::class, 'store']);
    Route::get('/contracts/{id}', [ContractController::class, 'show']);
    Route::put('/contracts/{id}', [ContractController::class, 'update']);
    Route::delete('/contracts/{id}', [ContractController::class, 'destroy']);

    // Destek Talepleri
    Route::get('/support-tickets', [SupportTicketController::class, 'index']);
    Route::post('/support-tickets', [SupportTicketController::class, 'store']);
    Route::get('/support-tickets/{id}', [SupportTicketController::class, 'show']);
    Route::post('/support-tickets/{id}/messages', [SupportTicketController::class, 'addMessage']);
    Route::post('/support-tickets/{id}/close', [SupportTicketController::class, 'close']);

    // Şikayetler
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/my-reports', [ReportController::class, 'myReports']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);

    // ========== TRANSFERMARKT ÖZELLİKLERİ ==========

    // Kulüpler
    Route::get('/clubs', [ClubController::class, 'index']);
    Route::get('/clubs/most-valuable', [ClubController::class, 'mostValuable']);
    Route::get('/clubs/{id}', [ClubController::class, 'show']);
    Route::get('/clubs/{id}/squad', [ClubController::class, 'squad']);
    Route::get('/clubs/{id}/transfers', [ClubController::class, 'transfers']);

    // Ligler
    Route::get('/leagues', [LeagueController::class, 'index']);
    Route::get('/leagues/{id}', [LeagueController::class, 'show']);
    Route::get('/leagues/{id}/standings', [LeagueController::class, 'standings']);
    Route::get('/leagues/{id}/top-scorers', [LeagueController::class, 'topScorers']);
    Route::get('/leagues/{id}/top-assists', [LeagueController::class, 'topAssists']);

    // Transferler
    Route::get('/transfers', [TransferController::class, 'index']);
    Route::post('/transfers', [TransferController::class, 'store']);
    Route::get('/transfers/player/{playerUserId}/history', [TransferController::class, 'playerHistory']);
    Route::get('/transfers/club/{clubId}/activity', [TransferController::class, 'clubActivity']);

    // Piyasa Değeri
    Route::get('/market-values/player/{playerUserId}/history', [MarketValueController::class, 'playerHistory']);
    Route::post('/market-values', [MarketValueController::class, 'addValuation']);
    Route::get('/market-values/most-valuable', [MarketValueController::class, 'mostValuable']);
    Route::get('/market-values/trends', [MarketValueController::class, 'valueTrends']);

    // Oyuncu Karşılaştırma
    Route::post('/players/compare', [PlayerComparisonController::class, 'compare']);
    Route::get('/players/{playerUserId}/similar', [PlayerComparisonController::class, 'similar']);

    // ========== AMATÖR FUTBOL ÖZELLİKLERİ ==========

    // Amatör Takımlar
    Route::get('/amateur-teams', [AmateurTeamController::class, 'index']);
    Route::post('/amateur-teams', [AmateurTeamController::class, 'store']);
    Route::get('/amateur-teams/nearby', [AmateurTeamController::class, 'nearbyTeams']);
    Route::get('/amateur-teams/{id}', [AmateurTeamController::class, 'show']);
    Route::put('/amateur-teams/{id}', [AmateurTeamController::class, 'update']);

    // Deneme Talepleri
    Route::post('/trial-requests', [TrialRequestController::class, 'store']);
    Route::get('/trial-requests/my-requests', [TrialRequestController::class, 'myRequests']);
    Route::get('/trial-requests/team/{teamId}', [TrialRequestController::class, 'teamRequests']);
    Route::post('/trial-requests/{id}/respond', [TrialRequestController::class, 'respond']);
    Route::post('/trial-requests/{id}/feedback', [TrialRequestController::class, 'addFeedback']);

    // Serbest Oyuncu İlanları
    Route::get('/free-agents', [FreeAgentController::class, 'index']);
    Route::post('/free-agents', [FreeAgentController::class, 'store']);
    Route::get('/free-agents/my-listing', [FreeAgentController::class, 'myListing']);
    Route::get('/free-agents/{id}', [FreeAgentController::class, 'show']);
    Route::put('/free-agents/{id}', [FreeAgentController::class, 'update']);

    // Video Portföy
    Route::get('/video-portfolio/player/{playerUserId}', [VideoPortfolioController::class, 'index']);
    Route::post('/video-portfolio', [VideoPortfolioController::class, 'store']);
    Route::put('/video-portfolio/{id}', [VideoPortfolioController::class, 'update']);
    Route::delete('/video-portfolio/{id}', [VideoPortfolioController::class, 'delete']);
    Route::get('/video-portfolio/{id}/view', [VideoPortfolioController::class, 'view']);
    Route::get('/video-portfolio/featured', [VideoPortfolioController::class, 'featured']);

    // Topluluk Etkinlikleri
    Route::post('/community-events', [CommunityEventController::class, 'store']);
    Route::get('/community-events/my-events', [CommunityEventController::class, 'myEvents']);
    Route::post('/community-events/{id}/register', [CommunityEventController::class, 'register']);

    // ========== MULTI-SPORT ÖZELLİKLERİ ==========

    // Spor Türleri
    Route::get('/sports/list', [SportsController::class, 'listSports']);
    Route::post('/sports/preference', [SportsController::class, 'setSportPreference']);
    Route::get('/sports/preference', [SportsController::class, 'getSportPreference']);
    Route::get('/sports/filter', [SportsController::class, 'filterBySource']);

    // Spor-Spesifik İstatistikler
    Route::get('/sport-stats/player/{playerUserId}', [SportStatsController::class, 'getPlayerStats']);
    Route::get('/sport-stats/player/{playerUserId}/sport/{sport}', [SportStatsController::class, 'getSportStats']);
    Route::put('/sport-stats/player/{playerUserId}', [SportStatsController::class, 'updateStats']);
    Route::get('/sport-stats/leaderboard', [SportStatsController::class, 'leaderboard']);

    // ========== TAKIM İSTATİSTİKLERİ ==========

    Route::get('/team-stats/{teamId}', [TeamStatsController::class, 'getTeamStats']);
    Route::put('/team-stats/{teamId}', [TeamStatsController::class, 'updateTeamStats']);
    Route::get('/team-schedule/{teamId}', [TeamStatsController::class, 'getTeamSchedule']);
    Route::get('/team-availability/{teamId}', [TeamStatsController::class, 'getTeamAvailability']);
    Route::put('/team-availability/{teamId}', [TeamStatsController::class, 'updateTeamAvailability']);
    Route::post('/team-comparison', [TeamStatsController::class, 'getTeamComparison']);

    // ========== CANLI MAÇLAR ==========

    // Auth group path renamed to avoid overriding public /live-matches
    Route::get('/match-center/live-matches', [LiveMatchController::class, 'liveMatches']);
    Route::get('/match/{matchId}/details', [LiveMatchController::class, 'matchDetails']);
    Route::put('/match/{matchId}/live-update', [LiveMatchController::class, 'updateLiveMatch']);
    Route::get('/match/{matchId}/scorers', [LiveMatchController::class, 'matchScorers']);
    Route::get('/recent-results', [LiveMatchController::class, 'recentResults']);
    Route::get('/upcoming-matches', [LiveMatchController::class, 'upcomingMatches']);

    // ========== OYUNCU MESAJLAŞMA SİSTEMİ ==========

    // Direkt Mesajlar
    Route::post('/messages/send', [PlayerMessagingController::class, 'sendMessage']);
    Route::get('/messages/inbox', [PlayerMessagingController::class, 'inbox']);
    Route::get('/messages/sent', [PlayerMessagingController::class, 'sent']);
    Route::get('/messages/{messageId}/read', [PlayerMessagingController::class, 'readMessage']);
    Route::post('/messages/mark-all-read', [PlayerMessagingController::class, 'markAllAsRead']);
    Route::post('/messages/{messageId}/archive', [PlayerMessagingController::class, 'archiveMessage']);

    // Chat Sistemi (Oyuncu Arası Sohbet)
    Route::post('/chat/create-room', [PlayerChatController::class, 'createDirectChat']);
    Route::get('/chat/rooms', [PlayerChatController::class, 'getMyChatRooms']);
    Route::post('/chat/rooms/{roomId}/message', [PlayerChatController::class, 'sendMessage']);
    Route::get('/chat/rooms/{roomId}/history', [PlayerChatController::class, 'getChatHistory']);
    Route::post('/chat/messages/{messageId}/delete', [PlayerChatController::class, 'deleteMessage']);
    Route::put('/chat/messages/{messageId}/edit', [PlayerChatController::class, 'editMessage']);
    Route::post('/chat/messages/{messageId}/read', [PlayerChatController::class, 'markAsRead']);
    Route::post('/chat/messages/{messageId}/react', [PlayerChatController::class, 'addReaction']);

    // Menajerin Anonim Bakış Sistemi
    Route::post('/scout/view-profile/{playerUserId}', [ManagerScoutViewController::class, 'recordProfileView']);
    Route::get('/scout/anonymous-notifications', [ManagerScoutViewController::class, 'getAnonymousNotifications']);
    Route::post('/scout/anonymous-notifications/{notificationId}/read', [ManagerScoutViewController::class, 'readAnonymousNotification']);
    Route::get('/scout/my-views', [ManagerScoutViewController::class, 'myViewsHistory']);
    Route::post('/scout/send-secret-interest/{playerUserId}', [ManagerScoutViewController::class, 'sendSecretInterestNotification']);
    Route::get('/scout/secret-interests', [ManagerScoutViewController::class, 'getSecretInterests']);

    // ========== HUKUK BÖLÜMÜ (LEGAL SYSTEM) ==========

    // Avukat Yönetimi
    Route::get('/lawyers', [LawyerController::class, 'index']);
    Route::post('/lawyers/register', [LawyerController::class, 'register']);
    Route::get('/lawyers/{lawyerId}', [LawyerController::class, 'show']);
    Route::put('/lawyers/profile', [LawyerController::class, 'update']);

    // Sözleşme Yönetimi
    Route::post('/contracts/create', [ContractController::class, 'create']);
    Route::post('/contracts/{contractId}/propose', [ContractController::class, 'propose']);
    Route::get('/contracts/{contractId}', [ContractController::class, 'show']);
    Route::get('/contracts/my-contracts', [ContractController::class, 'myContracts']);

    // İmzalama
    Route::post('/contracts/sign/{signatureRequestId}', [ContractController::class, 'sign']);
    Route::post('/contracts/reject/{signatureRequestId}', [ContractController::class, 'reject']);

    // Müzakere
    Route::post('/contracts/{contractId}/negotiation/start', [ContractNegotiationController::class, 'startNegotiation']);
    Route::post('/negotiation/{negotiationId}/respond', [ContractNegotiationController::class, 'respondToNegotiation']);
    Route::get('/contracts/{contractId}/negotiation/history', [ContractNegotiationController::class, 'getNegotiationHistory']);

    // Uyuşmazlık
    Route::post('/contracts/{contractId}/dispute', [ContractNegotiationController::class, 'reportDispute']);

    // Avukat İncelemesi
    Route::post('/contracts/{contractId}/review', [ContractNegotiationController::class, 'reviewAndApprove']);

    // ========== KÜLTÜRLEŞTİRME (LOCALIZATION) ==========

    // Ülke ve Dil Bilgileri (Public)
    Route::get('/countries', [LocalizationController::class, 'countries']);
    Route::get('/countries/{countryCode}', [LocalizationController::class, 'getCountry']);
    Route::get('/regions', [LocalizationController::class, 'getRegions']);
    Route::get('/regions/{region}/countries', [LocalizationController::class, 'getCountriesByRegion']);
    Route::get('/translations/{language}', [LocalizationController::class, 'getTranslations']);
    Route::get('/translations/{language}/{category}', [LocalizationController::class, 'getTranslations']);

    // Kullanıcı Lokalisasyon Ayarları (Authenticated)
    Route::post('/localization/settings', [LocalizationController::class, 'setUserLocalization']);
    Route::get('/localization/settings', [LocalizationController::class, 'getUserLocalization']);

    // Para Birimi Dönüşümü
    Route::post('/currency/convert', [LocalizationController::class, 'convertCurrency']);

    // ========== PROFIL KARTLARI (Profile Cards) ==========

    // Profil Kartlarını Görüntüle
    Route::get('/profile-cards/player/{playerId}', [ProfileCardController::class, 'getPlayerCard']);
    Route::get('/profile-cards/manager/{managerId}', [ProfileCardController::class, 'getManagerCard']);
    Route::get('/profile-cards/coach/{coachId}', [ProfileCardController::class, 'getCoachCard']);

    // Kartla İnteraksiyon
    Route::post('/profile-cards/{cardType}/{cardOwnerId}/like', [ProfileCardController::class, 'likeCard']);
    Route::post('/profile-cards/{cardType}/{cardOwnerId}/comment', [ProfileCardController::class, 'commentCard']);
    Route::post('/profile-cards/{cardType}/{cardOwnerId}/save', [ProfileCardController::class, 'saveCard']);

    // Kartı Ayarla
    Route::post('/profile-cards/settings', [ProfileCardController::class, 'updateCardSettings']);

    // Kartı İstatistikleri
    Route::get('/profile-cards/{cardType}/{cardOwnerId}/stats', [ProfileCardController::class, 'getCardStats']);

    // ========== PROFIL SAYFASI ==========
    Route::get('/profile/me', [ProfileController::class, 'getMyProfile']);
    Route::get('/profile/{userId}', [ProfileController::class, 'viewProfile']);
    Route::post('/profile/settings', [ProfileController::class, 'updateProfileSettings']);

    // ========== BİLDİRİMLER ==========
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notificationId}', [NotificationController::class, 'delete']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);

    // ========== OYUNCU ARAMA (MENAJER) ==========
    Route::post('/search/players', [PlayerSearchController::class, 'search']);
    Route::get('/search/saved', [PlayerSearchController::class, 'getSavedSearches']);
    Route::get('/search/{searchId}/results', [PlayerSearchController::class, 'getSearchResults']);

    // ========== YARDIM SİSTEMİ ==========
    Route::get('/help/categories', [HelpController::class, 'getCategories']);
    Route::get('/help/article/{slug}', [HelpController::class, 'getArticle']);
    Route::get('/help/category/{categorySlug}', [HelpController::class, 'getCategoryArticles']);
    Route::post('/help/article/{slug}/helpful', [HelpController::class, 'markArticleHelpful']);
    Route::post('/help/article/{slug}/unhelpful', [HelpController::class, 'markArticleUnhelpful']);
    Route::get('/help/faq', [HelpController::class, 'getFAQ']);
    Route::post('/help/faq/{faqId}/helpful', [HelpController::class, 'markFAQHelpful']);
    Route::get('/help/search', [HelpController::class, 'search']);

    // ========== ADMIN PANEL ==========
    Route::middleware('admin')->group(function () {
        // Dashboard
        Route::get('/admin/dashboard', [AdminPanelController::class, 'getAdminDashboard']);

        // Users
        Route::get('/admin/users', [AdminPanelController::class, 'getUsers']);
        Route::post('/admin/users/{userId}/ban', [AdminPanelController::class, 'banUser']);
        Route::post('/admin/users/{userId}/unban', [AdminPanelController::class, 'unbanUser']);
        Route::post('/admin/users/{userId}/verify', [AdminPanelController::class, 'verifyUser']);

        // Reports
        Route::get('/admin/reports', [AdminPanelController::class, 'getUserReports']);
        Route::post('/admin/reports/{reportId}/handle', [AdminPanelController::class, 'handleReport']);

        // Support Tickets
        Route::get('/admin/support-tickets', [AdminPanelController::class, 'getSupportTickets']);
        Route::post('/admin/support-tickets/{ticketId}/assign', [AdminPanelController::class, 'assignTicket']);
        Route::post('/admin/support-tickets/{ticketId}/resolve', [AdminPanelController::class, 'resolveTicket']);

        // Settings
        Route::get('/admin/settings', [AdminPanelController::class, 'getSettings']);
        Route::post('/admin/settings', [AdminPanelController::class, 'updateSettings']);

        // Content Moderation
        Route::get('/admin/moderation', [AdminPanelController::class, 'getContentForModeration']);
        Route::post('/admin/moderation/{contentId}', [AdminPanelController::class, 'moderateContent']);

        // Logs
        Route::get('/admin/logs', [AdminPanelController::class, 'getAdminLogs']);
        Route::get('/admin/trial-events', [CommunityEventController::class, 'adminTrialQueue']);
        Route::post('/admin/trial-events/{id}/moderate', [CommunityEventController::class, 'adminModerateTrial']);
        Route::get('/admin/contact-messages', [SiteContentController::class, 'adminContactMessages']);
    });
});

// ========== ADDITIVE PUBLIC COUNTERS ==========
// NOTE: Existing match/notification routes above remain primary.
Route::get('/live-matches/count', [LiveMatchController::class, 'getCount']);
Route::get('/notifications/count', [NotificationController::class, 'getCount']);
Route::get('/notifications/public', [NotificationController::class, 'publicList']);

// ========== DISCOVERY (Manager/Coach Needs + Boost + Weekly Digest) ==========
Route::get('/discovery/manager-needs', [DiscoveryController::class, 'managerNeeds']);
Route::get('/discovery/coach-needs', [DiscoveryController::class, 'coachNeeds']);
Route::get('/discovery/boosts', [DiscoveryController::class, 'boosts']);
Route::post('/discovery/player-views/track', [DiscoveryController::class, 'trackPlayerView']);
Route::get('/discovery/top-viewed', [DiscoveryController::class, 'topViewedPlayers']);
Route::get('/discovery/weekly-digest', [DiscoveryController::class, 'weeklyDigest']);

// ========== LOCALIZATION ==========
Route::get('/languages', [\App\Http\Controllers\Api\LocalizationApiController::class, 'getSupportedLanguages']);
Route::get('/translations', [\App\Http\Controllers\Api\LocalizationApiController::class, 'getTranslations']);

// ========== MOBILE APP ==========
Route::get('/mobile/version', [\App\Http\Controllers\Api\MobileController::class, 'getLatestVersion']);
Route::post('/mobile/check-update', [\App\Http\Controllers\Api\MobileController::class, 'checkUpdate']);

// ========== AUTHENTICATED FEATURES ==========
Route::middleware('auth:sanctum')->group(function () {
    // Community & Social
    Route::prefix('community')->group(function () {
        Route::get('/feed', [\App\Http\Controllers\Api\CommunityController::class, 'getFeed']);
        Route::post('/posts', [\App\Http\Controllers\Api\CommunityController::class, 'createPost']);
        Route::post('/posts/{postId}/like', [\App\Http\Controllers\Api\CommunityController::class, 'toggleLike']);
    });

    // Gamification
    Route::prefix('gamification')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Api\GamificationController::class, 'getProfile']);
        Route::get('/leaderboard', [\App\Http\Controllers\Api\GamificationController::class, 'getLeaderboard']);
        Route::post('/check-achievements', [\App\Http\Controllers\Api\GamificationController::class, 'checkAchievements']);
        Route::post('/referral', [\App\Http\Controllers\Api\GamificationController::class, 'useReferralCode']);
    });

    // Video Management
    Route::prefix('videos')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\VideoController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\VideoController::class, 'upload']);
        Route::get('/{id}', [\App\Http\Controllers\Api\VideoController::class, 'show']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\VideoController::class, 'destroy']);
    });

    // AI Recommendations
    Route::prefix('ai')->group(function () {
        Route::get('/recommendations', [\App\Http\Controllers\Api\AIRecommendationController::class, 'getRecommendations']);
        Route::post('/preferences', [\App\Http\Controllers\Api\AIRecommendationController::class, 'savePreferences']);
        Route::post('/recommendations/{id}/track', [\App\Http\Controllers\Api\AIRecommendationController::class, 'trackInteraction']);
    });

    // User Language
    Route::post('/language', [\App\Http\Controllers\Api\LocalizationApiController::class, 'changeLanguage']);

    // Mobile Device
    Route::post('/mobile/register-device', [\App\Http\Controllers\Api\MobileController::class, 'registerDevice']);

    // Discovery write actions (protected)
    Route::post('/discovery/manager-needs', [DiscoveryController::class, 'managerNeeds'])->middleware('throttle:30,1');
    Route::post('/discovery/coach-needs', [DiscoveryController::class, 'coachNeeds'])->middleware('throttle:30,1');
    Route::post('/discovery/boosts', [DiscoveryController::class, 'boosts'])->middleware('throttle:20,1');

    // Success stories
    Route::post('/success-stories', [SuccessStoryController::class, 'store'])->middleware('throttle:20,1');
});

// ========== LEGAL SERVICES (HUKUK OFİSİ) ==========
Route::prefix('legal')->group(function () {
    // Genel hizmetler (public)
    Route::get('/', [LegalServicesController::class, 'index']);
    Route::get('/popular', [LegalServicesController::class, 'popular']);
    Route::get('/transfer-contracts', [LegalServicesController::class, 'transferContracts']);
    Route::get('/sponsorship-contracts', [LegalServicesController::class, 'sponsorshipContracts']);
    Route::get('/labor-consultation', [LegalServicesController::class, 'laborConsultation']);
    Route::get('/inheritance-consultation', [LegalServicesController::class, 'inheritanceConsultation']);
    Route::get('/tax-consultation', [LegalServicesController::class, 'taxConsultation']);
    Route::get('/lawyer/{lawyerId}', [LegalServicesController::class, 'lawyerDetails']);
    Route::get('/document-templates', [LegalServicesController::class, 'documentTemplates']);
    Route::get('/success-cases', [LegalServicesController::class, 'successCases']);

    // Kimlik doğrulanmış kullanıcılar
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/request-service', [LegalServicesController::class, 'requestService']);
    });
});

Route::get('/trending/today', [\App\Http\Controllers\Api\TrendingController::class, 'getTodayTrending']);
Route::get('/trending/week', [\App\Http\Controllers\Api\TrendingController::class, 'getWeeklyTrending']);
Route::post('/trending/track', [\App\Http\Controllers\Api\TrendingController::class, 'trackInteraction']);

Route::get('/featured', [\App\Http\Controllers\Api\FeaturedController::class, 'getFeatured']);
Route::get('/rising-stars', [\App\Http\Controllers\Api\FeaturedController::class, 'getRisingStars']);
Route::get('/hot-transfers', [\App\Http\Controllers\Api\FeaturedController::class, 'getHotTransfers']);
Route::get('/player-of-week', [\App\Http\Controllers\Api\FeaturedController::class, 'getPlayerOfWeek']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/featured/admin', [\App\Http\Controllers\Api\FeaturedController::class, 'adminList']);
    Route::post('/featured/admin', [\App\Http\Controllers\Api\FeaturedController::class, 'adminStore']);
    Route::patch('/featured/admin/{id}/active', [\App\Http\Controllers\Api\FeaturedController::class, 'adminToggleActive']);

    Route::get('/admin/success-stories', [SuccessStoryController::class, 'adminIndex']);
    Route::patch('/admin/success-stories/{id}/moderate', [SuccessStoryController::class, 'adminModerate']);
});

// ========== CLUB NEEDS ==========
Route::get('/club-needs', [\App\Http\Controllers\Api\ClubNeedsController::class, 'index']);
Route::get('/club-needs/urgent', [\App\Http\Controllers\Api\ClubNeedsController::class, 'urgent']);
Route::get('/club-needs/position/{position}', [\App\Http\Controllers\Api\ClubNeedsController::class, 'byPosition']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/club-needs', [\App\Http\Controllers\Api\ClubNeedsController::class, 'store']);
});
