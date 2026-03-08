<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Anasayfa HTML'i göster
     */
    public function showHomepage()
    {
        return view('homepage');
    }

    /**
     * Giriş yapanlar için dashboard'u göster
     */
    public function showDashboard(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/');
        }

        // Activate new role-specific dashboards
        $role = strtolower((string) ($user->role ?? $user->user_type ?? 'player'));

        if (in_array($role, ['admin', 'super_admin'], true)) {
            return redirect()->route('dashboard.admin');
        }

        if (in_array($role, ['scout'], true)) {
            return redirect()->route('dashboard.scout');
        }

        if (in_array($role, ['manager', 'agent'], true)) {
            return redirect()->route('dashboard.manager');
        }

        if (in_array($role, ['club', 'team'], true)) {
            return redirect()->route('dashboard.club');
        }

        return redirect()->route('dashboard.player');
    }

    /**
     * 11 Buton listesini getir
     */
    private function getButtons()
    {
        return [
            [
                'id' => 'scout_platform',
                'name' => '⚽ SCOUT PLATFORM',
                'icon' => '⚽',
                'description' => 'Oyuncu Keşfi & Profil Yönetimi',
                'route' => '/scout',
            ],
            [
                'id' => 'radar',
                'name' => '🎯 RADAR',
                'icon' => '🎯',
                'description' => 'Trendler, Canlı Maçlar & Haberler',
                'route' => '/radar',
            ],
            [
                'id' => 'transfermarket',
                'name' => '💰 TRANSFERMARKET',
                'icon' => '💰',
                'description' => 'Piyasa Değeri & Transfer',
                'route' => '/transfermarket',
            ],
            [
                'id' => 'statistics',
                'name' => '📊 İSTATİSTİKLER',
                'icon' => '📊',
                'description' => 'Oyuncu & Takım İstatistikleri',
                'route' => '/statistics',
            ],
            [
                'id' => 'legal',
                'name' => '⚖️ HUKUK',
                'icon' => '⚖️',
                'description' => 'Sözleşme, Avukat & Müzakere',
                'route' => '/legal',
            ],
            [
                'id' => 'messages',
                'name' => '📱 MESAJLAR',
                'icon' => '📱',
                'description' => 'Direkt Mesajlaşma & Chat',
                'route' => '/messages',
            ],
            [
                'id' => 'notifications',
                'name' => '🔔 BİLDİRİMLER',
                'icon' => '🔔',
                'description' => 'Sistem Bildirimleri',
                'route' => '/notifications',
            ],
            [
                'id' => 'help',
                'name' => '❓ YARDIM',
                'icon' => '❓',
                'description' => 'Rehber, FAQ & Destek',
                'route' => '/help',
            ],
            [
                'id' => 'settings',
                'name' => '⚙️ AYARLAR',
                'icon' => '⚙️',
                'description' => 'Profil Ayarları & Gizlilik',
                'route' => '/settings',
            ],
            [
                'id' => 'manager_panel',
                'name' => '👨‍💼 MENAJER PANELİ',
                'icon' => '👨‍💼',
                'description' => 'Menajer İçin Özel Araçlar',
                'route' => '/manager',
            ],
            [
                'id' => 'coach_panel',
                'name' => '👨‍🏫 ANTRENÖR PANELİ',
                'icon' => '👨‍🏫',
                'description' => 'Antrenör İçin Özel Araçlar',
                'route' => '/coach',
            ],
        ];
    }
}
