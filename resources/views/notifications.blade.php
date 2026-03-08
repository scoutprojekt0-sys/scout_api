<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bildirimler - NextScout</title>
    <style>
        body { font-family: Inter, Arial, sans-serif; background:#f8fafc; color:#1f2937; margin:0; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 24px; }
        .top { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        a { color:#2563eb; text-decoration:none; }
        .card { background:#fff; border:1px solid #dbeafe; border-radius:10px; padding:14px; margin-bottom:10px; }
        .title { font-weight:700; }
        .meta { font-size:12px; color:#64748b; margin-top:4px; }
        .badge { display:inline-block; font-size:11px; padding:2px 8px; border-radius:999px; margin-left:8px; }
        .unread { background:#dbeafe; color:#1d4ed8; }
        .read { background:#e2e8f0; color:#475569; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <h1>Bildirimler</h1>
        <a href="/">Anasayfaya Don</a>
    </div>
    <div id="list"></div>
</div>
<script>
(async function() {
  const el = document.getElementById('list');
  try {
    let res = await fetch('/api/notifications');
    let data = await res.json();
    if (!data.success) {
      // guest kullanicilar icin public feed
      res = await fetch('/api/notifications/public');
      data = await res.json();
    }
    const notifications = data.notifications || [];
    if (!notifications.length) {
      el.innerHTML = '<div class="card">Bildirim bulunmuyor.</div>';
      return;
    }
    el.innerHTML = notifications.map(n => `
      <div class="card">
        <div class="title">${n.title || ''}
          <span class="badge ${n.read ? 'read' : 'unread'}">${n.read ? 'Okundu' : 'Yeni'}</span>
        </div>
        <div>${n.message || ''}</div>
        <div class="meta">${n.time || ''}</div>
      </div>
    `).join('');
  } catch (e) {
    el.innerHTML = '<div class="card">Bildirim verisi alinamadi.</div>';
  }
})();
</script>
</body>
</html>
