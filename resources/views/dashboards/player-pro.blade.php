<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oyuncu Dashboard - NextScout Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0F172A; color: #E2E8F0; }

        .header {
            background: linear-gradient(180deg, #1E293B, #0F172A);
            border-bottom: 1px solid #334155;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .logo { font-size: 24px; font-weight: 800; color: #60A5FA; display: flex; align-items: center; gap: 8px; }
        .nav { display: flex; gap: 24px; }
        .nav a { color: #94A3B8; text-decoration: none; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; gap: 6px; }
        .nav a:hover { color: #60A5FA; }

        .container { max-width: 1600px; margin: 0 auto; padding: 24px; }

        .profile-header {
            background: linear-gradient(135deg, #1E3A8A, #0369A1);
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(59, 130, 246, 0.2);
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .profile-info { position: relative; z-index: 1; }
        .player-name { font-size: 48px; font-weight: 800; color: #FFFFFF; margin-bottom: 8px; }
        .player-meta { display: flex; gap: 20px; margin-bottom: 16px; flex-wrap: wrap; }
        .meta-item { display: flex; align-items: center; gap: 8px; color: #E0E7FF; font-size: 14px; }
        .meta-item i { color: #60A5FA; }

        .player-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 20px;
        }

        .stat-mini {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid rgba(96, 165, 250, 0.3);
        }

        .stat-mini-value { font-size: 20px; font-weight: 800; color: #60A5FA; }
        .stat-mini-label { font-size: 11px; color: #CBD5E1; margin-top: 4px; text-transform: uppercase; }

        .profile-photo-section {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .profile-photo {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #3B82F6, #0EA5E9);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            margin: 0 auto 16px;
            border: 3px solid #60A5FA;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
        }

        .profile-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #60A5FA, #3B82F6);
            color: #FFFFFF;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: rgba(96, 165, 250, 0.15);
            color: #60A5FA;
            border: 1px solid #60A5FA;
        }

        .btn-secondary:hover {
            background: rgba(96, 165, 250, 0.25);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .section {
            background: #1E293B;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s;
        }

        .section:hover {
            border-color: #60A5FA;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.1);
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #60A5FA;
            font-size: 24px;
        }

        .performance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .performance-stat {
            background: linear-gradient(135deg, #1E3A8A, #0C4A6E);
            border: 1px solid #0369A1;
            padding: 16px;
            border-radius: 8px;
        }

        .perf-label {
            font-size: 12px;
            color: #94A3B8;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .perf-value {
            font-size: 28px;
            font-weight: 800;
            color: #60A5FA;
        }

        .perf-rating {
            font-size: 12px;
            color: #64748B;
            margin-top: 4px;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .video-card {
            background: #0F172A;
            border: 1px solid #334155;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }

        .video-card:hover {
            border-color: #60A5FA;
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.2);
        }

        .video-thumbnail {
            width: 100%;
            height: 120px;
            background: linear-gradient(135deg, #1E3A8A, #0369A1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            position: relative;
        }

        .video-play {
            width: 40px;
            height: 40px;
            background: #60A5FA;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
        }

        .video-info {
            padding: 12px;
        }

        .video-title {
            font-weight: 600;
            color: #E2E8F0;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .video-meta {
            font-size: 12px;
            color: #64748B;
        }

        .interest-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .interest-item {
            background: #0F172A;
            border: 1px solid #334155;
            padding: 16px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .interest-item:hover {
            border-color: #60A5FA;
            background: rgba(59, 130, 246, 0.05);
        }

        .scout-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .scout-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3B82F6, #0EA5E9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-weight: 700;
        }

        .scout-details h4 {
            color: #E2E8F0;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .scout-details p {
            color: #64748B;
            font-size: 12px;
        }

        .interest-action {
            background: #60A5FA;
            color: #FFFFFF;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s;
        }

        .interest-action:hover {
            background: #3B82F6;
        }

        .match-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .match-card {
            background: #0F172A;
            border: 1px solid #334155;
            padding: 16px;
            border-radius: 8px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 16px;
            align-items: center;
        }

        .team {
            text-align: right;
            color: #E2E8F0;
            font-weight: 600;
        }

        .score {
            text-align: center;
            color: #60A5FA;
            font-size: 24px;
            font-weight: 800;
        }

        .team-opponent {
            text-align: left;
            color: #E2E8F0;
            font-weight: 600;
        }

        .match-date {
            grid-column: 1 / -1;
            text-align: center;
            font-size: 12px;
            color: #64748B;
            padding-top: 8px;
            border-top: 1px solid #334155;
        }

        .completion-bar {
            background: #0F172A;
            border: 1px solid #334155;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .completion-title {
            color: #E2E8F0;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
        }

        .progress-bar {
            height: 8px;
            background: #334155;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #60A5FA, #0EA5E9);
            border-radius: 4px;
            transition: width 0.5s;
            width: 75%;
        }

        .missing-items {
            margin-top: 12px;
            font-size: 12px;
            color: #64748B;
        }

        .missing-items li {
            margin: 4px 0;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .profile-header {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .player-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .profile-photo {
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (max-width: 768px) {
            .player-name {
                font-size: 32px;
            }

            .player-meta {
                font-size: 12px;
            }

            .performance-grid {
                grid-template-columns: 1fr;
            }

            .video-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .match-card {
                grid-template-columns: 1fr;
            }

            .team, .score, .team-opponent {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo"><i class="fas fa-users"></i> NextScout Pro</div>
        <div class="nav">
            <a href="/"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="#"><i class="fas fa-user"></i> Profil</a>
            <a href="#"><i class="fas fa-envelope"></i> Mesajlar</a>
            <a href="#"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

    <div class="container">
        <div class="profile-header">
            <div class="profile-info">
                <div class="player-name">{{ Auth::user()->name ?? 'Oyuncu' }}</div>
                <div class="player-meta">
                    <div class="meta-item"><i class="fas fa-shirt"></i> Kaleci</div>
                    <div class="meta-item"><i class="fas fa-globe"></i> Türkiye</div>
                    <div class="meta-item"><i class="fas fa-birthday-cake"></i> 24 yaş</div>
                    <div class="meta-item"><i class="fas fa-location-dot"></i> Galatasaray</div>
                </div>

                <div class="player-stats">
                    <div class="stat-mini">
                        <div class="stat-mini-value">8.2</div>
                        <div class="stat-mini-label">Puan</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value">1,547</div>
                        <div class="stat-mini-label">Görüntülenme</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value">42</div>
                        <div class="stat-mini-label">Scout</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value">12</div>
                        <div class="stat-mini-label">Teklif</div>
                    </div>
                </div>
            </div>

            <div class="profile-photo-section">
                <div class="profile-photo">📸</div>
                <div class="profile-actions">
                    <a href="#" class="btn btn-primary"><i class="fas fa-edit"></i> Düzenle</a>
                    <a href="#" class="btn btn-secondary"><i class="fas fa-share"></i> Paylaş</a>
                </div>
            </div>
        </div>

        <div class="completion-bar">
            <div class="completion-title">
                <span>Profil Tamamlanması</span>
                <span style="color: #60A5FA;">75%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 75%;"></div>
            </div>
            <ul class="missing-items">
                <li>✓ Kişisel Bilgiler (100%)</li>
                <li>✓ Oyun Verisi (100%)</li>
                <li>⚠️ Video Portfolio (50%) - 4 video daha ekle</li>
                <li>⚠️ Sertifikalar (0%) - Sertifika ekle</li>
            </ul>
        </div>

        <div class="dashboard-grid">
            <div>
                <div class="section">
                    <div class="section-title"><i class="fas fa-chart-line"></i> Bu Sezonun Performansı</div>
                    <div class="performance-grid">
                        <div class="performance-stat">
                            <div class="perf-label">Oynadığı Maç</div>
                            <div class="perf-value">24</div>
                            <div class="perf-rating">Liga: 20, Kupa: 4</div>
                        </div>
                        <div class="performance-stat">
                            <div class="perf-label">Ortalama Puan</div>
                            <div class="perf-value">8.2</div>
                            <div class="perf-rating">Ligde Top 5%</div>
                        </div>
                        <div class="performance-stat">
                            <div class="perf-label">Temiz Levha</div>
                            <div class="perf-value">12</div>
                            <div class="perf-rating">%50 temiz levha oranı</div>
                        </div>
                        <div class="performance-stat">
                            <div class="perf-label">Kurtarışlar</div>
                            <div class="perf-value">187</div>
                            <div class="perf-rating">Oyun başına 7.8</div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title"><i class="fas fa-video"></i> Video Portföyü (8 Video)</div>
                    <div class="video-grid">
                        <div class="video-card">
                            <div class="video-thumbnail">
                                <div class="video-play"><i class="fas fa-play"></i></div>
                            </div>
                            <div class="video-info">
                                <div class="video-title">Harika Kurtarışlar</div>
                                <div class="video-meta">2024 Sezonu</div>
                            </div>
                        </div>
                        <div class="video-card">
                            <div class="video-thumbnail">
                                <div class="video-play"><i class="fas fa-play"></i></div>
                            </div>
                            <div class="video-info">
                                <div class="video-title">Hızlı Çıkışlar</div>
                                <div class="video-meta">2023 Sezonu</div>
                            </div>
                        </div>
                        <div class="video-card">
                            <div class="video-thumbnail">
                                <div class="video-play"><i class="fas fa-play"></i></div>
                            </div>
                            <div class="video-info">
                                <div class="video-title">Penaltı Kurtarışları</div>
                                <div class="video-meta">3 maç</div>
                            </div>
                        </div>
                        <div class="video-card" style="opacity: 0.7; cursor: not-allowed;">
                            <div class="video-thumbnail" style="background: #0C4A6E;">
                                <i class="fas fa-plus" style="font-size: 32px; color: #60A5FA;"></i>
                            </div>
                            <div class="video-info">
                                <div class="video-title">Video Ekle</div>
                                <div class="video-meta">Tıkla & Yükle</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title"><i class="fas fa-futbol"></i> Son Maçlar</div>
                    <div class="match-list">
                        <div class="match-card">
                            <div class="team">Galatasaray</div>
                            <div class="score">3 - 1</div>
                            <div class="team-opponent">Fenerbahçe</div>
                            <div class="match-date">4 Mart 2026 • Süper Lig • Puan: 8.5</div>
                        </div>
                        <div class="match-card">
                            <div class="team">Galatasaray</div>
                            <div class="score">1 - 0</div>
                            <div class="team-opponent">Beşiktaş</div>
                            <div class="match-date">1 Mart 2026 • Süper Lig • Puan: 8.0</div>
                        </div>
                        <div class="match-card">
                            <div class="team">Galatasaray</div>
                            <div class="score">2 - 2</div>
                            <div class="team-opponent">Trabzonspor</div>
                            <div class="match-date">27 Şub 2026 • Süper Lig • Puan: 7.5</div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="section">
                    <div class="section-title"><i class="fas fa-eye"></i> Scout İlgisi (42)</div>
                    <div class="interest-list">
                        <div class="interest-item">
                            <div class="scout-info">
                                <div class="scout-avatar">R</div>
                                <div class="scout-details">
                                    <h4>Real Madrid</h4>
                                    <p>Direktör Scout</p>
                                </div>
                            </div>
                            <button class="interest-action">Kontakt</button>
                        </div>
                        <div class="interest-item">
                            <div class="scout-info">
                                <div class="scout-avatar">M</div>
                                <div class="scout-details">
                                    <h4>Manchester City</h4>
                                    <p>Scout Yöneticisi</p>
                                </div>
                            </div>
                            <button class="interest-action">Kontakt</button>
                        </div>
                        <div class="interest-item">
                            <div class="scout-info">
                                <div class="scout-avatar">L</div>
                                <div class="scout-details">
                                    <h4>Liverpool</h4>
                                    <p>Teknik Direktör</p>
                                </div>
                            </div>
                            <button class="interest-action">Kontakt</button>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title"><i class="fas fa-trophy"></i> Başarılar</div>
                    <div class="interest-list">
                        <div class="interest-item" style="justify-content: flex-start;">
                            <i class="fas fa-medal" style="color: #FCD34D; font-size: 20px;"></i>
                            <div style="flex: 1; margin-left: 12px;">
                                <div style="color: #E2E8F0; font-weight: 600;">Süper Lig Şampiyonu</div>
                                <div style="color: #64748B; font-size: 12px;">2024</div>
                            </div>
                        </div>
                        <div class="interest-item" style="justify-content: flex-start;">
                            <i class="fas fa-star" style="color: #FCD34D; font-size: 20px;"></i>
                            <div style="flex: 1; margin-left: 12px;">
                                <div style="color: #E2E8F0; font-weight: 600;">En İyi Kaleci Adayı</div>
                                <div style="color: #64748B; font-size: 12px;">Süper Lig 2024</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title"><i class="fas fa-cog"></i> Hızlı İşlemler</div>
                    <div class="interest-list" style="gap: 8px;">
                        <button class="btn btn-primary" style="width: 100%; justify-content: center;">
                            <i class="fas fa-video-plus"></i> Video Yükle
                        </button>
                        <button class="btn btn-secondary" style="width: 100%; justify-content: center;">
                            <i class="fas fa-file-pdf"></i> CV İndir
                        </button>
                        <button class="btn btn-secondary" style="width: 100%; justify-content: center;">
                            <i class="fas fa-share-alt"></i> Profil Paylaş
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
