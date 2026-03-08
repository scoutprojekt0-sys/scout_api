@extends('layouts.app')

@section('title', 'NextScout - Scout Platform')

@section('content')
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">
            <span class="logo-icon">🎯</span>
            nextscout.pro
        </div>
        <div class="nav-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="nav-button btn-login">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-button btn-signup">Çıkış Yap</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-button btn-login">Giriş Yap</a>
                <a href="{{ route('register') }}" class="nav-button btn-signup">Kayıt Ol</a>
            @endauth
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <h1>Futbolcunun Geleceğini Keşfet</h1>
            <p>Scout Platform ile oyuncu yeteneklerini değerlendir, menajerleri bul ve profesyonel kariyerin başını at.</p>
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">Oyuncu Olarak Başla</a>
                <button class="btn-hero btn-hero-secondary" onclick="scrollToFeatures()">Daha Fazlasını Öğren</button>
            </div>
        </div>
    </section>

    <!-- BUTTONS CONTAINER -->
    <div class="buttons-container">
        <div class="buttons-grid">
            <div class="button-card" onclick="navigateTo('/scout')">
                <span class="button-icon">⚽</span>
                <div class="button-label">Scout<br>Platform</div>
            </div>
            <div class="button-card" onclick="navigateTo('/radar')">
                <span class="button-icon">🎯</span>
                <div class="button-label">Radar</div>
            </div>
            <div class="button-card" onclick="navigateTo('/transfermarket')">
                <span class="button-icon">💰</span>
                <div class="button-label">Transfer<br>Market</div>
            </div>
            <div class="button-card" onclick="navigateTo('/statistics')">
                <span class="button-icon">📊</span>
                <div class="button-label">İstatistikler</div>
            </div>
            <div class="button-card" onclick="navigateTo('/legal')">
                <span class="button-icon">⚖️</span>
                <div class="button-label">Hukuk</div>
            </div>
            <div class="button-card" onclick="navigateTo('/messages')">
                <span class="button-icon">📱</span>
                <div class="button-label">Mesajlar</div>
            </div>
            <div class="button-card" onclick="navigateTo('/notifications')">
                <span class="button-icon">🔔</span>
                <div class="button-label">Bildirimler</div>
            </div>
            <div class="button-card" onclick="navigateTo('/help')">
                <span class="button-icon">❓</span>
                <div class="button-label">Yardım</div>
            </div>
            <div class="button-card" onclick="navigateTo('/settings')">
                <span class="button-icon">⚙️</span>
                <div class="button-label">Ayarlar</div>
            </div>
            <div class="button-card" onclick="navigateTo('/manager')">
                <span class="button-icon">👨‍💼</span>
                <div class="button-label">Menajer<br>Paneli</div>
            </div>
            <div class="button-card" onclick="navigateTo('/coach')">
                <span class="button-icon">👨‍🏫</span>
                <div class="button-label">Antrenör<br>Paneli</div>
            </div>
        </div>
    </div>

    <!-- FEATURES SECTION -->
    <section class="features-section" id="features">
        <div class="section-title">
            <h2>Neden Scout Platform?</h2>
            <p>Oyuncu yeteneklerini değerlendir, menajerleri bul ve profesyonel kariyerin başını at.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <span class="feature-icon">🔍</span>
                <h3>Oyuncu Keşfi</h3>
                <p>En iyi oyuncuları bulun, performanslarını analiz edin ve değerlendir.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">📊</span>
                <h3>Detaylı İstatistikler</h3>
                <p>Futbol, basketbol ve voleybolda kapsamlı istatistik ve analiz.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">💬</span>
                <h3>Anonim Mesajlaşma</h3>
                <p>Menajersiz de menajerleriyle iletişime geç, gizliliği koru.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">💰</span>
                <h3>Piyasa Değeri</h3>
                <p>Amatör oyuncuların tıklandıkça artan piyasa değerleri.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">🎯</span>
                <h3>Canlı Maçlar</h3>
                <p>Canlı maçları izle, sonuçları takip et, istatistikleri gör.</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">⚖️</span>
                <h3>Hukuk Desteği</h3>
                <p>Sözleşme yönetimi, avukat destek ve müzakere sistemi.</p>
            </div>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-number">1,250+</div>
                <div class="stat-label">Aktif Oyuncu</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">150+</div>
                <div class="stat-label">Takım</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">2,500+</div>
                <div class="stat-label">Tamamlanan Maç</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">98%</div>
                <div class="stat-label">Memnuniyet Oranı</div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Futbolun Geleceğinin Parçası Ol</h2>
            <p>Hem oyuncu hem menajer, antrenör ve scout olarak platforma katıl. Kariyer ve işletmenin ilerlemesini hızlandır.</p>
            <a href="{{ route('register') }}" class="btn-hero btn-hero-primary" style="margin: 0 auto; display: inline-block;">Hemen Başla</a>
        </div>
    </section>

    <script>
        function navigateTo(path) {
            @auth
                window.location.href = path;
            @else
                window.location.href = '{{ route("register") }}';
            @endauth
        }

        function scrollToFeatures() {
            document.getElementById('features').scrollIntoView({ behavior: 'smooth' });
        }

        // Smooth scroll for links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if(target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
@endsection
