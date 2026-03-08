<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulüp Dashboard - NextScout</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #F8FAFC; color: #1F2937; }

        .header { background: #FFFFFF; border-bottom: 2px solid #8B5CF6; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 800; color: #8B5CF6; }
        .nav { display: flex; gap: 20px; }
        .nav a { color: #64748B; text-decoration: none; font-weight: 600; transition: color 0.3s; }
        .nav a:hover { color: #8B5CF6; }

        .container { max-width: 1400px; margin: 0 auto; padding: 24px; }

        .welcome { background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: #FFFFFF; padding: 32px; border-radius: 12px; margin-bottom: 24px; }
        .welcome h1 { font-size: 32px; margin-bottom: 8px; }
        .welcome p { opacity: 0.9; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: #FFFFFF; padding: 24px; border-radius: 12px; border: 1px solid #EDE9FE; transition: transform 0.3s, box-shadow 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(139, 92, 246, 0.1); }
        .stat-label { font-size: 14px; color: #64748B; margin-bottom: 8px; }
        .stat-value { font-size: 36px; font-weight: 800; color: #8B5CF6; }
        .stat-icon { float: right; font-size: 32px; color: #EDE9FE; }

        .section { background: #FFFFFF; padding: 24px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #EDE9FE; }
        .section-title { font-size: 20px; font-weight: 700; color: #1F2937; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
        .section-title i { color: #8B5CF6; }

        .action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: #FFFFFF; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4); }
        .btn-secondary { background: #FAF5FF; color: #8B5CF6; border: 2px solid #8B5CF6; }
        .btn-secondary:hover { background: #EDE9FE; }

        .squad-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        .squad-card { background: #F8FAFC; border-radius: 12px; padding: 16px; border: 1px solid #EDE9FE; transition: all 0.3s; }
        .squad-card:hover { border-color: #8B5CF6; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15); }
        .squad-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .squad-avatar { width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #8B5CF6, #7C3AED); display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-size: 18px; font-weight: 800; }
        .squad-info h4 { font-size: 16px; color: #1F2937; margin-bottom: 2px; }
        .squad-info p { font-size: 12px; color: #64748B; }
        .squad-stats { display: flex; gap: 12px; font-size: 12px; color: #64748B; }

        .transfer-list { display: flex; flex-direction: column; gap: 12px; }
        .transfer-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #F8FAFC; border-radius: 8px; }
        .transfer-info h4 { font-size: 16px; color: #1F2937; margin-bottom: 4px; }
        .transfer-info p { font-size: 14px; color: #64748B; }
        .transfer-badge { padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-incoming { background: #D1FAE5; color: #065F46; }
        .badge-outgoing { background: #FEE2E2; color: #991B1B; }
        .badge-target { background: #DBEAFE; color: #1E40AF; }

        .scout-reports { display: flex; gap: 12px; flex-wrap: wrap; }
        .report-card { flex: 1; min-width: 250px; background: #F8FAFC; padding: 16px; border-radius: 8px; border: 1px solid #EDE9FE; }
        .report-player { font-weight: 700; color: #1F2937; margin-bottom: 8px; }
        .report-scout { font-size: 14px; color: #64748B; margin-bottom: 8px; }
        .report-rating { display: flex; gap: 4px; }
        .star { color: #FBBF24; font-size: 16px; }

        .budget-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .budget-card { background: linear-gradient(135deg, #FAF5FF, #EDE9FE); padding: 20px; border-radius: 8px; }
        .budget-label { font-size: 12px; color: #64748B; margin-bottom: 8px; text-transform: uppercase; }
        .budget-value { font-size: 28px; font-weight: 800; color: #8B5CF6; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .welcome h1 { font-size: 24px; }
            .container { padding: 16px; }
            .squad-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo"><i class="fas fa-search"></i> NextScout</div>
        <div class="nav">
            <a href="/"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="/squad"><i class="fas fa-users"></i> Kadro</a>
            <a href="/transfers"><i class="fas fa-exchange-alt"></i> Transferler</a>
            <a href="/scouts"><i class="fas fa-binoculars"></i> Scout Raporları</a>
            <a href="/logout"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h1>Hoş Geldin, {{ Auth::user()->club_name ?? 'Kulüp Yöneticisi' }}! 🏆</h1>
            <p>Kadronuzu yönetin, transfer hedeflerinizi belirleyin ve başarıya ulaşın!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-label">Kadro Mevcudu</div>
                <div class="stat-value">28</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-exchange-alt stat-icon"></i>
                <div class="stat-label">Transfer Hedefi</div>
                <div class="stat-value">12</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-file-alt stat-icon"></i>
                <div class="stat-label">Scout Raporu</div>
                <div class="stat-value">47</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-trophy stat-icon"></i>
                <div class="stat-label">Lig Sıralaması</div>
                <div class="stat-value">3.</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-fire"></i> Hızlı İşlemler</div>
            <div class="action-buttons">
                <a href="/players/search" class="btn btn-primary"><i class="fas fa-search"></i> Oyuncu Ara</a>
                <a href="/transfers/new" class="btn btn-secondary"><i class="fas fa-plus"></i> Transfer Başlat</a>
                <a href="/scouts/request" class="btn btn-secondary"><i class="fas fa-binoculars"></i> Scout Talebi</a>
                <a href="/calendar" class="btn btn-secondary"><i class="fas fa-calendar"></i> Maç Takvimi</a>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-wallet"></i> Bütçe Özeti</div>
            <div class="budget-overview">
                <div class="budget-card">
                    <div class="budget-label">Transfer Bütçesi</div>
                    <div class="budget-value">€15M</div>
                </div>
                <div class="budget-card">
                    <div class="budget-label">Maaş Bütçesi</div>
                    <div class="budget-value">€8.5M</div>
                </div>
                <div class="budget-card">
                    <div class="budget-label">Kullanılan</div>
                    <div class="budget-value">€12M</div>
                </div>
                <div class="budget-card">
                    <div class="budget-label">Kalan</div>
                    <div class="budget-value">€3M</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-users"></i> Kadro Durumu</div>
            <div class="squad-grid">
                <div class="squad-card">
                    <div class="squad-header">
                        <div class="squad-avatar">AY</div>
                        <div class="squad-info">
                            <h4>Ahmet Yılmaz</h4>
                            <p>Forvet • 19 yaş</p>
                        </div>
                    </div>
                    <div class="squad-stats">
                        <span>⚽ 15 Gol</span>
                        <span>🎯 8 Asist</span>
                        <span>⭐ 8.5 Rating</span>
                    </div>
                </div>
                <div class="squad-card">
                    <div class="squad-header">
                        <div class="squad-avatar">MK</div>
                        <div class="squad-info">
                            <h4>Mehmet Kaya</h4>
                            <p>Orta Saha • 20 yaş</p>
                        </div>
                    </div>
                    <div class="squad-stats">
                        <span>⚽ 5 Gol</span>
                        <span>🎯 12 Asist</span>
                        <span>⭐ 8.2 Rating</span>
                    </div>
                </div>
                <div class="squad-card">
                    <div class="squad-header">
                        <div class="squad-avatar">ED</div>
                        <div class="squad-info">
                            <h4>Emre Demir</h4>
                            <p>Kanat • 18 yaş</p>
                        </div>
                    </div>
                    <div class="squad-stats">
                        <span>⚽ 10 Gol</span>
                        <span>🎯 7 Asist</span>
                        <span>⭐ 8.0 Rating</span>
                    </div>
                </div>
                <div class="squad-card">
                    <div class="squad-header">
                        <div class="squad-avatar">BÖ</div>
                        <div class="squad-info">
                            <h4>Burak Özdemir</h4>
                            <p>Defans • 22 yaş</p>
                        </div>
                    </div>
                    <div class="squad-stats">
                        <span>⚽ 2 Gol</span>
                        <span>🎯 3 Asist</span>
                        <span>⭐ 7.8 Rating</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-exchange-alt"></i> Transfer Durumu</div>
            <div class="transfer-list">
                <div class="transfer-item">
                    <div class="transfer-info">
                        <h4>Can Yılmaz - Forvet</h4>
                        <p>Bundesliga kulübünden • Müzakere aşamasında • €3.5M</p>
                    </div>
                    <span class="transfer-badge badge-incoming">Gelen Transfer</span>
                </div>
                <div class="transfer-item">
                    <div class="transfer-info">
                        <h4>Serkan Aydın - Orta Saha</h4>
                        <p>La Liga kulübüne • Teklif bekliyor • €2.8M</p>
                    </div>
                    <span class="transfer-badge badge-outgoing">Giden Transfer</span>
                </div>
                <div class="transfer-item">
                    <div class="transfer-info">
                        <h4>Kerem Aktürkoğlu - Kanat</h4>
                        <p>Premier League • Scout raporu istendi</p>
                    </div>
                    <span class="transfer-badge badge-target">Hedef Oyuncu</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-file-alt"></i> Son Scout Raporları</div>
            <div class="scout-reports">
                <div class="report-card">
                    <div class="report-player">Kerem Aktürkoğlu</div>
                    <div class="report-scout">Scout: Ali Yılmaz • 28 Şub 2026</div>
                    <div class="report-rating">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star-half-alt star"></i>
                    </div>
                </div>
                <div class="report-card">
                    <div class="report-player">Barış Alper Yılmaz</div>
                    <div class="report-scout">Scout: Mehmet Demir • 25 Şub 2026</div>
                    <div class="report-rating">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="far fa-star star"></i>
                    </div>
                </div>
                <div class="report-card">
                    <div class="report-player">Cenk Tosun</div>
                    <div class="report-scout">Scout: Can Öztürk • 20 Şub 2026</div>
                    <div class="report-rating">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star-half-alt star"></i>
                        <i class="far fa-star star"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
