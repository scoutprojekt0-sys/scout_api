<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NextScout</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #F8FAFC; color: #1F2937; }

        .header { background: #FFFFFF; border-bottom: 2px solid #EF4444; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1); }
        .logo { font-size: 24px; font-weight: 800; color: #EF4444; display: flex; align-items: center; gap: 8px; }
        .admin-badge { background: #EF4444; color: #FFFFFF; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .nav { display: flex; gap: 20px; }
        .nav a { color: #64748B; text-decoration: none; font-weight: 600; transition: color 0.3s; }
        .nav a:hover { color: #EF4444; }

        .container { max-width: 1600px; margin: 0 auto; padding: 24px; }

        .welcome { background: linear-gradient(135deg, #EF4444, #DC2626); color: #FFFFFF; padding: 32px; border-radius: 12px; margin-bottom: 24px; box-shadow: 0 8px 24px rgba(239, 68, 68, 0.2); }
        .welcome h1 { font-size: 32px; margin-bottom: 8px; }
        .welcome p { opacity: 0.9; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: #FFFFFF; padding: 24px; border-radius: 12px; border: 1px solid #FEE2E2; transition: transform 0.3s, box-shadow 0.3s; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: #FEE2E2; border-radius: 50%; transform: translate(30%, -30%); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(239, 68, 68, 0.15); }
        .stat-label { font-size: 14px; color: #64748B; margin-bottom: 8px; position: relative; z-index: 1; }
        .stat-value { font-size: 36px; font-weight: 800; color: #EF4444; position: relative; z-index: 1; }
        .stat-change { font-size: 14px; color: #10B981; font-weight: 600; margin-top: 8px; position: relative; z-index: 1; }
        .stat-change.negative { color: #EF4444; }
        .stat-icon { position: absolute; right: 24px; top: 24px; font-size: 32px; color: #FECACA; z-index: 1; }

        .section { background: #FFFFFF; padding: 24px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #FEE2E2; }
        .section-title { font-size: 20px; font-weight: 700; color: #1F2937; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
        .section-title i { color: #EF4444; }

        .action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #EF4444, #DC2626); color: #FFFFFF; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4); }
        .btn-secondary { background: #FEF2F2; color: #EF4444; border: 2px solid #EF4444; }
        .btn-secondary:hover { background: #FEE2E2; }
        .btn-success { background: #10B981; color: #FFFFFF; }
        .btn-danger { background: #EF4444; color: #FFFFFF; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        .users-table { width: 100%; border-collapse: collapse; }
        .users-table th { background: #FEF2F2; color: #991B1B; font-weight: 700; text-align: left; padding: 12px; border-bottom: 2px solid #FEE2E2; }
        .users-table td { padding: 12px; border-bottom: 1px solid #F3F4F6; }
        .users-table tr:hover { background: #FEFCE8; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #EF4444, #DC2626); display: inline-flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 700; }
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #D1FAE5; color: #065F46; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger { background: #FEE2E2; color: #991B1B; }
        .badge-info { background: #DBEAFE; color: #1E40AF; }

        .activity-timeline { display: flex; flex-direction: column; gap: 16px; }
        .activity-item { display: flex; gap: 16px; padding: 16px; background: #F9FAFB; border-radius: 8px; border-left: 4px solid #EF4444; }
        .activity-icon { width: 40px; height: 40px; border-radius: 50%; background: #FEE2E2; display: flex; align-items: center; justify-content: center; color: #EF4444; font-size: 18px; }
        .activity-content h4 { font-size: 14px; color: #1F2937; margin-bottom: 4px; }
        .activity-content p { font-size: 13px; color: #64748B; }
        .activity-time { font-size: 12px; color: #9CA3AF; margin-left: auto; }

        .chart-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; }
        .chart-card { background: #F9FAFB; padding: 20px; border-radius: 12px; }
        .chart-title { font-size: 16px; font-weight: 700; color: #1F2937; margin-bottom: 16px; }
        .chart-bars { display: flex; align-items: flex-end; gap: 12px; height: 200px; }
        .chart-bar { flex: 1; background: linear-gradient(180deg, #EF4444, #DC2626); border-radius: 8px 8px 0 0; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; padding: 8px; color: #FFFFFF; font-weight: 600; font-size: 14px; transition: all 0.3s; cursor: pointer; position: relative; }
        .chart-bar:hover { transform: translateY(-8px); opacity: 0.9; }
        .chart-label { font-size: 12px; color: #64748B; text-align: center; margin-top: 8px; }

        .quick-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }
        .quick-stat { background: linear-gradient(135deg, #FEF2F2, #FEE2E2); padding: 20px; border-radius: 8px; text-align: center; }
        .quick-stat-value { font-size: 24px; font-weight: 800; color: #EF4444; }
        .quick-stat-label { font-size: 12px; color: #64748B; margin-top: 4px; text-transform: uppercase; }

        .alert { padding: 16px; border-radius: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
        .alert-warning { background: #FFFBEB; border: 1px solid #FCD34D; color: #92400E; }
        .alert-info { background: #EFF6FF; border: 1px solid #93C5FD; color: #1E40AF; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .welcome h1 { font-size: 24px; }
            .container { padding: 16px; }
            .chart-container { grid-template-columns: 1fr; }
            .users-table { font-size: 14px; }
            .users-table th, .users-table td { padding: 8px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <i class="fas fa-shield-alt"></i> NextScout
            <span class="admin-badge">ADMIN</span>
        </div>
        <div class="nav">
            <a href="/"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="/admin/users"><i class="fas fa-users"></i> Kullanıcılar</a>
            <a href="/admin/reports"><i class="fas fa-file-alt"></i> Raporlar</a>
            <a href="/admin/settings"><i class="fas fa-cog"></i> Ayarlar</a>
            <a href="/logout"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

    <div class="container">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Sistem Durumu:</strong> Tüm sistemler normal çalışıyor. Son yedekleme: 4 Mart 2026, 02:00
            </div>
        </div>

        <div class="welcome">
            <h1>Hoş Geldin, {{ Auth::user()->name ?? 'Admin' }}! 🛡️</h1>
            <p>NextScout yönetim paneline hoş geldin. Platformu buradan yönetebilirsin.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-label">Toplam Kullanıcı</div>
                <div class="stat-value">15,847</div>
                <div class="stat-change"><i class="fas fa-arrow-up"></i> +12.5% bu ay</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-check stat-icon"></i>
                <div class="stat-label">Aktif Kullanıcı</div>
                <div class="stat-value">8,234</div>
                <div class="stat-change"><i class="fas fa-arrow-up"></i> +8.3% bu hafta</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-handshake stat-icon"></i>
                <div class="stat-label">Toplam Transfer</div>
                <div class="stat-value">1,847</div>
                <div class="stat-change"><i class="fas fa-arrow-up"></i> +23 bugün</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign stat-icon"></i>
                <div class="stat-label">Aylık Gelir</div>
                <div class="stat-value">€284K</div>
                <div class="stat-change"><i class="fas fa-arrow-up"></i> +15.2% bu ay</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-bolt"></i> Hızlı İşlemler</div>
            <div class="action-buttons">
                <a href="/admin/users/new" class="btn btn-primary"><i class="fas fa-user-plus"></i> Yeni Kullanıcı Ekle</a>
                <a href="/admin/reports" class="btn btn-secondary"><i class="fas fa-file-alt"></i> Raporları Görüntüle</a>
                <a href="/admin/settings" class="btn btn-secondary"><i class="fas fa-cog"></i> Sistem Ayarları</a>
                <a href="/admin/backup" class="btn btn-secondary"><i class="fas fa-database"></i> Yedek Al</a>
                <a href="/admin/logs" class="btn btn-secondary"><i class="fas fa-clipboard-list"></i> Sistem Logları</a>
            </div>
        </div>

        <div class="section" style="border: 2px solid #4285F4;">
            <div class="section-title">
                <i class="fab fa-google" style="color: #4285F4;"></i>
                Google Entegrasyonu
                <span class="badge badge-success" style="margin-left: 12px;">Aktif</span>
            </div>

            <div class="stats-grid" style="margin-bottom: 20px;">
                <div class="stat-card" style="border-color: #4285F4;">
                    <i class="fab fa-google stat-icon" style="color: #4285F4;"></i>
                    <div class="stat-label">Google Ads Geliri</div>
                    <div class="stat-value" style="color: #4285F4;">€42,580</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> +18.3% bu ay</div>
                </div>
                <div class="stat-card" style="border-color: #EA4335;">
                    <i class="fab fa-youtube stat-icon" style="color: #EA4335;"></i>
                    <div class="stat-label">YouTube Geliri</div>
                    <div class="stat-value" style="color: #EA4335;">€8,240</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> +24.7% bu ay</div>
                </div>
                <div class="stat-card" style="border-color: #34A853;">
                    <i class="fas fa-chart-line stat-icon" style="color: #34A853;"></i>
                    <div class="stat-label">Google Analytics</div>
                    <div class="stat-value" style="color: #34A853;">284K</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> Aylık ziyaretçi</div>
                </div>
                <div class="stat-card" style="border-color: #FBBC04;">
                    <i class="fas fa-mouse-pointer stat-icon" style="color: #FBBC04;"></i>
                    <div class="stat-label">Tıklama Oranı (CTR)</div>
                    <div class="stat-value" style="color: #FBBC04;">3.8%</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> +0.5% artış</div>
                </div>
            </div>

            <div class="action-buttons">
                <button onclick="syncGoogleData()" class="btn btn-primary" style="background: linear-gradient(135deg, #4285F4, #34A853);">
                    <i class="fas fa-sync-alt"></i> Google Verilerini Senkronize Et
                </button>
                <a href="/admin/google/analytics" class="btn btn-secondary" style="color: #4285F4; border-color: #4285F4;">
                    <i class="fab fa-google"></i> Analytics Dashboard
                </a>
                <a href="/admin/google/ads" class="btn btn-secondary" style="color: #EA4335; border-color: #EA4335;">
                    <i class="fas fa-ad"></i> Ads Yönetimi
                </a>
                <a href="/admin/google/settings" class="btn btn-secondary" style="color: #34A853; border-color: #34A853;">
                    <i class="fas fa-cog"></i> API Ayarları
                </a>
            </div>

            <div style="margin-top: 20px; padding: 16px; background: #E8F0FE; border-radius: 8px; border-left: 4px solid #4285F4;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                    <i class="fas fa-info-circle" style="color: #4285F4; font-size: 20px;"></i>
                    <strong style="color: #1967D2;">Google API Durumu</strong>
                </div>
                <div style="font-size: 14px; color: #1967D2;">
                    ✅ Analytics API: Bağlı | Son senkronizasyon: 4 Mart 2026, 14:35<br>
                    ✅ AdSense API: Bağlı | Son güncelleme: 4 Mart 2026, 14:30<br>
                    ✅ YouTube Data API: Bağlı | Kota kullanımı: 3,847 / 10,000
                </div>
            </div>

            <div style="margin-top: 20px;">
                <div style="font-weight: 700; margin-bottom: 12px; color: #1F2937;">Google Gelir Detayları (Bu Ay)</div>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Kaynak</th>
                            <th>Günlük Ort.</th>
                            <th>Bu Hafta</th>
                            <th>Bu Ay</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fab fa-google" style="color: #4285F4; font-size: 20px;"></i>
                                    <strong>Google Ads</strong>
                                </div>
                            </td>
                            <td><strong>€1,419</strong></td>
                            <td>€9,933</td>
                            <td><strong style="color: #4285F4;">€42,580</strong></td>
                            <td><span class="badge badge-success"><i class="fas fa-arrow-up"></i> +18.3%</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fab fa-youtube" style="color: #EA4335; font-size: 20px;"></i>
                                    <strong>YouTube Partner</strong>
                                </div>
                            </td>
                            <td><strong>€275</strong></td>
                            <td>€1,925</td>
                            <td><strong style="color: #EA4335;">€8,240</strong></td>
                            <td><span class="badge badge-success"><i class="fas fa-arrow-up"></i> +24.7%</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-search" style="color: #34A853; font-size: 20px;"></i>
                                    <strong>Google Search Ads</strong>
                                </div>
                            </td>
                            <td><strong>€892</strong></td>
                            <td>€6,244</td>
                            <td><strong style="color: #34A853;">€26,760</strong></td>
                            <td><span class="badge badge-success"><i class="fas fa-arrow-up"></i> +12.1%</span></td>
                        </tr>
                        <tr style="background: #FEF3C7; font-weight: 700;">
                            <td><strong>TOPLAM GOOGLE GELİRİ</strong></td>
                            <td><strong>€2,586</strong></td>
                            <td><strong>€18,102</strong></td>
                            <td><strong style="color: #4285F4; font-size: 18px;">€77,580</strong></td>
                            <td><span class="badge badge-success"><i class="fas fa-arrow-up"></i> +17.8%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-card">
                <div class="chart-title">Kullanıcı Kayıt Trendi (Son 6 Ay)</div>
                <div class="chart-bars">
                    <div>
                        <div class="chart-bar" style="height: 60%;">1.2K</div>
                        <div class="chart-label">Eki</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 75%;">1.5K</div>
                        <div class="chart-label">Kas</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 85%;">1.7K</div>
                        <div class="chart-label">Ara</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 90%;">1.8K</div>
                        <div class="chart-label">Oca</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 95%;">1.9K</div>
                        <div class="chart-label">Şub</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 100%;">2.1K</div>
                        <div class="chart-label">Mar</div>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">Aylık Gelir (Son 6 Ay)</div>
                <div class="chart-bars">
                    <div>
                        <div class="chart-bar" style="height: 55%;">€185K</div>
                        <div class="chart-label">Eki</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 70%;">€210K</div>
                        <div class="chart-label">Kas</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 80%;">€235K</div>
                        <div class="chart-label">Ara</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 75%;">€220K</div>
                        <div class="chart-label">Oca</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 90%;">€265K</div>
                        <div class="chart-label">Şub</div>
                    </div>
                    <div>
                        <div class="chart-bar" style="height: 100%;">€284K</div>
                        <div class="chart-label">Mar</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-chart-pie"></i> Platform İstatistikleri</div>
            <div class="quick-stats">
                <div class="quick-stat">
                    <div class="quick-stat-value">4,523</div>
                    <div class="quick-stat-label">Oyuncu</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-value">1,847</div>
                    <div class="quick-stat-label">Scout</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-value">892</div>
                    <div class="quick-stat-label">Menajer</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-value">247</div>
                    <div class="quick-stat-label">Kulüp</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-value">8,338</div>
                    <div class="quick-stat-label">Diğer</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-user-clock"></i> Son Kayıtlar (Onay Bekleyen)</div>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Kullanıcı</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Kayıt Tarihi</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="user-avatar">AY</span>
                                <strong>Ahmet Yılmaz</strong>
                            </div>
                        </td>
                        <td>ahmet@example.com</td>
                        <td><span class="badge badge-info">Oyuncu</span></td>
                        <td>4 Mart 2026, 14:23</td>
                        <td><span class="badge badge-warning">Beklemede</span></td>
                        <td>
                            <button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Onayla</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Reddet</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="user-avatar">MK</span>
                                <strong>Mehmet Kaya</strong>
                            </div>
                        </td>
                        <td>mehmet@example.com</td>
                        <td><span class="badge badge-success">Scout</span></td>
                        <td>4 Mart 2026, 13:15</td>
                        <td><span class="badge badge-warning">Beklemede</span></td>
                        <td>
                            <button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Onayla</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Reddet</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="user-avatar">ED</span>
                                <strong>Emre Demir</strong>
                            </div>
                        </td>
                        <td>emre@example.com</td>
                        <td><span class="badge badge-warning">Menajer</span></td>
                        <td>4 Mart 2026, 11:42</td>
                        <td><span class="badge badge-warning">Beklemede</span></td>
                        <td>
                            <button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Onayla</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Reddet</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-history"></i> Son Aktiviteler</div>
            <div class="activity-timeline">
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-user-plus"></i></div>
                    <div class="activity-content">
                        <h4>Yeni Kullanıcı Kaydı</h4>
                        <p>Ahmet Yılmaz platforma katıldı (Oyuncu)</p>
                    </div>
                    <div class="activity-time">5 dk önce</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-handshake"></i></div>
                    <div class="activity-content">
                        <h4>Transfer Tamamlandı</h4>
                        <p>Can Yılmaz → Premier League kulübü (€3.5M)</p>
                    </div>
                    <div class="activity-time">12 dk önce</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="activity-content">
                        <h4>Yeni Scout Raporu</h4>
                        <p>Ali Yılmaz - Kerem Aktürkoğlu için rapor ekledi</p>
                    </div>
                    <div class="activity-time">23 dk önce</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-video"></i></div>
                    <div class="activity-content">
                        <h4>Video Yüklendi</h4>
                        <p>Mehmet Kaya yeni performans videosu yükledi</p>
                    </div>
                    <div class="activity-time">1 saat önce</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="activity-content">
                        <h4>Kullanıcı Şikayeti</h4>
                        <p>Spam içerik bildirimi (#12847) - İnceleme gerekli</p>
                    </div>
                    <div class="activity-time">2 saat önce</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart bar hover effects
        document.querySelectorAll('.chart-bar').forEach(bar => {
            bar.addEventListener('click', function() {
                alert('Detaylı grafik: ' + this.textContent);
            });
        });

        // Action button confirmations
        document.querySelectorAll('.btn-success, .btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.textContent.trim().includes('Onayla') ? 'onaylamak' : 'reddetmek';
                if(confirm(`Bu kullanıcıyı ${action} istediğinize emin misiniz?`)) {
                    alert(`Kullanıcı ${action === 'onaylamak' ? 'onaylandı' : 'reddedildi'}!`);
                    this.closest('tr').style.opacity = '0.5';
                }
            });
        });

        // Google Data Sync
        function syncGoogleData() {
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;

            // Loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Senkronize Ediliyor...';

            // Simulate API call
            setTimeout(() => {
                // Success state
                btn.innerHTML = '<i class="fas fa-check"></i> Senkronizasyon Tamamlandı!';
                btn.style.background = 'linear-gradient(135deg, #34A853, #10B981)';

                // Show success notification
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    background: linear-gradient(135deg, #34A853, #10B981);
                    color: white;
                    padding: 16px 24px;
                    border-radius: 12px;
                    box-shadow: 0 8px 24px rgba(52, 168, 83, 0.3);
                    z-index: 10000;
                    font-weight: 600;
                    animation: slideIn 0.3s ease;
                `;
                notification.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                        <div>
                            <div style="font-weight: 700;">Google Verileri Güncellendi!</div>
                            <div style="font-size: 13px; opacity: 0.9;">Analytics, Ads ve YouTube verileri senkronize edildi.</div>
                        </div>
                    </div>
                `;
                document.body.appendChild(notification);

                // Remove notification after 4 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => notification.remove(), 300);
                }, 4000);

                // Reset button after 2 seconds
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    btn.style.background = 'linear-gradient(135deg, #4285F4, #34A853)';

                    // Simulate data update (add small random increases)
                    updateGoogleStats();
                }, 2000);
            }, 2000);
        }

        // Update Google stats with small increases
        function updateGoogleStats() {
            const adsValue = document.querySelector('.stat-value[style*="color: #4285F4"]');
            const youtubeValue = document.querySelector('.stat-value[style*="color: #EA4335"]');
            const analyticsValue = document.querySelector('.stat-value[style*="color: #34A853"]');
            const ctrValue = document.querySelector('.stat-value[style*="color: #FBBC04"]');

            if (adsValue) {
                const current = parseFloat(adsValue.textContent.replace(/[€,]/g, ''));
                const increase = Math.floor(Math.random() * 500) + 100;
                adsValue.textContent = '€' + (current + increase).toLocaleString('tr-TR');
            }

            if (youtubeValue) {
                const current = parseFloat(youtubeValue.textContent.replace(/[€,]/g, ''));
                const increase = Math.floor(Math.random() * 100) + 50;
                youtubeValue.textContent = '€' + (current + increase).toLocaleString('tr-TR');
            }

            if (analyticsValue) {
                const current = parseFloat(analyticsValue.textContent.replace(/[K,]/g, '')) * 1000;
                const increase = Math.floor(Math.random() * 1000) + 500;
                analyticsValue.textContent = Math.floor((current + increase) / 1000) + 'K';
            }

            if (ctrValue) {
                const current = parseFloat(ctrValue.textContent.replace('%', ''));
                const increase = (Math.random() * 0.2).toFixed(1);
                ctrValue.textContent = (parseFloat(current) + parseFloat(increase)).toFixed(1) + '%';
            }
        }

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
