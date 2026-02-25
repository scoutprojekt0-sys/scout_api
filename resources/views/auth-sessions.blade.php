<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Session Manager</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main class="ds-page ds-stack">
            <header class="ds-stack">
                <h1 class="ds-h1">Session / Device Manager</h1>
                <p class="ds-caption">Aktif oturumlari goruntule, tekli oturum kapat veya tum diger cihazlardan cikis yap.</p>
            </header>

            <section class="ds-card ds-stack">
                <div class="ds-field">
                    <label class="ds-label" for="token">Bearer Token</label>
                    <input id="token" class="ds-input" type="text" placeholder="1|xxxxxxxx..." autocomplete="off">
                    <p class="ds-help">Token sadece bu sayfada request icin kullanilir, depolanmaz.</p>
                </div>
                <div class="ds-button-row">
                    <button class="ds-btn ds-btn-primary" id="loadBtn" type="button">Load Sessions</button>
                    <button class="ds-btn ds-btn-secondary" id="refreshBtn" type="button">Refresh Current Session</button>
                    <button class="ds-btn ds-btn-secondary" id="logoutOthersBtn" type="button">Logout Other Devices</button>
                </div>
                <div id="status"></div>
            </section>

            <section class="ds-card ds-stack">
                <h2 class="ds-h2">Active Sessions</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr>
                                <th class="p-2">Device</th>
                                <th class="p-2">IP</th>
                                <th class="p-2">User Agent</th>
                                <th class="p-2">Last Active</th>
                                <th class="p-2">Action</th>
                            </tr>
                        </thead>
                        <tbody id="sessionRows">
                            <tr><td colspan="5" class="p-2 ds-caption">Henuz veri yok.</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <script>
            const tokenInput = document.getElementById('token');
            const loadBtn = document.getElementById('loadBtn');
            const refreshBtn = document.getElementById('refreshBtn');
            const logoutOthersBtn = document.getElementById('logoutOthersBtn');
            const sessionRows = document.getElementById('sessionRows');
            const statusBox = document.getElementById('status');

            function authHeaders() {
                const token = tokenInput.value.trim();
                return {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                };
            }

            function setStatus(type, text) {
                const klass = type === 'error' ? 'ds-alert ds-alert-danger'
                    : type === 'warn' ? 'ds-alert ds-alert-warning'
                    : 'ds-alert ds-alert-success';
                statusBox.innerHTML = `<div class="${klass}">${text}</div>`;
            }

            function fmtDate(value) {
                if (!value) return '-';
                const date = new Date(value);
                return Number.isNaN(date.getTime()) ? value : date.toLocaleString();
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            async function loadSessions() {
                if (!tokenInput.value.trim()) {
                    setStatus('warn', 'Bearer token girin.');
                    return;
                }

                const res = await fetch('/api/auth/sessions', { headers: authHeaders() });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    setStatus('error', data.message || 'Sessions yuklenemedi.');
                    return;
                }

                const rows = (data.data || []).map((session) => {
                    const revokeBtn = session.is_current
                        ? '<span class="ds-caption">Current</span>'
                        : `<button class="ds-btn ds-btn-secondary revoke-btn" data-id="${session.id}" type="button">Revoke</button>`;
                    return `
                        <tr>
                            <td class="p-2">${escapeHtml(session.device_label || '-')}</td>
                            <td class="p-2">${escapeHtml(session.ip_address || '-')}</td>
                            <td class="p-2">${escapeHtml(session.user_agent || '-')}</td>
                            <td class="p-2">${escapeHtml(fmtDate(session.last_used_at || session.created_at))}</td>
                            <td class="p-2">${revokeBtn}</td>
                        </tr>
                    `;
                });

                sessionRows.innerHTML = rows.length ? rows.join('') : '<tr><td colspan="5" class="p-2 ds-caption">Aktif oturum yok.</td></tr>';
                setStatus('success', 'Session listesi guncellendi.');
            }

            async function refreshCurrent() {
                if (!confirm('Mevcut oturumu yenilemek istiyor musun? Eski token gecersiz olacak.')) return;

                const res = await fetch('/api/auth/refresh', {
                    method: 'POST',
                    headers: authHeaders(),
                    body: '{}'
                });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    setStatus('error', data.message || 'Session yenilenemedi.');
                    return;
                }

                tokenInput.value = data?.data?.token || tokenInput.value;
                setStatus('success', 'Session yenilendi. Token kutusu guncellendi.');
                await loadSessions();
            }

            async function logoutOthers() {
                if (!confirm('Diger tum cihazlardan cikis yapilsin mi?')) return;

                const res = await fetch('/api/auth/sessions', {
                    method: 'DELETE',
                    headers: authHeaders()
                });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    setStatus('error', data.message || 'Diger cihazlar kapatilamadi.');
                    return;
                }

                setStatus('success', `Tamamlandi. Revoked: ${data?.data?.revoked_count ?? 0}`);
                await loadSessions();
            }

            sessionRows.addEventListener('click', async (event) => {
                const button = event.target.closest('.revoke-btn');
                if (!button) return;

                const id = button.getAttribute('data-id');
                if (!id) return;
                if (!confirm(`Session #${id} sonlandirilsin mi?`)) return;

                const res = await fetch(`/api/auth/sessions/${id}`, {
                    method: 'DELETE',
                    headers: authHeaders()
                });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    setStatus('error', data.message || 'Oturum sonlandirilamadi.');
                    return;
                }

                setStatus('success', `Session #${id} sonlandirildi.`);
                await loadSessions();
            });

            loadBtn.addEventListener('click', loadSessions);
            refreshBtn.addEventListener('click', refreshCurrent);
            logoutOthersBtn.addEventListener('click', logoutOthers);
        </script>
    </body>
</html>
