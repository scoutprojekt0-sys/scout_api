<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menajer Dashboard - NextScout</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #F8FAFC; color: #1F2937; }

        .header { background: #FFFFFF; border-bottom: 2px solid #10B981; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 800; color: #10B981; }
        .nav { display: flex; gap: 20px; }
        .nav a { color: #64748B; text-decoration: none; font-weight: 600; transition: color 0.3s; }
        .nav a:hover { color: #10B981; }

        .container { max-width: 1400px; margin: 0 auto; padding: 24px; }

        .welcome { background: linear-gradient(135deg, #10B981, #059669); color: #FFFFFF; padding: 32px; border-radius: 12px; margin-bottom: 24px; }
        .welcome h1 { font-size: 32px; margin-bottom: 8px; }
        .welcome p { opacity: 0.9; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: #FFFFFF; padding: 24px; border-radius: 12px; border: 1px solid #D1FAE5; transition: transform 0.3s, box-shadow 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(16, 185, 129, 0.1); }
        .stat-label { font-size: 14px; color: #64748B; margin-bottom: 8px; }
        .stat-value { font-size: 36px; font-weight: 800; color: #10B981; }
        .stat-icon { float: right; font-size: 32px; color: #D1FAE5; }

        .section { background: #FFFFFF; padding: 24px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #D1FAE5; }
        .section-title { font-size: 20px; font-weight: 700; color: #1F2937; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
        .section-title i { color: #10B981; }

        .action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #10B981, #059669); color: #FFFFFF; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); }
        .btn-secondary { background: #ECFDF5; color: #10B981; border: 2px solid #10B981; }
        .btn-secondary:hover { background: #D1FAE5; }

        .client-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; }
        .client-card { background: #F8FAFC; border-radius: 12px; padding: 20px; border: 1px solid #D1FAE5; transition: all 0.3s; }
        .client-card:hover { border-color: #10B981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); }
        .client-header { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
        .client-avatar { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #10B981, #059669); display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-size: 24px; font-weight: 800; }
        .client-info h3 { font-size: 18px; color: #1F2937; margin-bottom: 4px; }
        .client-info p { font-size: 14px; color: #64748B; }
        .client-value { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 12px; border-top: 1px solid #E5E7EB; }
        .value-label { font-size: 12px; color: #64748B; }
        .value-amount { font-size: 18px; font-weight: 700; color: #10B981; }

        .deal-list { display: flex; flex-direction: column; gap: 12px; }
        .deal-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #F8FAFC; border-radius: 8px; }
        .deal-info h4 { font-size: 16px; color: #1F2937; margin-bottom: 4px; }
        .deal-info p { font-size: 14px; color: #64748B; }
        .deal-amount { font-size: 20px; font-weight: 700; color: #10B981; }
        .deal-status { padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; margin-top: 8px; }
        .status-active { background: #D1FAE5; color: #065F46; }
        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-completed { background: #DBEAFE; color: #1E40AF; }

        .income-chart { display: flex; align-items: flex-end; gap: 12px; height: 200px; }
        .chart-bar { flex: 1; background: linear-gradient(180deg, #10B981, #059669); border-radius: 8px 8px 0 0; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; padding: 8px; color: #FFFFFF; font-weight: 600; font-size: 14px; transition: all 0.3s; cursor: pointer; }
        .chart-bar:hover { transform: translateY(-8px); opacity: 0.9; }
        .chart-label { font-size: 12px; color: #64748B; text-align: center; margin-top: 8px; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .welcome h1 { font-size: 24px; }
            .container { padding: 16px; }
            .client-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo"><i class="fas fa-search"></i> NextScout</div>
        <div class="nav">
            <a href="/"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="/clients"><i class="fas fa-users"></i> Müvekkillerim</a>
            <a href="/deals"><i class="fas fa-handshake"></i> Transferler</a>
            <a href="/logout"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h1>Hoş Geldin, {{ Auth::user()->name ?? 'Menajer' }}! 💼</h1>
            <p>Müvekkillerini yönet, transfer fırsatlarını yakala ve komisyonunu al!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-user-tie stat-icon"></i>
                <div class="stat-label">Aktif Müvekkil</div>
                <div class="stat-value">18</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-handshake stat-icon"></i>
                <div class="stat-label">Devam Eden Transfer</div>
                <div class="stat-value">7</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-trophy stat-icon"></i>
                <div class="stat-label">Başarılı Transfer</div>
                <div class="stat-value">42</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign stat-icon"></i>
                <div class="stat-label">Toplam Komisyon</div>
                <div class="stat-value">€1.2M</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-fire"></i> Hızlı İşlemler</div>
            <div class="action-buttons">
                <a href="/clients/add" class="btn btn-primary"><i class="fas fa-user-plus"></i> Yeni Müvekkil Ekle</a>
                <a href="/deals/new" class="btn btn-secondary"><i class="fas fa-plus"></i> Transfer Başlat</a>
                <a href="/opportunities" class="btn btn-secondary"><i class="fas fa-bullseye"></i> Fırsatlar</a>
                <a href="/calendar" class="btn btn-secondary"><i class="fas fa-calendar"></i> Takvim</a>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-users"></i> Öncelikli Müvekkillerim</div>
            <div class="client-grid">
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">AY</div>
                        <div class="client-info">
                            <h3>Ahmet Yılmaz</h3>
                            <p>Forvet • 19 yaş • Fenerbahçe</p>
                        </div>
                    </div>
                    <div class="client-value">
                        <span class="value-label">Piyasa Değeri</span>
                        <span class="value-amount">€2.5M</span>
                    </div>
                </div>
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">MK</div>
                        <div class="client-info">
                            <h3>Mehmet Kaya</h3>
                            <p>Orta Saha • 20 yaş • Galatasaray</p>
                        </div>
                    </div>
                    <div class="client-value">
                        <span class="value-label">Piyasa Değeri</span>
                        <span class="value-amount">€3.2M</span>
                    </div>
                </div>
                <div class="client-card">
                    <div class="client-header">
                        <div class="client-avatar">ED</div>
                        <div class="client-info">
                            <h3>Emre Demir</h3>
                            <p>Kanat • 18 yaş • Beşiktaş</p>
                        </div>
                    </div>
                    <div class="client-value">
                        <span class="value-label">Piyasa Değeri</span>
                        <span class="value-amount">€1.8M</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-chart-line"></i> Devam Eden Transferler</div>
            <div class="deal-list">
                <div class="deal-item">
                    <div class="deal-info">
                        <h4>Ahmet Yılmaz → Premier League (İngiltere)</h4>
                        <p>Müzakere aşaması • Son güncelleme: 2 saat önce</p>
                        <span class="deal-status status-active">Aktif</span>
                    </div>
                    <div class="deal-amount">€2.8M</div>
                </div>
                <div class="deal-item">
                    <div class="deal-info">
                        <h4>Mehmet Kaya → La Liga (İspanya)</h4>
                        <p>Teklif bekleniyor • Son güncelleme: 1 gün önce</p>
                        <span class="deal-status status-pending">Beklemede</span>
                    </div>
                    <div class="deal-amount">€3.5M</div>
                </div>
                <div class="deal-item">
                    <div class="deal-info">
                        <h4>Can Öztürk → Bundesliga (Almanya)</h4>
                        <p>İmza aşaması • Tamamlandı: 3 gün önce</p>
                        <span class="deal-status status-completed">Tamamlandı</span>
                    </div>
                    <div class="deal-amount">€4.2M</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-chart-bar"></i> Aylık Komisyon Grafiği</div>
            <div class="income-chart">
                <div>
                    <div class="chart-bar" style="height: 60%;">€85K</div>
                    <div class="chart-label">Oca</div>
                </div>
                <div>
                    <div class="chart-bar" style="height: 75%;">€120K</div>
                    <div class="chart-label">Şub</div>
                </div>
                <div>
                    <div class="chart-bar" style="height: 100%;">€180K</div>
                    <div class="chart-label">Mar</div>
                </div>
                <div>
                    <div class="chart-bar" style="height: 45%;">€65K</div>
                    <div class="chart-label">Nis</div>
                </div>
                <div>
                    <div class="chart-bar" style="height: 90%;">€150K</div>
                    <div class="chart-label">May</div>
                </div>
                <div>
                    <div class="chart-bar" style="height: 70%;">€110K</div>
                    <div class="chart-label">Haz</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
