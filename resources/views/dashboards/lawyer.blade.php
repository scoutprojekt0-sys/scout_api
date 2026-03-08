<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avukat Dashboard - NextScout</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0F172A;
            color: #E2E8F0;
        }

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

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #A78BFA;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav {
            display: flex;
            gap: 24px;
        }

        .nav a {
            color: #94A3B8;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav a:hover {
            color: #A78BFA;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .welcome-section {
            background: linear-gradient(135deg, #7C3AED, #A78BFA);
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 40px;
            color: #FFFFFF;
            box-shadow: 0 8px 32px rgba(124, 58, 237, 0.2);
        }

        .welcome-section h1 {
            font-size: 36px;
            margin-bottom: 12px;
        }

        .welcome-section p {
            opacity: 0.95;
            font-size: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #1E293B;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            border-color: #A78BFA;
            box-shadow: 0 4px 16px rgba(167, 139, 250, 0.1);
        }

        .stat-label {
            font-size: 12px;
            color: #94A3B8;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #A78BFA;
        }

        .stat-change {
            font-size: 12px;
            color: #64748B;
            margin-top: 8px;
        }

        .section {
            background: #1E293B;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.3s;
        }

        .section:hover {
            border-color: #A78BFA;
            box-shadow: 0 4px 16px rgba(167, 139, 250, 0.1);
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #E2E8F0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #A78BFA;
            font-size: 24px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .action-btn {
            background: linear-gradient(135deg, #7C3AED, #A78BFA);
            color: #FFFFFF;
            border: none;
            padding: 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(124, 58, 237, 0.3);
        }

        .action-btn i {
            font-size: 24px;
        }

        .logout-btn {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        @media (max-width: 768px) {
            .welcome-section {
                padding: 24px;
            }

            .welcome-section h1 {
                font-size: 24px;
            }

            .action-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <i class="fas fa-gavel"></i>
            NextScout - Avukat Dashboard
        </div>
        <div class="nav">
            <a href="{{ route('home') }}"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="#"><i class="fas fa-briefcase"></i> Hizmetlerim</a>
            <a href="#"><i class="fas fa-envelope"></i> Mesajlar</a>
            <a href="#" onclick="logout(); return false;" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Hoş Geldin, {{ Auth::user()->name ?? 'Avukat' }}! 🏛️</h1>
            <p>Yasal hizmetlerin yönetim paneline hoş geldin. Buradan tüm hizmetlerini ve müşteri taleplerini yönetebilirsin.</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Aktif Hizmetler</div>
                <div class="stat-value">0</div>
                <div class="stat-change">Hizmet ekle</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Müşteri Talepleri</div>
                <div class="stat-value">0</div>
                <div class="stat-change">Beklenen talep yok</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tamamlanan Projeler</div>
                <div class="stat-value">0</div>
                <div class="stat-change">Henüz proje yok</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Ortalama Puanı</div>
                <div class="stat-value">★ 5.0</div>
                <div class="stat-change">0 inceleme</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-bolt"></i> Hızlı İşlemler
            </div>
            <div class="action-grid">
                <a href="#" class="action-btn">
                    <i class="fas fa-file-contract"></i>
                    Yeni Hizmet Ekle
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-briefcase"></i>
                    Profil Düzenle
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-dollar-sign"></i>
                    Fiyatları Ayarla
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-chart-line"></i>
                    İstatistikler
                </a>
            </div>
        </div>

        <!-- Hizmetler -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-tasks"></i> Sunulan Hizmetler
            </div>
            <p style="color: #94A3B8;">Henüz hiç hizmet eklenmemiş. Hizmet eklemek için hızlı işlemlerden "Yeni Hizmet Ekle" butonunu kullan.</p>
        </div>

        <!-- Müşteri Talepleri -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-inbox"></i> Müşteri Talepleri
            </div>
            <p style="color: #94A3B8;">Henüz müşteri talebiniz yok. Profil oluşturduktan sonra müşteriler tarafından bulunabileceksin.</p>
        </div>
    </div>

    <script>
        // Çıkış fonksiyonu
        function logout() {
            if (confirm('Çıkış yapmak istediğinize emin misiniz?')) {
                // Laravel logout endpoint'ine istek gönder
                fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('nextscout_token')
                    }
                }).then(() => {
                    localStorage.removeItem('nextscout_token');
                    localStorage.removeItem('nextscout_user');
                    location.href = '/';
                }).catch(() => {
                    // Hata durumunda bile localStorage temizle
                    localStorage.removeItem('nextscout_token');
                    localStorage.removeItem('nextscout_user');
                    location.href = '/';
                });
            }
        }
    </script>
</body>
</html>
