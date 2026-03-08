<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomepageCompleteController extends Controller
{
    /**
     * ANASAYFA - 12 BUTON YAPISI
     * Tüm butonlar ve altlarındaki içerikler
     */
    public function getCompleteHomepageStructure(Request $request): JsonResponse
    {
        $buttons = [
            // BUTON 1: ⚽ SCOUT PLATFORM
            [
                'id' => 'scout_platform',
                'name' => '⚽ SCOUT PLATFORM',
                'icon' => '🔍',
                'color' => '#667eea',
                'description' => 'Oyuncu Keşfi & Profil Yönetimi',
                'sections' => [
                    [
                        'title' => '🔍 OYUNCU KEŞFİ',
                        'endpoint' => '/api/scout/discovery',
                        'features' => ['Top Players', 'New Players', 'Filter by Sport', 'Verified Users'],
                    ],
                    [
                        'title' => '👔 MENAJER PROFİLLERİ',
                        'endpoint' => '/api/scout/managers',
                        'features' => ['Top Managers', 'Experience', 'Rating', 'Managed Teams'],
                    ],
                    [
                        'title' => '🎯 ANTRENÖR PROFİLLERİ',
                        'endpoint' => '/api/scout/coaches',
                        'features' => ['Top Coaches', 'Certifications', 'Languages', 'Experience'],
                    ],
                    [
                        'title' => '⚽ AMATÖR FUTBOL',
                        'endpoint' => '/api/scout/amateur',
                        'features' => ['Amateur Players', 'Trial Requests', 'Community Events', 'Free Agent Listings'],
                    ],
                    [
                        'title' => '🎓 SCOUT RAPORLARI',
                        'endpoint' => '/api/scout/reports',
                        'features' => ['Video Analysis', 'Player Reviews', 'Technical Evaluation', 'Potential Analysis'],
                    ],
                    [
                        'title' => '🎬 VIDEO PORTFÖY',
                        'endpoint' => '/api/scout/videos',
                        'features' => ['Highlight Videos', 'Training Videos', 'Match Performance', 'Technique Videos'],
                    ],
                ],
            ],

            // BUTON 2: 🎯 RADAR
            [
                'id' => 'radar',
                'name' => '🎯 RADAR',
                'icon' => '📊',
                'color' => '#f59e0b',
                'description' => 'Trendler, Canlı İçerik & Haberler',
                'sections' => [
                    [
                        'title' => '🔥 HAFTALIK TRENDLER',
                        'endpoint' => '/api/radar/trending',
                        'features' => ['Trending Up', 'Trending Down', 'Most Popular', 'Scout Selected'],
                    ],
                    [
                        'title' => '⚽ CANLI MAÇLAR & SONUÇLAR',
                        'endpoint' => '/api/radar/matches',
                        'features' => ['Live Matches', 'Live Score', 'Match Stats', 'Finished Results', 'Upcoming Matches'],
                    ],
                    [
                        'title' => '🏆 LİG TABLOLARI',
                        'endpoint' => '/api/radar/leagues',
                        'features' => ['Points Table', 'Top Scorers', 'Assists', 'Standings', 'Fixtures'],
                    ],
                    [
                        'title' => '📰 HABERLER',
                        'endpoint' => '/api/radar/news',
                        'features' => ['Transfer News', 'Player News', 'Team News', 'League News', 'Last Minute'],
                    ],
                    [
                        'title' => '👁️ SCOUT AKTİVİTELERİ',
                        'endpoint' => '/api/radar/scout-activities',
                        'features' => ['New Reports', 'Video Reviews', 'Scout Opinions', 'Feature Analysis'],
                    ],
                ],
            ],

            // BUTON 3: 💰 TRANSFERMARKET
            [
                'id' => 'transfermarket',
                'name' => '💰 TRANSFERMARKET',
                'icon' => '💎',
                'color' => '#10b981',
                'description' => 'Piyasa Değeri & Transfer Sistemi',
                'sections' => [
                    [
                        'title' => '💎 PROFESYONEL OYUNCU PAZARI',
                        'endpoint' => '/api/market/professional/players',
                        'features' => ['Market Values', 'Most Valuable', 'Value Trends', 'Transfer News'],
                    ],
                    [
                        'title' => '🏪 AMATÖR PİYASA (TIKLANDI PUAN!)',
                        'endpoint' => '/api/market/amateur/players',
                        'features' => ['Click Point System', '+1 for View', '+5 for Goal', 'Leaderboard', 'Weekly Trends'],
                    ],
                    [
                        'title' => '📈 PAZAR ANALİZİ',
                        'endpoint' => '/api/market/analysis',
                        'features' => ['Rising Values', 'Falling Values', 'Market Stats', 'Trend Charts'],
                    ],
                    [
                        'title' => '👤 MENAJER PAZARI',
                        'endpoint' => '/api/market/professional/managers',
                        'features' => ['Manager Market Value', 'Most Valuable', 'Experience Ratio'],
                    ],
                    [
                        'title' => '👨‍🏫 ANTRENÖR PAZARI',
                        'endpoint' => '/api/market/professional/coaches',
                        'features' => ['Coach Market Value', 'Most Valuable', 'Experience Years', 'Certifications'],
                    ],
                    [
                        'title' => '📰 TRANSFER YAZARI',
                        'endpoint' => '/api/market/transfer-news',
                        'features' => ['Rumors', 'Potential Transfers', 'Star Players', 'Hidden Bids'],
                    ],
                ],
            ],

            // BUTON 4: 📊 İSTATİSTİKLER
            [
                'id' => 'statistics',
                'name' => '📊 İSTATİSTİKLER',
                'icon' => '📈',
                'color' => '#8b5cf6',
                'description' => 'Oyuncu & Takım İstatistikleri',
                'sections' => [
                    [
                        'title' => '👤 OYUNCU İSTATİSTİKLERİ',
                        'endpoint' => '/api/stats/players',
                        'features' => ['Goals', 'Assists', 'Matches Played', 'Minutes', 'Cards', 'Shot Stats'],
                    ],
                    [
                        'title' => '🏆 TAKIM İSTATİSTİKLERİ',
                        'endpoint' => '/api/stats/teams',
                        'features' => ['Team Performance', 'Average Goals', 'Defense Success', 'Win/Draw/Loss'],
                    ],
                    [
                        'title' => '⚽ FUTBOL İSTATİSTİKLERİ',
                        'endpoint' => '/api/stats/football',
                        'features' => ['Sport based', 'Position based', 'Season based'],
                    ],
                    [
                        'title' => '🏀 BASKETBOL İSTATİSTİKLERİ',
                        'endpoint' => '/api/stats/basketball',
                        'features' => ['Points', 'Rebounds', 'Assists', 'Blocks'],
                    ],
                    [
                        'title' => '🏐 VOLEYBOL İSTATİSTİKLERİ',
                        'endpoint' => '/api/stats/volleyball',
                        'features' => ['Kills', 'Blocks', 'Aces', 'Errors'],
                    ],
                ],
            ],

            // BUTON 5: ⚖️ HUKUK
            [
                'id' => 'legal',
                'name' => '⚖️ HUKUK',
                'icon' => '⚖️',
                'color' => '#ef4444',
                'description' => 'Sözleşme, Avukat & Müzakere',
                'sections' => [
                    [
                        'title' => '⚖️ AVUKAT PROFİLLERİ',
                        'endpoint' => '/api/legal/lawyers',
                        'features' => ['Lawyer List', 'Expertise', 'Experience', 'Rating', 'Contact'],
                    ],
                    [
                        'title' => '📋 SÖZLEŞME YÖNETİMİ',
                        'endpoint' => '/api/legal/contracts',
                        'features' => ['Current Contracts', 'Templates', 'History', 'Digital Signature', 'Analysis'],
                    ],
                    [
                        'title' => '💬 MÜZAKERE SİSTEMİ',
                        'endpoint' => '/api/legal/negotiations',
                        'features' => ['Manager-Player', 'Manager-Team', 'Player-Team', 'Process', 'Agreement Signing'],
                    ],
                    [
                        'title' => '📝 UYUŞMAZLIK ÇÖZÜMü',
                        'endpoint' => '/api/legal/disputes',
                        'features' => ['Open Cases', 'Mediation', 'Appeal Process', 'Decision Date'],
                    ],
                ],
            ],

            // BUTON 6: 📱 MESAJLAR
            [
                'id' => 'messages',
                'name' => '📱 MESAJLAR',
                'icon' => '💬',
                'color' => '#06b6d4',
                'description' => 'Direkt Mesajlaşma & Chat',
                'sections' => [
                    [
                        'title' => '💬 NORMAL MESAJLAŞMA',
                        'endpoint' => '/api/messages/conversations',
                        'features' => ['Conversations', 'Active Chats', 'Unread Count', 'Message History', 'File Attachments'],
                    ],
                    [
                        'title' => '👤 ANONIM MESAJLAŞMA',
                        'endpoint' => '/api/messages/anonymous',
                        'features' => ['Manager to Player (Anonym)', 'Show Interest', 'Send Offer', 'Secret Conversation', 'Reveal System'],
                    ],
                    [
                        'title' => '🔔 GRUP SOHBETLERI',
                        'endpoint' => '/api/messages/groups',
                        'features' => ['Team Groups', 'Manager Groups', 'Coach Groups', 'Common Groups'],
                    ],
                    [
                        'title' => '🔐 GİZLİ MESAJLAR',
                        'endpoint' => '/api/messages/secret',
                        'features' => ['Encrypted Messages', 'Auto Delete', 'Read Status', 'Recall Option'],
                    ],
                ],
            ],

            // BUTON 7: 🔔 BİLDİRİMLER
            [
                'id' => 'notifications',
                'name' => '🔔 BİLDİRİMLER',
                'icon' => '🔔',
                'color' => '#ec4899',
                'description' => 'Sistem Bildirimleri & Uyarıları',
                'sections' => [
                    [
                        'title' => '📧 BİLDİRİM TÜRLERI',
                        'endpoint' => '/api/notifications/types',
                        'features' => ['Message', 'Profile Viewed', 'Interest Shown', 'Match Result', 'League Update', 'Offer', 'Alert', 'Achievement'],
                    ],
                    [
                        'title' => '✅ BİLDİRİM YÖNETİMİ',
                        'endpoint' => '/api/notifications/management',
                        'features' => ['Mark as Read', 'Mark All Read', 'Delete', 'Unread Count', 'Filters'],
                    ],
                    [
                        'title' => '🔊 BİLDİRİM AYARLARI',
                        'endpoint' => '/api/notifications/settings',
                        'features' => ['In-App', 'Email', 'Push', 'Silent Mode'],
                    ],
                    [
                        'title' => '📊 BİLDİRİM GEÇMİŞİ',
                        'endpoint' => '/api/notifications/history',
                        'features' => ['Last 30 Days', 'By Category', 'Statistics'],
                    ],
                ],
            ],

            // BUTON 8: ❓ YARDIM
            [
                'id' => 'help',
                'name' => '❓ YARDIM',
                'icon' => '❓',
                'color' => '#6366f1',
                'description' => 'Rehber, FAQ & Destek Sistemi',
                'sections' => [
                    [
                        'title' => '📚 YARDIM MAKALARI',
                        'endpoint' => '/api/help/articles',
                        'features' => ['Getting Started', 'Profile Creation', 'Video Upload', 'Messaging Guide', 'Transfer Process'],
                    ],
                    [
                        'title' => '❓ SIKÇA SORULAN SORULAR',
                        'endpoint' => '/api/help/faq',
                        'features' => ['Account Questions', 'Profile Q&A', 'Messaging', 'Payment', 'Technical'],
                    ],
                    [
                        'title' => '🔍 YARDIM ARAMA',
                        'endpoint' => '/api/help/search',
                        'features' => ['Article Search', 'Question Search', 'Video Search'],
                    ],
                    [
                        'title' => '💬 DESTEK TALEBİ',
                        'endpoint' => '/api/help/support-tickets',
                        'features' => ['Create Ticket', 'Open Requests', 'Support Messages', 'Resolution Time', 'Feedback'],
                    ],
                    [
                        'title' => '📞 İLETİŞİM',
                        'endpoint' => '/api/help/contact',
                        'features' => ['Email', 'Chat Support', 'Phone', 'Social Media'],
                    ],
                ],
            ],

            // BUTON 9: ⚙️ AYARLAR
            [
                'id' => 'settings',
                'name' => '⚙️ AYARLAR',
                'icon' => '⚙️',
                'color' => '#78716c',
                'description' => 'Profil Ayarları & Gizlilik',
                'sections' => [
                    [
                        'title' => '👤 PROFIL AYARLARI',
                        'endpoint' => '/api/settings/profile',
                        'features' => ['Edit Name', 'Update Age', 'Profile Photo', 'Banner Image', 'Biography', 'Social Links'],
                    ],
                    [
                        'title' => '🔐 GİZLİLİK AYARLARI',
                        'endpoint' => '/api/settings/privacy',
                        'features' => ['Profile Privacy', 'Hide Email', 'Hide Phone', 'Visibility', 'Block/Unblock'],
                    ],
                    [
                        'title' => '📊 GÖRÜNÜRLÜK AYARLARI',
                        'endpoint' => '/api/settings/visibility',
                        'features' => ['Show Views', 'Hide Stats', 'Notification Settings', 'Preferences'],
                    ],
                    [
                        'title' => '🔑 GÜVENLIK',
                        'endpoint' => '/api/settings/security',
                        'features' => ['Change Password', '2FA', 'Login History', 'Active Sessions', 'Device Management'],
                    ],
                    [
                        'title' => '🌐 DİĞER',
                        'endpoint' => '/api/settings/other',
                        'features' => ['Language', 'Timezone', 'Delete Account', 'Data Download'],
                    ],
                ],
            ],

            // BUTON 10: 👨‍💼 MENAJER PANELİ
            [
                'id' => 'manager_panel',
                'name' => '👨‍💼 MENAJER PANELİ',
                'icon' => '👨‍💼',
                'color' => '#3b82f6',
                'description' => 'Menajer İçin Özel Araçlar',
                'sections' => [
                    [
                        'title' => '🔍 OYUNCU ARAMA (DETAYLI)',
                        'endpoint' => '/api/manager/advanced-search',
                        'features' => ['Sport Filter', 'Position Filter', 'Age Range', 'Height/Weight', 'Rating', 'Location', 'Skill Level', 'Saved Searches'],
                    ],
                    [
                        'title' => '💬 ANONIM MESAJLAŞMA',
                        'endpoint' => '/api/manager/anonymous-messaging',
                        'features' => ['Show Interest', 'Send Offer', 'Secret Conversation', 'Reveal Time'],
                    ],
                    [
                        'title' => '📋 TRANSFER YÖNETİMİ',
                        'endpoint' => '/api/manager/transfers',
                        'features' => ['Offers Sent', 'Transfer History', 'Agreements', 'Negotiations'],
                    ],
                    [
                        'title' => '👥 OYUNCU TAKIBI',
                        'endpoint' => '/api/manager/player-tracking',
                        'features' => ['Watched Players', 'Favorites', 'Notes', 'Evaluation'],
                    ],
                    [
                        'title' => '📊 MENAJER İSTATİSTİKLERİ',
                        'endpoint' => '/api/manager/statistics',
                        'features' => ['Successful Transfers', 'Signed Players', 'Rating', 'View Count'],
                    ],
                ],
            ],

            // BUTON 11: 👨‍🏫 ANTRENÖR PANELİ
            [
                'id' => 'coach_panel',
                'name' => '👨‍🏫 ANTRENÖR PANELİ',
                'icon' => '👨‍🏫',
                'color' => '#f97316',
                'description' => 'Antrenör İçin Özel Araçlar',
                'sections' => [
                    [
                        'title' => '📅 ANTRENMAN PLANI',
                        'endpoint' => '/api/coach/training-plan',
                        'features' => ['Training Calendar', 'Completed Training', 'Planned Training', 'Notes'],
                    ],
                    [
                        'title' => '👤 OYUNCU TAKIBI',
                        'endpoint' => '/api/coach/player-tracking',
                        'features' => ['Training Performance', 'Collaboration Notes', 'Development Tracking', 'Video Review'],
                    ],
                    [
                        'title' => '🎓 SERTİFİKALAR',
                        'endpoint' => '/api/coach/certifications',
                        'features' => ['Current Certs', 'Certificate Process', 'Renewal Dates', 'Verification'],
                    ],
                    [
                        'title' => '📊 BAŞARI İSTATİSTİKLERİ',
                        'endpoint' => '/api/coach/success-stats',
                        'features' => ['Trained Players', 'Success Rate', 'Player Development', 'Rating'],
                    ],
                    [
                        'title' => '🎬 TEKNIK VİDEOLAR',
                        'endpoint' => '/api/coach/technique-videos',
                        'features' => ['Uploaded Videos', 'Technical Analysis', 'Training Videos', 'Review Requests'],
                    ],
                ],
            ],

            // BUTON 12: 🏢 ADMIN PANELİ
            [
                'id' => 'admin_panel',
                'name' => '🏢 ADMIN PANELİ',
                'icon' => '🏢',
                'color' => '#dc2626',
                'description' => 'İdari Yönetim (Admin Üyeler İçin)',
                'sections' => [
                    [
                        'title' => '📊 ADMİN DASHBOARD',
                        'endpoint' => '/api/admin/dashboard',
                        'features' => ['System Stats', 'Total Users', 'Active Users', 'New Registrations', 'Pending Items'],
                    ],
                    [
                        'title' => '👥 KULLANICI YÖNETİMİ',
                        'endpoint' => '/api/admin/users',
                        'features' => ['User List', 'Ban/Unban', 'Email Verification', 'Role Change', 'Details'],
                    ],
                    [
                        'title' => '🚨 RAPORLAR',
                        'endpoint' => '/api/admin/reports',
                        'features' => ['User Reports', 'Dispute Reports', 'Report Management', 'Action History', 'Resolution'],
                    ],
                    [
                        'title' => '📞 DESTEK TALEPLERİ',
                        'endpoint' => '/api/admin/support-tickets',
                        'features' => ['Open Tickets', 'Assign', 'Resolve', 'Response Time', 'Categories'],
                    ],
                    [
                        'title' => '🚫 İÇERİK MODERASYONU',
                        'endpoint' => '/api/admin/moderation',
                        'features' => ['Pending Content', 'Approve/Reject', 'Spam Control', 'Inappropriate Content'],
                    ],
                    [
                        'title' => '⚙️ SİSTEM AYARLARI',
                        'endpoint' => '/api/admin/settings',
                        'features' => ['Site Name & URL', 'Email Settings', 'File Limits', 'Maintenance Mode', 'Features'],
                    ],
                    [
                        'title' => '📝 AUDIT LOGS',
                        'endpoint' => '/api/admin/logs',
                        'features' => ['Admin Actions', 'System Logs', 'Change History', 'Statistics'],
                    ],
                ],
            ],
        ];

        return response()->json([
            'ok' => true,
            'total_buttons' => count($buttons),
            'buttons' => $buttons,
        ]);
    }

    /**
     * Tek bir butonun detaylarını getir
     */
    public function getButtonDetails(string $buttonId): JsonResponse
    {
        $allButtons = $this->getCompleteHomepageStructure(new Request())->json()['buttons'];
        $button = collect($allButtons)->firstWhere('id', $buttonId);

        if (!$button) {
            return response()->json([
                'ok' => false,
                'message' => 'Button not found',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'button' => $button,
        ]);
    }
}
