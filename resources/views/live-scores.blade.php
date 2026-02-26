<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NextScout | Canli Skorlar</title>
  <style>
    :root {
      --bg: #070707;
      --panel: #111111;
      --line: #2e2e2e;
      --text: #f5f5f5;
      --muted: #aaaaaa;
      --red: #df1f36;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: "Segoe UI", Tahoma, sans-serif;
      background: radial-gradient(circle at 10% 10%, #171717 0%, transparent 30%), var(--bg);
      color: var(--text);
    }

    .page {
      width: min(1100px, 92%);
      margin: 0 auto;
      padding: 24px 0 40px;
      display: grid;
      gap: 14px;
    }

    .topbar {
      border: 1px solid #d6d6d6;
      border-radius: 14px;
      background: rgba(16, 16, 16, 0.95);
      padding: 12px 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .brand {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-family: "Arial Black", "Segoe UI", sans-serif;
      font-weight: 900;
      font-size: clamp(1.25rem, 2vw, 1.65rem);
      line-height: 1;
      letter-spacing: 0.02em;
      user-select: none;
    }

    .brand-next { color: #ffffff; }
    .brand-scout { color: var(--red); }
    .brand-slash { color: var(--red); width: 18px; text-align: center; }

    .btn {
      border: 1px solid #3a3a3a;
      background: #111111;
      color: #f5f5f5;
      text-decoration: none;
      padding: 10px 14px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 0.9rem;
    }

    .panel {
      border: 1px solid var(--line);
      border-radius: 16px;
      background: linear-gradient(165deg, #121212, #0b0b0b);
      padding: 18px;
      display: grid;
      gap: 12px;
    }

    h1 {
      margin: 0;
      font-size: clamp(1.4rem, 2.8vw, 2rem);
    }

    .status {
      margin: 0;
      color: var(--muted);
      font-size: 0.9rem;
    }

    .list {
      display: grid;
      gap: 10px;
    }

    .match {
      border: 1px solid #303030;
      border-radius: 12px;
      background: #121212;
      padding: 12px;
      display: grid;
      gap: 4px;
    }

    .teams {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      font-weight: 700;
    }

    .score {
      font-weight: 900;
      color: var(--red);
      min-width: 72px;
      text-align: center;
    }

    .meta {
      color: var(--muted);
      font-size: 0.82rem;
    }
  </style>
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="brand">
        <span class="brand-slash">//</span>
        <span class="brand-next">NEXT</span><span class="brand-scout">SCOUT</span>
        <span class="brand-slash">//</span>
      </div>
      <a class="btn" href="/">Ana Sayfa</a>
    </header>

    <section class="panel">
      <h1>Canli Mac Skorlari</h1>
      <p class="status" id="status">Skorlar yukleniyor...</p>
      <div class="list" id="match-list"></div>
    </section>
  </div>

  <script>
    (async function () {
      const status = document.getElementById('status');
      const list = document.getElementById('match-list');

      function renderFallback() {
        status.textContent = 'Canli servis su an erisilemiyor.';
        list.innerHTML = '<div class="match"><div class="meta">Su an gosterilecek canli mac bulunamadi.</div></div>';
      }

      try {
        const response = await fetch('https://www.thesportsdb.com/api/v1/json/3/livescore.php?s=Soccer');
        if (!response.ok) throw new Error('service_error');
        const payload = await response.json();
        const events = Array.isArray(payload?.events) ? payload.events : [];

        if (!events.length) {
          renderFallback();
          return;
        }

        status.textContent = events.length + ' canli mac listeleniyor.';
        list.innerHTML = events.slice(0, 20).map((event) => {
          const home = event.strHomeTeam || 'Home';
          const away = event.strAwayTeam || 'Away';
          const score = (event.intHomeScore ?? '-') + ' : ' + (event.intAwayScore ?? '-');
          const league = event.strLeague || 'League';
          const time = event.strTime || '';
          return `
            <article class="match">
              <div class="teams">
                <span>${home}</span>
                <span class="score">${score}</span>
                <span>${away}</span>
              </div>
              <div class="meta">${league}${time ? ' | ' + time : ''}</div>
            </article>
          `;
        }).join('');
      } catch (_) {
        renderFallback();
      }
    })();
  </script>
</body>
</html>
