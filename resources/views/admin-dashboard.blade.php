<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Scout Admin Dashboard</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            :root {
                --bg: #f4f6fb;
                --surface: #ffffff;
                --ink: #1d2440;
                --muted: #6b7288;
                --line: #dde3f0;
                --blue: #5f67c7;
                --blue-strong: #454ea8;
                --green: #79df63;
                --yellow: #f3c247;
                --violet: #a6a3de;
                --danger: #d9415f;
                --shadow: 0 16px 36px rgba(27, 35, 77, 0.1);
            }

            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: "Segoe UI", Tahoma, sans-serif;
                background: radial-gradient(circle at 0% 0%, #e8ebff 0%, var(--bg) 45%, #f7f8fc 100%);
                color: var(--ink);
            }

            .admin-shell {
                min-height: 100vh;
                padding: 20px;
                display: grid;
                grid-template-columns: 240px 1fr 330px;
                gap: 16px;
            }

            .panel {
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: 16px;
                box-shadow: var(--shadow);
            }

            .sidebar {
                padding: 16px;
                display: grid;
                gap: 14px;
                align-content: start;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 700;
            }

            .brand-badge {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                background: linear-gradient(145deg, var(--green), #9df88a);
                display: grid;
                place-items: center;
                font-size: 14px;
                font-weight: 700;
            }

            .nav-list {
                display: grid;
                gap: 8px;
            }

            .nav-item {
                border: 1px solid transparent;
                background: #f5f7ff;
                color: #454d72;
                border-radius: 10px;
                padding: 10px 12px;
                text-align: left;
                font-weight: 600;
                cursor: pointer;
            }

            .nav-item.active {
                background: var(--blue);
                color: #fff;
            }

            .support-box {
                margin-top: 8px;
                border: 1px solid #cad2ec;
                border-radius: 12px;
                padding: 12px;
                background: #eef2ff;
                display: grid;
                gap: 8px;
            }

            .support-box button {
                border: 0;
                border-radius: 8px;
                padding: 10px 12px;
                background: var(--green);
                color: #134200;
                font-weight: 700;
                cursor: pointer;
            }

            .main {
                padding: 16px;
                display: grid;
                gap: 14px;
                align-content: start;
            }

            .top-row {
                display: flex;
                gap: 10px;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .search {
                min-width: 240px;
                max-width: 340px;
                width: 100%;
                border: 1px solid var(--line);
                border-radius: 10px;
                padding: 10px 12px;
                outline: none;
            }

            .token-row {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
            }

            .token-row input {
                min-width: 230px;
                flex: 1;
                border: 1px solid var(--line);
                border-radius: 10px;
                padding: 10px 12px;
                outline: none;
            }

            .btn {
                border: 1px solid transparent;
                border-radius: 10px;
                padding: 10px 12px;
                font-weight: 700;
                cursor: pointer;
            }

            .btn-primary {
                background: var(--blue);
                color: #fff;
            }

            .btn-secondary {
                background: #fff;
                border-color: var(--line);
                color: var(--ink);
            }

            .status {
                font-size: 13px;
                color: var(--muted);
            }

            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 10px;
            }

            .kpi {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 12px;
                background: #fbfcff;
                display: grid;
                gap: 6px;
            }

            .kpi-label {
                font-size: 12px;
                color: var(--muted);
                font-weight: 600;
            }

            .kpi-value {
                font-size: 24px;
                font-weight: 800;
                line-height: 1;
            }

            .tab-row {
                display: flex;
                gap: 8px;
                border-bottom: 1px solid var(--line);
                padding-bottom: 10px;
            }

            .tab-btn {
                border: 1px solid var(--line);
                border-radius: 999px;
                padding: 8px 12px;
                cursor: pointer;
                background: #fff;
                color: #4f567b;
                font-weight: 700;
            }

            .tab-btn.active {
                background: #e9ecff;
                border-color: #c9d0ff;
                color: var(--blue-strong);
            }

            .board-wrap {
                display: grid;
                gap: 10px;
            }

            .team-card {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 12px;
                background: #fff;
                display: grid;
                gap: 8px;
            }

            .team-head {
                display: flex;
                justify-content: space-between;
                gap: 8px;
                align-items: center;
            }

            .chip {
                border-radius: 999px;
                padding: 4px 8px;
                font-size: 11px;
                font-weight: 700;
            }

            .chip-open { background: #e8ffe4; color: #1f6f00; }
            .chip-closed { background: #ffe9ee; color: #a3002e; }
            .chip-pending { background: #fff6de; color: #8a5f00; }

            .tiny {
                font-size: 12px;
                color: var(--muted);
            }

            .progress {
                width: 100%;
                height: 8px;
                border-radius: 999px;
                background: #edf1fb;
                overflow: hidden;
            }

            .progress > span {
                display: block;
                height: 100%;
                border-radius: 999px;
                background: linear-gradient(90deg, var(--blue), var(--green));
            }

            .report-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .report-card {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 12px;
                background: #fff;
                display: grid;
                gap: 8px;
            }

            .bar-row {
                display: grid;
                gap: 6px;
            }

            .bar-line {
                display: grid;
                grid-template-columns: 90px 1fr 36px;
                gap: 8px;
                align-items: center;
                font-size: 12px;
            }

            .bar-track {
                background: #edf1fb;
                border-radius: 999px;
                height: 8px;
                overflow: hidden;
            }

            .bar-track > span {
                display: block;
                height: 100%;
                background: var(--violet);
            }

            .rightbar {
                padding: 16px;
                display: grid;
                gap: 12px;
                align-content: start;
            }

            .profile {
                display: flex;
                gap: 10px;
                align-items: center;
                padding-bottom: 8px;
                border-bottom: 1px solid var(--line);
            }

            .avatar {
                width: 42px;
                height: 42px;
                border-radius: 50%;
                background: #d9def3;
                display: grid;
                place-items: center;
                font-weight: 700;
            }

            .task-box,
            .scout-box {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 12px;
                display: grid;
                gap: 8px;
            }

            .task-box select,
            .task-box input {
                border: 1px solid var(--line);
                border-radius: 8px;
                padding: 9px 10px;
            }

            .scout-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 8px;
            }

            .scout-pill {
                border: 1px solid var(--line);
                border-radius: 10px;
                padding: 8px;
                text-align: center;
                font-size: 11px;
                background: #f7f8ff;
            }

            .hidden { display: none; }

            @media (max-width: 1220px) {
                .admin-shell { grid-template-columns: 220px 1fr; }
                .rightbar { grid-column: span 2; }
            }

            @media (max-width: 880px) {
                .admin-shell { grid-template-columns: 1fr; padding: 12px; }
                .sidebar, .rightbar, .main { grid-column: auto; }
                .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .report-grid { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <div class="admin-shell">
            <aside class="panel sidebar">
                <div class="brand">
                    <div class="brand-badge">SZ</div>
                    <div>ScoutZone Admin</div>
                </div>
                <div class="nav-list">
                    <button class="nav-item active" type="button">Dashboard</button>
                    <button class="nav-item" type="button">Reports</button>
                    <button class="nav-item" type="button">All Players</button>
                    <button class="nav-item" type="button">Settings</button>
                    <button class="nav-item" type="button">Notifications</button>
                    <button class="nav-item" type="button">Support</button>
                </div>
                <div class="support-box">
                    <strong>Support 24/7</strong>
                    <span class="tiny">Need help with scout operations?</span>
                    <button type="button">Start</button>
                </div>
            </aside>

            <main class="panel main">
                <div class="top-row">
                    <input id="globalSearch" class="search" type="search" placeholder="Search team, player, city...">
                    <div class="token-row">
                        <input id="tokenInput" type="text" placeholder="Bearer token (required)">
                        <button id="loadBtn" class="btn btn-primary" type="button">Load Dashboard</button>
                    </div>
                </div>

                <div class="status" id="statusBox" role="status" aria-live="polite">Dashboard is waiting for a token.</div>

                <section class="kpi-grid" id="kpiGrid">
                    <article class="kpi"><span class="kpi-label">Open Opportunities</span><span class="kpi-value" id="kpiOpen">-</span></article>
                    <article class="kpi"><span class="kpi-label">Pending Applications</span><span class="kpi-value" id="kpiPending">-</span></article>
                    <article class="kpi"><span class="kpi-label">Inbox Messages</span><span class="kpi-value" id="kpiInbox">-</span></article>
                    <article class="kpi"><span class="kpi-label">Media Assets</span><span class="kpi-value" id="kpiMedia">-</span></article>
                </section>

                <div class="tab-row">
                    <button class="tab-btn active" id="tabPlanBtn" type="button">Scouting plan</button>
                    <button class="tab-btn" id="tabReportBtn" type="button">Report statistics</button>
                </div>

                <section id="tabPlan" class="board-wrap" aria-live="polite"></section>

                <section id="tabReport" class="report-grid hidden" aria-live="polite">
                    <article class="report-card">
                        <strong>Application Status Distribution</strong>
                        <div class="bar-row" id="applicationBars"></div>
                    </article>
                    <article class="report-card">
                        <strong>Opportunity Status Distribution</strong>
                        <div class="bar-row" id="opportunityBars"></div>
                    </article>
                    <article class="report-card">
                        <strong>Top Cities</strong>
                        <div class="bar-row" id="cityBars"></div>
                    </article>
                    <article class="report-card">
                        <strong>Activity Snapshot</strong>
                        <div class="tiny" id="activitySnapshot">Load dashboard to see activity totals.</div>
                    </article>
                </section>
            </main>

            <aside class="panel rightbar">
                <div class="profile">
                    <div class="avatar" id="adminAvatar">A</div>
                    <div>
                        <strong id="adminName">Admin</strong>
                        <div class="tiny" id="adminRole">Role: -</div>
                    </div>
                </div>

                <section class="task-box">
                    <strong>Assign Task</strong>
                    <select id="taskOpportunity">
                        <option value="">Select opportunity</option>
                    </select>
                    <select id="taskScout">
                        <option value="">Select scout</option>
                    </select>
                    <input id="taskNote" type="text" maxlength="180" placeholder="Task note">
                    <button id="taskSaveBtn" class="btn btn-primary" type="button">Create Task</button>
                    <span class="tiny" id="taskStatus">Tasks are local demo notes for now.</span>
                </section>

                <section class="scout-box">
                    <strong>Scouting Team</strong>
                    <div class="scout-grid" id="scoutGrid"></div>
                </section>
            </aside>
        </div>

        <script>
            const state = {
                me: null,
                opportunities: [],
                applications: [],
                inbox: [],
                media: [],
                scouts: [],
            };

            const statusBox = document.getElementById('statusBox');
            const tabPlanBtn = document.getElementById('tabPlanBtn');
            const tabReportBtn = document.getElementById('tabReportBtn');
            const tabPlan = document.getElementById('tabPlan');
            const tabReport = document.getElementById('tabReport');
            const tokenInput = document.getElementById('tokenInput');

            function setStatus(text, isError = false) {
                statusBox.textContent = text;
                statusBox.style.color = isError ? '#b31e46' : '#6b7288';
            }

            function headers() {
                return {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${tokenInput.value.trim()}`,
                };
            }

            async function api(path) {
                const response = await fetch(path, { headers: headers() });
                const data = await response.json().catch(() => ({}));
                if (!response.ok || data.ok === false) {
                    throw new Error(data.message || `Request failed (${response.status})`);
                }
                return data;
            }

            function byKeyCount(rows, key) {
                const map = {};
                rows.forEach((row) => {
                    const raw = row && row[key] ? String(row[key]).toLowerCase() : 'unknown';
                    map[raw] = (map[raw] || 0) + 1;
                });
                return map;
            }

            function topCities(rows, cityKey) {
                const map = {};
                rows.forEach((row) => {
                    const c = row && row[cityKey] ? String(row[cityKey]).trim() : '';
                    if (!c) return;
                    map[c] = (map[c] || 0) + 1;
                });
                return map;
            }

            function renderBars(targetId, statsObj) {
                const target = document.getElementById(targetId);
                const entries = Object.entries(statsObj);
                if (!entries.length) {
                    target.innerHTML = '<div class="tiny">No data</div>';
                    return;
                }
                const max = Math.max(...entries.map(([, value]) => Number(value) || 0), 1);
                target.innerHTML = entries.map(([label, value]) => {
                    const width = Math.max(6, Math.round((Number(value) / max) * 100));
                    return `
                        <div class="bar-line">
                            <span>${label}</span>
                            <div class="bar-track"><span style="width:${width}%"></span></div>
                            <span>${value}</span>
                        </div>
                    `;
                }).join('');
            }

            function renderPlan() {
                const list = state.opportunities.slice(0, 14);
                if (!list.length) {
                    tabPlan.innerHTML = '<div class="tiny">No opportunities loaded for scouting plan.</div>';
                    return;
                }

                const byOpportunity = {};
                state.applications.forEach((app) => {
                    const key = app.opportunity_id || app.opportunity_title || 'unknown';
                    byOpportunity[key] = (byOpportunity[key] || 0) + 1;
                });

                tabPlan.innerHTML = list.map((opp) => {
                    const status = String(opp.status || 'pending').toLowerCase();
                    const chipClass = status === 'open' ? 'chip-open' : (status === 'closed' ? 'chip-closed' : 'chip-pending');
                    const appCount = byOpportunity[opp.id] || byOpportunity[opp.title] || 0;
                    const progress = status === 'open' ? 64 : (status === 'closed' ? 100 : 34);

                    return `
                        <article class="team-card">
                            <div class="team-head">
                                <strong>${opp.title || 'Untitled opportunity'}</strong>
                                <span class="chip ${chipClass}">${status}</span>
                            </div>
                            <span class="tiny">Team: ${opp.team_name || '-'} | City: ${opp.city || '-'}</span>
                            <span class="tiny">Applications: ${appCount} | Position: ${opp.position || '-'}</span>
                            <div class="progress"><span style="width:${progress}%"></span></div>
                        </article>
                    `;
                }).join('');
            }

            function renderRightPanel() {
                const me = state.me || {};
                const initials = (me.name || 'Admin').split(' ').map((p) => p[0] || '').join('').slice(0, 2).toUpperCase();
                document.getElementById('adminAvatar').textContent = initials || 'A';
                document.getElementById('adminName').textContent = me.name || 'Admin';
                document.getElementById('adminRole').textContent = `Role: ${me.role || '-'}`;

                const scoutGrid = document.getElementById('scoutGrid');
                const scouts = state.scouts.slice(0, 12);
                scoutGrid.innerHTML = scouts.length
                    ? scouts.map((scout) => `<div class="scout-pill">${scout.name || 'Scout'}<br><span class="tiny">${scout.organization || scout.city || '-'}</span></div>`).join('')
                    : '<span class="tiny">No scouts in list</span>';

                const taskOpportunity = document.getElementById('taskOpportunity');
                taskOpportunity.innerHTML = '<option value="">Select opportunity</option>' + state.opportunities.slice(0, 20).map((opp) => `<option value="${opp.id}">${opp.title || 'Untitled'} (#${opp.id})</option>`).join('');

                const taskScout = document.getElementById('taskScout');
                taskScout.innerHTML = '<option value="">Select scout</option>' + scouts.map((scout) => `<option value="${scout.id}">${scout.name || 'Scout'}</option>`).join('');
            }

            function renderKpiAndReports() {
                const oppByStatus = byKeyCount(state.opportunities, 'status');
                const appByStatus = byKeyCount(state.applications, 'status');
                const cities = topCities(state.opportunities, 'city');

                document.getElementById('kpiOpen').textContent = oppByStatus.open || 0;
                document.getElementById('kpiPending').textContent = appByStatus.pending || 0;
                document.getElementById('kpiInbox').textContent = state.inbox.length;
                document.getElementById('kpiMedia').textContent = state.media.length;

                renderBars('applicationBars', appByStatus);
                renderBars('opportunityBars', oppByStatus);
                renderBars('cityBars', cities);

                document.getElementById('activitySnapshot').textContent =
                    `Opportunities: ${state.opportunities.length}, Applications: ${state.applications.length}, Messages: ${state.inbox.length}, Media: ${state.media.length}, Scouts: ${state.scouts.length}`;
            }

            async function loadDashboard() {
                if (!tokenInput.value.trim()) {
                    setStatus('Bearer token is required.', true);
                    return;
                }

                setStatus('Loading dashboard data...');
                try {
                    const me = await api('/api/auth/me');
                    state.me = me.data || null;

                    const [opportunitiesRes, appIncomingRes, appOutgoingRes, inboxRes, mediaRes, scoutsRes] = await Promise.all([
                        api('/api/opportunities?per_page=100'),
                        api('/api/applications/incoming?per_page=100').catch(() => ({ data: { data: [] } })),
                        api('/api/applications/outgoing?per_page=100').catch(() => ({ data: { data: [] } })),
                        api('/api/contacts/inbox?per_page=100').catch(() => ({ data: { data: [] } })),
                        api(`/api/users/${state.me?.id || 0}/media?per_page=100`).catch(() => ({ data: { data: [] } })),
                        api('/api/staff?role_type=scout&per_page=60').catch(() => ({ data: { data: [] } })),
                    ]);

                    state.opportunities = opportunitiesRes?.data?.data || [];
                    const incoming = appIncomingRes?.data?.data || [];
                    const outgoing = appOutgoingRes?.data?.data || [];
                    state.applications = [...incoming, ...outgoing];
                    state.inbox = inboxRes?.data?.data || [];
                    state.media = mediaRes?.data?.data || [];
                    state.scouts = scoutsRes?.data?.data || [];

                    renderPlan();
                    renderKpiAndReports();
                    renderRightPanel();
                    setStatus('Dashboard loaded.');
                } catch (error) {
                    setStatus(error.message || 'Dashboard load failed.', true);
                }
            }

            function activatePlanTab() {
                tabPlanBtn.classList.add('active');
                tabReportBtn.classList.remove('active');
                tabPlan.classList.remove('hidden');
                tabReport.classList.add('hidden');
            }

            function activateReportTab() {
                tabReportBtn.classList.add('active');
                tabPlanBtn.classList.remove('active');
                tabReport.classList.remove('hidden');
                tabPlan.classList.add('hidden');
            }

            document.getElementById('loadBtn').addEventListener('click', loadDashboard);
            tabPlanBtn.addEventListener('click', activatePlanTab);
            tabReportBtn.addEventListener('click', activateReportTab);

            document.getElementById('taskSaveBtn').addEventListener('click', () => {
                const opp = document.getElementById('taskOpportunity').value;
                const scout = document.getElementById('taskScout').value;
                const note = document.getElementById('taskNote').value.trim();
                if (!opp || !scout) {
                    document.getElementById('taskStatus').textContent = 'Select opportunity and scout first.';
                    return;
                }
                document.getElementById('taskStatus').textContent = `Task recorded for scout #${scout} on opportunity #${opp}${note ? `: ${note}` : ''}`;
                document.getElementById('taskNote').value = '';
            });

            document.getElementById('globalSearch').addEventListener('input', (event) => {
                const query = String(event.target.value || '').trim().toLowerCase();
                if (!query) {
                    renderPlan();
                    return;
                }

                const filtered = state.opportunities.filter((opp) => {
                    const hay = [
                        opp.title,
                        opp.team_name,
                        opp.city,
                        opp.position,
                        opp.status,
                    ].map((v) => String(v || '').toLowerCase()).join(' ');
                    return hay.includes(query);
                });

                const backup = state.opportunities;
                state.opportunities = filtered;
                renderPlan();
                state.opportunities = backup;
            });
        </script>
    </body>
</html>
