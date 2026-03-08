<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canli Maclar - NextScout</title>
    <style>
        body { font-family: Inter, Arial, sans-serif; background:#f8fafc; color:#1f2937; margin:0; }
        .wrap { max-width: 1000px; margin: 0 auto; padding: 24px; }
        .top { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        a { color:#2563eb; text-decoration:none; }
        .card { background:#fff; border:1px solid #dbeafe; border-radius:10px; padding:16px; margin-bottom:12px; }
        .league { font-size:12px; color:#64748b; }
        .teams { font-weight:700; margin:6px 0; }
        .score { color:#dc2626; font-weight:800; }
        .meta { font-size:12px; color:#475569; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <h1>Canli Maclar</h1>
        <a href="/">Anasayfaya Don</a>
    </div>
    <div id="list"></div>
</div>
<script>
(async function() {
  const el = document.getElementById('list');
  try {
    const res = await fetch('/api/live-matches');
    const data = await res.json();
    const matches = data.matches || [];
    if (!matches.length) {
      el.innerHTML = '<div class="card">Su an canli mac yok.</div>';
      return;
    }
    el.innerHTML = matches.map(m => `
      <div class="card">
        <div class="league">${m.league || ''}</div>
        <div class="teams">${m.home_team || ''} - ${m.away_team || ''}</div>
        <div class="score">${m.home_score ?? '-'} : ${m.away_score ?? '-'}</div>
        <div class="meta">Dakika: ${m.minute || '-'} | Durum: ${m.status || '-'}</div>
      </div>
    `).join('');
  } catch (e) {
    el.innerHTML = '<div class="card">Canli mac verisi alinamadi.</div>';
  }
})();
</script>
</body>
</html>
