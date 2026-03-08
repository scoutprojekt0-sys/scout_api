<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oyuncu Dashboard - NextScout</title>
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

        .welcome { background: linear-gradient(135deg, #3B82F6, #0EA5E9); color: #FFFFFF; padding: 32px; border-radius: 12px; margin-bottom: 24px; }
        .welcome h1 { font-size: 32px; margin-bottom: 8px; }
        .welcome p { opacity: 0.9; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: #FFFFFF; padding: 24px; border-radius: 12px; border: 1px solid #E0E7FF; transition: transform 0.3s, box-shadow 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(59, 130, 246, 0.1); }
        .stat-label { font-size: 14px; color: #64748B; margin-bottom: 8px; }
        .stat-value { font-size: 36px; font-weight: 800; color: #3B82F6; }
        .stat-icon { float: right; font-size: 32px; color: #DBEAFE; }

        .section { background: #FFFFFF; padding: 24px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #E0E7FF; }
        .section-title { font-size: 20px; font-weight: 700; color: #1F2937; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
        .section-title i { color: #3B82F6; }

        .profile-completion { margin-bottom: 16px; }
        .progress-bar { height: 12px; background: #E0E7FF; border-radius: 6px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #3B82F6, #0EA5E9); border-radius: 6px; transition: width 0.5s; }
        .progress-text { font-size: 14px; color: #64748B; margin-top: 8px; }

        .video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        .video-card { position: relative; border-radius: 8px; overflow: hidden; cursor: pointer; }
        .video-thumbnail { width: 100%; height: 180px; background: #E0E7FF; display: flex; align-items: center; justify-content: center; font-size: 48px; color: #3B82F6; }
        .video-info { padding: 12px; background: #F8FAFC; }
        .video-title { font-weight: 600; color: #1F2937; margin-bottom: 4px; }
        .video-meta { font-size: 12px; color: #64748B; }

        .action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #3B82F6, #0EA5E9); color: #FFFFFF; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); }
        .btn-secondary { background: #F0F9FF; color: #3B82F6; border: 2px solid #3B82F6; }
        .btn-secondary:hover { background: #DBEAFE; }

        .match-list { display: flex; flex-direction: column; gap: 12px; }
        .match-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #F8FAFC; border-radius: 8px; }
        .match-teams { font-weight: 600; }
        .match-date { font-size: 14px; color: #64748B; }
        .match-status { padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .status-live { background: #FEE2E2; color: #DC2626; }
        .status-upcoming { background: #DBEAFE; color: #1D4ED8; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .welcome h1 { font-size: 24px; }
            .container { padding: 16px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo"><i class="fas fa-search"></i> NextScout</div>
        <div class="nav">
            <a href="/"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="/profile"><i class="fas fa-user"></i> Profil</a>
            <a href="/messages"><i class="fas fa-envelope"></i> Mesajlar</a>
            <a href="/logout"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h1>Hoş Geldin, {{ Auth::user()->name ?? 'Oyuncu' }}! ⚽</h1>
            <p>Profilini tamamla, videolarını yükle ve scout'ların dikkatini çek!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-eye stat-icon"></i>
                <div class="stat-label">Profil Görüntülenme</div>
                <div class="stat-value">1,247</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-video stat-icon"></i>
                <div class="stat-label">Video Sayısı</div>
                <div class="stat-value">8</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-star stat-icon"></i>
                <div class="stat-label">Scout İlgisi</div>
                <div class="stat-value">23</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-handshake stat-icon"></i>
                <div class="stat-label">Transfer Teklifi</div>
                <div class="stat-value">3</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-chart-line"></i> Profil Tamamlanma</div>
            <div class="profile-completion">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 75%;"></div>
                </div>
                <div class="progress-text">%75 - Neredeyse tamamlandı! Video ekleyerek %100'e ulaş.</div>
            </div>
            <div class="action-buttons">
                <a href="/profile/edit" class="btn btn-primary"><i class="fas fa-edit"></i> Profili Düzenle</a>
                <a href="/videos/upload" class="btn btn-secondary"><i class="fas fa-upload"></i> Video Yükle</a>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-video"></i> Video Portföyüm</div>
            <div class="video-grid">
                <div class="video-card">
                    <div class="video-thumbnail"><i class="fas fa-play-circle"></i></div>
                    <div class="video-info">
                        <div class="video-title">Maç Performansı - Galatasaray</div>
                        <div class="video-meta">2.5k görüntülenme • 3 gün önce</div>
                    </div>
                </div>
                <div class="video-card">
                    <div class="video-thumbnail"><i class="fas fa-play-circle"></i></div>
                    <div class="video-info">
                        <div class="video-title">Antrenman Highlights</div>
                        <div class="video-meta">1.8k görüntülenme • 1 hafta önce</div>
                    </div>
                </div>
                <div class="video-card">
                    <div class="video-thumbnail"><i class="fas fa-play-circle"></i></div>
                    <div class="video-info">
                        <div class="video-title">Skills & Drills</div>
                        <div class="video-meta">3.2k görüntülenme • 2 hafta önce</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-calendar-alt"></i> Yaklaşan Maçlarım</div>
            <div class="match-list">
                <div class="match-item">
                    <div>
                        <div class="match-teams">Fenerbahçe U19 vs Galatasaray U19</div>
                        <div class="match-date">8 Mart 2026, 15:00</div>
                    </div>
                    <span class="match-status status-upcoming">Yaklaşan</span>
                </div>
                <div class="match-item">
                    <div>
                        <div class="match-teams">Beşiktaş U19 vs Fenerbahçe U19</div>
                        <div class="match-date">12 Mart 2026, 14:00</div>
                    </div>
                    <span class="match-status status-upcoming">Yaklaşan</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
