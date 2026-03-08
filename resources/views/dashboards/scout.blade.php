<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scout Dashboard - NextScout</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #F8FAFC; color: #1F2937; }

        .header { background: #FFFFFF; border-bottom: 2px solid #3B82F6; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 800; color: #3B82F6; }
        .nav { display: flex; gap: 20px; }
        .nav a { color: #64748B; text-decoration: none; font-weight: 600; transition: color 0.3s; }
        .nav a:hover { color: #3B82F6; }

        .container { max-width: 1400px; margin: 0 auto; padding: 24px; }

        .welcome { background: linear-gradient(135deg, #06B6D4, #3B82F6); color: #FFFFFF; padding: 32px; border-radius: 12px; margin-bottom: 24px; }
        .welcome h1 { font-size: 32px; margin-bottom: 8px; }
        .welcome p { opacity: 0.9; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: #FFFFFF; padding: 24px; border-radius: 12px; border: 1px solid #E0E7FF; transition: transform 0.3s, box-shadow 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(59, 130, 246, 0.1); }
        .stat-label { font-size: 14px; color: #64748B; margin-bottom: 8px; }
        .stat-value { font-size: 36px; font-weight: 800; color: #06B6D4; }
        .stat-icon { float: right; font-size: 32px; color: #DBEAFE; }

        .section { background: #FFFFFF; padding: 24px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #E0E7FF; }
        .section-title { font-size: 20px; font-weight: 700; color: #1F2937; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
        .section-title i { color: #06B6D4; }

        .action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #06B6D4, #0EA5E9); color: #FFFFFF; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4); }
        .btn-secondary { background: #F0F9FF; color: #06B6D4; border: 2px solid #06B6D4; }
        .btn-secondary:hover { background: #DBEAFE; }

        .player-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
        .player-card { background: #F8FAFC; border-radius: 12px; padding: 20px; border: 1px solid #E0E7FF; transition: all 0.3s; cursor: pointer; }
        .player-card:hover { border-color: #06B6D4; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.15); }
        .player-header { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
        .player-avatar { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #06B6D4, #3B82F6); display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-size: 24px; font-weight: 800; }
        .player-info h3 { font-size: 18px; color: #1F2937; margin-bottom: 4px; }
        .player-info p { font-size: 14px; color: #64748B; }
        .player-stats { display: flex; gap: 16px; margin-top: 12px; }
        .player-stat { text-align: center; }
        .player-stat-value { font-size: 20px; font-weight: 700; color: #06B6D4; }
        .player-stat-label { font-size: 12px; color: #64748B; }

        .report-list { display: flex; flex-direction: column; gap: 12px; }
        .report-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #F8FAFC; border-radius: 8px; }
        .report-info h4 { font-size: 16px; color: #1F2937; margin-bottom: 4px; }
        .report-info p { font-size: 14px; color: #64748B; }
        .report-badge { padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-approved { background: #D1FAE5; color: #065F46; }

        .match-watch { display: flex; gap: 12px; flex-wrap: wrap; }
        .match-card { flex: 1; min-width: 280px; background: #F8FAFC; padding: 16px; border-radius: 8px; border: 1px solid #E0E7FF; }
        .match-league { font-size: 12px; color: #64748B; margin-bottom: 8px; }
        .match-teams { font-weight: 700; color: #1F2937; margin-bottom: 8px; }
        .match-time { font-size: 14px; color: #06B6D4; font-weight: 600; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .welcome h1 { font-size: 24px; }
            .container { padding: 16px; }
            .player-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo"><i class="fas fa-search"></i> NextScout</div>
        <div class="nav">
            <a href="/"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="/players"><i class="fas fa-users"></i> Oyuncular</a>
            <a href="/reports"><i class="fas fa-file-alt"></i> Raporlarım</a>
            <a href="/logout"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h1>Hoş Geldin, {{ Auth::user()->name ?? 'Scout' }}! 🔍</h1>
            <p>Yetenekleri keşfet, raporlarını yaz ve geleceğin yıldızlarını bul!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-clipboard-check stat-icon"></i>
                <div class="stat-label">Tamamlanan Rapor</div>
                <div class="stat-value">47</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-check stat-icon"></i>
                <div class="stat-label">Takip Edilen Oyuncu</div>
                <div class="stat-value">128</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-futbol stat-icon"></i>
                <div class="stat-label">İzlenen Maç</div>
                <div class="stat-value">34</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-trophy stat-icon"></i>
                <div class="stat-label">Başarılı Transfer</div>
                <div class="stat-value">8</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-fire"></i> Öncelikli İşlemler</div>
            <div class="action-buttons">
                <a href="/players/search" class="btn btn-primary"><i class="fas fa-search"></i> Oyuncu Ara</a>
                <a href="/reports/new" class="btn btn-secondary"><i class="fas fa-plus"></i> Yeni Rapor Yaz</a>
                <a href="/live-matches" class="btn btn-secondary"><i class="fas fa-tv"></i> Canlı Maçlar</a>
                <a href="/calendar" class="btn btn-secondary"><i class="fas fa-calendar"></i> Takvim</a>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-star"></i> Öne Çıkan Oyuncular</div>
            <div class="player-grid">
                <div class="player-card">
                    <div class="player-header">
                        <div class="player-avatar">AY</div>
                        <div class="player-info">
                            <h3>Ahmet Yılmaz</h3>
                            <p>19 yaş • Forvet • Fenerbahçe U19</p>
                        </div>
                    </div>
                    <div class="player-stats">
                        <div class="player-stat">
                            <div class="player-stat-value">23</div>
                            <div class="player-stat-label">Gol</div>
                        </div>
                        <div class="player-stat">
                            <div class="player-stat-value">12</div>
                            <div class="player-stat-label">Asist</div>
                        </div>
                        <div class="player-stat">
                            <div class="player-stat-value">8.7</div>
                            <div class="player-stat-label">Rating</div>
                        </div>
                    </div>
                </div>
                <div class="player-card">
                    <div class="player-header">
                        <div class="player-avatar">MK</div>
                        <div class="player-info">
                            <h3>Mehmet Kaya</h3>
                            <p>20 yaş • Orta Saha • Galatasaray U21</p>
                        </div>
                    </div>
                    <div class="player-stats">
                        <div class="player-stat">
                            <div class="player-stat-value">7</div>
                            <div class="player-stat-label">Gol</div>
                        </div>
                        <div class="player-stat">
                            <div class="player-stat-value">18</div>
                            <div class="player-stat-label">Asist</div>
                        </div>
                        <div class="player-stat">
                            <div class="player-stat-value">8.2</div>
                            <div class="player-stat-label">Rating</div>
                        </div>
                    </div>
                </div>
                <div class="player-card">
                    <div class="player-header">
                        <div class="player-avatar">ED</div>
                        <div class="player-info">
                            <h3>Emre Demir</h3>
                            <p>18 yaş • Kanat • Beşiktaş U19</p>
                        </div>
                    </div>
                    <div class="player-stats">
                        <div class="player-stat">
                            <div class="player-stat-value">15</div>
                            <div class="player-stat-label">Gol</div>
                        </div>
                        <div class="player-stat">
                            <div class="player-stat-value">9</div>
                            <div class="player-stat-label">Asist</div>
                        </div>
                        <div class="player-stat">
                            <div class="player-stat-value">8.5</div>
                            <div class="player-stat-label">Rating</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-file-alt"></i> Son Raporlarım</div>
            <div class="report-list">
                <div class="report-item">
                    <div class="report-info">
                        <h4>Ahmet Yılmaz - Performans Raporu</h4>
                        <p>Galatasaray vs Fenerbahçe maçı • 2 Mart 2026</p>
                    </div>
                    <span class="report-badge badge-approved">Onaylandı</span>
                </div>
                <div class="report-item">
                    <div class="report-info">
                        <h4>Mehmet Kaya - Teknik Analiz</h4>
                        <p>Beşiktaş vs Trabzonspor maçı • 28 Şubat 2026</p>
                    </div>
                    <span class="report-badge badge-pending">İnceleniyor</span>
                </div>
                <div class="report-item">
                    <div class="report-info">
                        <h4>Emre Demir - Potansiyel Değerlendirme</h4>
                        <p>Ankaragücü vs Samsunspor maçı • 25 Şubat 2026</p>
                    </div>
                    <span class="report-badge badge-approved">Onaylandı</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-calendar-check"></i> İzlenecek Maçlar</div>
            <div class="match-watch">
                <div class="match-card">
                    <div class="match-league">Süper Lig U19</div>
                    <div class="match-teams">Fenerbahçe vs Galatasaray</div>
                    <div class="match-time">8 Mart 2026, 15:00</div>
                </div>
                <div class="match-card">
                    <div class="match-league">1. Lig U21</div>
                    <div class="match-teams">Samsunspor vs Ankaragücü</div>
                    <div class="match-time">9 Mart 2026, 14:00</div>
                </div>
                <div class="match-card">
                    <div class="match-league">Süper Lig U19</div>
                    <div class="match-teams">Beşiktaş vs Trabzonspor</div>
                    <div class="match-time">10 Mart 2026, 16:00</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
