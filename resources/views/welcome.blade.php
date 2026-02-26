<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NextScout | Homepage</title>
  <style>
    :root {
      --bg: #2c2c2c;
      --panel: #111111;
      --line: #242424;
      --text: #f5f5f5;
      --muted: #a7a7a7;
      --accent: #ffffff;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      background: radial-gradient(circle at 10% 10%, #2c2c2c 0%, transparent 34%), var(--bg);
      color: var(--text);
      font-family: "Segoe UI", Tahoma, sans-serif;
    }

    .page {
      width: min(1100px, 92%);
      margin: 0 auto;
      padding: 24px 0 48px;
    }

    .topbar {
      display: grid;
      grid-template-columns: auto 1fr auto;
      align-items: center;
      border: 1px solid #d6d6d6;
      border-radius: 14px;
      background: rgba(44, 44, 44, 0.95);
      padding: 12px 14px;
      gap: 14px;
    }

    .top-left {
      display: inline-flex;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .top-center {
      display: flex;
      justify-content: center;
      align-items: center;
      padding-left: 18px;
    }

    .brand {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-family: "Arial Black", "Segoe UI", sans-serif;
      font-weight: 900;
      font-size: clamp(1.55rem, 2.2vw, 2.05rem);
      line-height: 1;
      letter-spacing: 0.02em;
      user-select: none;
    }
    .brand-next { color: #ffffff; }
    .brand-scout { color: #df1f36; }
    .brand-slash {
      color: #df1f36;
      font-weight: 900;
      display: inline-block;
      width: 24px;
      text-align: center;
    }

    .btn {
      border: 1px solid #3a3a3a;
      background: #111111;
      color: #f5f5f5;
      text-decoration: none;
      padding: 10px 14px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 0.9rem;
      cursor: pointer;
      font-family: "Arial Black", "Segoe UI", sans-serif;
      letter-spacing: 0.02em;
    }

    .btn-primary {
      background: #f5f5f5;
      color: #0a0a0a;
      border-color: #f5f5f5;
    }

    .live-btn {
      margin-left: 2px;
      padding: 4px 8px;
      font-size: 0.7rem;
      border-radius: 5px;
      background: #df1f36;
      color: #ffffff;
      border-color: #df1f36;
      flex: 0 0 auto;
      font-weight: 800;
    }

    .search-box {
      margin-left: 0;
      width: 320px;
      height: 30px;
      border-radius: 6px;
      border: 1px solid #d9d9d9;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 8px 0 10px;
    }

    .search-box input {
      border: 0;
      outline: 0;
      width: 100%;
      font-size: 0.82rem;
      color: #303030;
      background: transparent;
      font-family: "Arial Black", "Segoe UI", sans-serif;
      letter-spacing: 0.01em;
    }

    .search-box input::placeholder {
      color: #8a8a8a;
    }

    .search-icon {
      color: #505050;
      font-size: 0.95rem;
      line-height: 1;
      margin-left: 6px;
    }

    .member-btn {
      margin-left: 0;
      padding: 4px 9px;
      font-size: 0.7rem;
      border-radius: 5px;
      background: #df1f36;
      color: #ffffff;
      border-color: #df1f36;
      flex: 0 0 auto;
      font-weight: 800;
    }

    .hero {
      margin-top: 18px;
      border: 1px solid var(--line);
      border-radius: 18px;
      background: linear-gradient(165deg, #111111, #0a0a0a);
      padding: 28px;
    }

    .hero h1 {
      margin: 0 0 10px;
      font-size: clamp(2rem, 4.8vw, 3rem);
      line-height: 1.05;
      max-width: 14ch;
    }

    .hero p {
      margin: 0;
      color: var(--muted);
      max-width: 58ch;
      line-height: 1.5;
    }

    .hero-actions {
      margin-top: 18px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .note {
      margin-top: 10px;
      font-size: 0.82rem;
      color: #8f8f8f;
    }

    @media (max-width: 720px) {
      .topbar {
        grid-template-columns: 1fr;
      }
      .top-center {
        order: 2;
        justify-content: flex-start;
      }
      .member-btn { order: 3; }
      .btn { flex: 1; text-align: center; }
      .search-box { width: 100%; }
      .hero { padding: 20px; }
    }
  </style>
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="top-left">
        <div class="brand">
          <span class="brand-slash">//</span>
          <span class="brand-next">NEXT</span><span class="brand-scout">SCOUT</span>
          <span class="brand-slash">//</span>
        </div>
        <a class="btn live-btn" href="/live-scores">Canli</a>
      </div>
      <div class="top-center">
        <label class="search-box" aria-label="Arama">
          <input type="search" placeholder="Arama" />
          <span class="search-icon">⌕</span>
        </label>
      </div>
      <a class="btn member-btn" href="/login">Uye Girisi</a>
    </header>

    <section class="hero">
      <h1>Oyuncular icin resmi ana sayfa</h1>
      <p>
        Burasi admin paneli degil. Oyuncu kayit ve giris akisi bu sayfadan ilerleyecek.
        Bir sonraki adimda butonlari senin istedigin gercek sayfalara baglayacagiz.
      </p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="#">Hemen Kaydol</a>
        <a class="btn" href="/login">Zaten Uyeliyim</a>
      </div>
      <div class="note">Admin paneli ayridir ve bu sayfadan acilmaz.</div>
    </section>
  </div>
</body>
</html>
