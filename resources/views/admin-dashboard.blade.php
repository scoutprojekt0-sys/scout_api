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
                --bg: #0b0b0c;
                --surface: #141417;
                --surface-soft: #1b1b21;
                --ink: #f2f2f4;
                --muted: #aaaab4;
                --line: #2d2d35;
                --red: #df1f36;
                --red-strong: #b41227;
                --white: #ffffff;
                --shadow: 0 18px 38px rgba(0, 0, 0, 0.38);
            }

            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: "Segoe UI", Tahoma, sans-serif;
                background:
                    radial-gradient(circle at 8% 8%, rgba(223, 31, 54, 0.22), transparent 34%),
                    radial-gradient(circle at 92% 92%, rgba(223, 31, 54, 0.12), transparent 30%),
                    var(--bg);
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
                background: linear-gradient(145deg, var(--red), #ff4e63);
                display: grid;
                place-items: center;
                font-size: 14px;
                font-weight: 700;
                color: var(--white);
            }

            .nav-list {
                display: grid;
                gap: 8px;
            }

            .nav-item {
                border: 1px solid transparent;
                background: var(--surface-soft);
                color: #d2d2db;
                border-radius: 10px;
                padding: 10px 12px;
                text-align: left;
                font-weight: 600;
                cursor: pointer;
                transition: 150ms ease;
            }

            .nav-item.active {
                background: linear-gradient(120deg, var(--red), var(--red-strong));
                color: var(--white);
                border-color: transparent;
            }

            .nav-item:hover {
                border-color: #43434f;
            }

            .support-box {
                margin-top: 8px;
                border: 1px solid #353541;
                border-radius: 12px;
                padding: 12px;
                background: #17171d;
                display: grid;
                gap: 8px;
            }

            .support-box button {
                border: 0;
                border-radius: 8px;
                padding: 10px 12px;
                background: var(--red);
                color: var(--white);
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
                background: #101015;
                color: var(--white);
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
                background: #101015;
                color: var(--white);
            }

            .btn {
                border: 1px solid transparent;
                border-radius: 10px;
                padding: 10px 12px;
                font-weight: 700;
                cursor: pointer;
            }

            .btn-primary {
                background: linear-gradient(120deg, var(--red), var(--red-strong));
                color: var(--white);
            }

            .btn-secondary {
                background: #1a1a20;
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
                background: linear-gradient(180deg, #1a1a20, #151519);
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
                color: var(--white);
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
                background: #191920;
                color: #d0d0d7;
                font-weight: 700;
            }

            .tab-btn.active {
                background: #29161a;
                border-color: #603039;
                color: #ffd5db;
            }

            .board-wrap {
                display: grid;
                gap: 10px;
            }

            .team-card {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 12px;
                background: #18181d;
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

            .chip-open { background: #1c3724; color: #97f4ad; }
            .chip-closed { background: #3a151b; color: #ff95a4; }
            .chip-pending { background: #3a311a; color: #f4d78d; }

            .tiny {
                font-size: 12px;
                color: var(--muted);
            }

            .progress {
                width: 100%;
                height: 8px;
                border-radius: 999px;
                background: #262630;
                overflow: hidden;
            }

            .progress > span {
                display: block;
                height: 100%;
                border-radius: 999px;
                background: linear-gradient(90deg, #ff5e6f, var(--red));
            }

            .report-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .player-tools {
                display: grid;
                grid-template-columns: repeat(6, minmax(0, 1fr));
                gap: 8px;
            }

            .player-tools input,
            .player-tools select {
                border: 1px solid var(--line);
                border-radius: 8px;
                padding: 9px 10px;
                background: #101015;
                color: var(--white);
                width: 100%;
            }

            .player-table-wrap {
                border: 1px solid var(--line);
                border-radius: 12px;
                overflow: auto;
                background: #17171d;
            }

            .player-table {
                width: 100%;
                border-collapse: collapse;
                min-width: 820px;
            }

            .player-table th,
            .player-table td {
                text-align: left;
                padding: 10px;
                border-bottom: 1px solid #24242c;
                font-size: 13px;
            }

            .player-table th {
                color: #bdbdc8;
                font-weight: 700;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                background: #1b1b23;
            }

            .player-row-btn {
                border: 1px solid #383845;
                border-radius: 8px;
                background: #1d1d25;
                color: #f2f2f4;
                padding: 6px 9px;
                font-weight: 700;
                cursor: pointer;
            }

            .player-pager {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 8px;
            }

            .player-detail {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 12px;
                background: #17171d;
                display: grid;
                gap: 8px;
            }

            .report-card {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 12px;
                background: #18181d;
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
                background: #262630;
                border-radius: 999px;
                height: 8px;
                overflow: hidden;
            }

            .bar-track > span {
                display: block;
                height: 100%;
                background: linear-gradient(90deg, #ff5e6f, var(--red));
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
                background: #2f2f38;
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
                background: #101015;
                color: var(--white);
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
                background: #1a1a22;
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
                .player-tools { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
        </style>
    </head>
    <body>
        <div class="admin-shell">
            <aside class="panel sidebar">
                <div class="brand">
                    <div class="brand-badge">SZ</div>
                    <div>ScoutZone Command</div>
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
                    <span class="tiny">Live ops assistance for scouting cycle.</span>
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

                <div class="status" id="statusBox" role="status" aria-live="polite">Command center is waiting for a token.</div>

                <section class="kpi-grid" id="kpiGrid">
                    <article class="kpi"><span class="kpi-label">Total Players</span><span class="kpi-value" id="kpiPlayers">-</span></article>
                    <article class="kpi"><span class="kpi-label">Filtered Players</span><span class="kpi-value" id="kpiFilteredPlayers">-</span></article>
                    <article class="kpi"><span class="kpi-label">Open Opportunities</span><span class="kpi-value" id="kpiOpen">-</span></article>
                    <article class="kpi"><span class="kpi-label">Pending Applications</span><span class="kpi-value" id="kpiPending">-</span></article>
                </section>

                <div class="tiny">Live board for planning, assignment, and reporting across opportunities.</div>

                <div class="tab-row">
                    <button class="tab-btn" id="tabPlayersBtn" type="button">Players</button>
                    <button class="tab-btn active" id="tabPlanBtn" type="button">Scouting plan</button>
                    <button class="tab-btn" id="tabReportBtn" type="button">Report statistics</button>
                </div>

                <section id="tabPlayers" class="board-wrap hidden" aria-live="polite">
                    <div class="player-tools">
                        <input id="playerPositionFilter" type="text" placeholder="Position (e.g. ST)">
                        <input id="playerCityFilter" type="text" placeholder="City">
                        <input id="playerAgeMinFilter" type="number" min="10" max="60" placeholder="Min age">
                        <input id="playerAgeMaxFilter" type="number" min="10" max="60" placeholder="Max age">
                        <select id="playerPerPageFilter">
                            <option value="10">10 / page</option>
                            <option value="20" selected>20 / page</option>
                            <option value="50">50 / page</option>
                        </select>
                        <button id="playerFilterBtn" class="btn btn-secondary" type="button">Apply Filters</button>
                    </div>
                    <div class="player-table-wrap" id="playersTableWrap"></div>
                    <div class="player-pager">
                        <div class="tiny" id="playersPaginationInfo">Page -</div>
                        <div>
                            <button id="playersPrevBtn" class="btn btn-secondary" type="button">Prev</button>
                            <button id="playersNextBtn" class="btn btn-secondary" type="button">Next</button>
                        </div>
                    </div>
                    <article class="player-detail" id="playerDetailBox">
                        <strong>Player Detail</strong>
                        <span class="tiny">Select a player row to inspect profile details.</span>
                    </article>
                </section>

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
                    <strong>Assign Player Review</strong>
                    <select id="taskOpportunity">
                        <option value="">Select opportunity</option>
                    </select>
                    <select id="taskScout">
                        <option value="">Select scout</option>
                    </select>
                    <select id="taskPlayer">
                        <option value="">Select player</option>
                    </select>
                    <input id="taskNote" type="text" maxlength="180" placeholder="Task note">
                    <button id="taskSaveBtn" class="btn btn-primary" type="button">Create Task</button>
                    <span class="tiny" id="taskStatus">Assignments are local demo notes for now.</span>
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
                players: [],
                playersMeta: {
                    current_page: 1,
                    last_page: 1,
                    total: 0,
                    per_page: 20,
                },
                playersFilters: {
                    position: '',
                    city: '',
                    ageMin: '',
                    ageMax: '',
                    perPage: 20,
                },
                applications: [],
                inbox: [],
                media: [],
                scouts: [],
            };

            const statusBox = document.getElementById('statusBox');
            const tabPlayersBtn = document.getElementById('tabPlayersBtn');
            const tabPlanBtn = document.getElementById('tabPlanBtn');
            const tabReportBtn = document.getElementById('tabReportBtn');
            const tabPlayers = document.getElementById('tabPlayers');
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

            function playerAgeFromBirthYear(birthYear) {
                if (!birthYear) return '-';
                const year = Number(birthYear);
                if (!Number.isFinite(year)) return '-';
                return new Date().getFullYear() - year;
            }

            async function loadPlayers(page = 1) {
                const params = new URLSearchParams();
                params.set('page', String(page));
                params.set('per_page', String(state.playersFilters.perPage || 20));
                if (state.playersFilters.position) params.set('position', state.playersFilters.position);
                if (state.playersFilters.city) params.set('city', state.playersFilters.city);
                if (state.playersFilters.ageMin) params.set('age_min', state.playersFilters.ageMin);
                if (state.playersFilters.ageMax) params.set('age_max', state.playersFilters.ageMax);

                const playersRes = await api(`/api/players?${params.toString()}`);
                const payload = playersRes?.data || {};
                state.players = payload.data || [];
                state.playersMeta = {
                    current_page: Number(payload.current_page || 1),
                    last_page: Number(payload.last_page || 1),
                    total: Number(payload.total || 0),
                    per_page: Number(payload.per_page || state.playersFilters.perPage || 20),
                };
                renderPlayers();
            }

            function renderPlayers() {
                const wrap = document.getElementById('playersTableWrap');
                const rows = state.players || [];

                if (!rows.length) {
                    wrap.innerHTML = '<div class="tiny" style="padding:12px;">No players found with current filters.</div>';
                } else {
                    wrap.innerHTML = `
                        <table class="player-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Age</th>
                                    <th>City</th>
                                    <th>Team</th>
                                    <th>Foot</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows.map((player) => `
                                    <tr>
                                        <td>#${player.id}</td>
                                        <td>${player.name || '-'}</td>
                                        <td>${player.position || '-'}</td>
                                        <td>${playerAgeFromBirthYear(player.birth_year)}</td>
                                        <td>${player.city || '-'}</td>
                                        <td>${player.current_team || '-'}</td>
                                        <td>${player.dominant_foot || '-'}</td>
                                        <td><button class="player-row-btn" type="button" data-player-id="${player.id}">Inspect</button></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    `;
                }

                const meta = state.playersMeta;
                document.getElementById('playersPaginationInfo').textContent =
                    `Page ${meta.current_page} / ${meta.last_page}  Total: ${meta.total}`;
                document.getElementById('kpiPlayers').textContent = meta.total;
                document.getElementById('kpiFilteredPlayers').textContent = rows.length;

                const taskPlayer = document.getElementById('taskPlayer');
                taskPlayer.innerHTML = '<option value="">Select player</option>' + rows.map((player) => `<option value="${player.id}">${player.name || 'Player'} (#${player.id})</option>`).join('');
            }

            async function inspectPlayer(playerId) {
                try {
                    const playerRes = await api(`/api/players/${playerId}`);
                    const player = playerRes?.data || {};
                    document.getElementById('playerDetailBox').innerHTML = `
                        <strong>Player Detail #${player.id || '-'}</strong>
                        <span class="tiny"><b>Name:</b> ${player.name || '-'}</span>
                        <span class="tiny"><b>Email:</b> ${player.email || '-'}</span>
                        <span class="tiny"><b>Position:</b> ${player.position || '-'}</span>
                        <span class="tiny"><b>Age:</b> ${playerAgeFromBirthYear(player.birth_year)}</span>
                        <span class="tiny"><b>City:</b> ${player.city || '-'}</span>
                        <span class="tiny"><b>Current Team:</b> ${player.current_team || '-'}</span>
                        <span class="tiny"><b>Height/Weight:</b> ${player.height_cm || '-'} cm / ${player.weight_kg || '-'} kg</span>
                        <span class="tiny"><b>Bio:</b> ${player.bio || '-'}</span>
                    `;
                } catch (error) {
                    document.getElementById('playerDetailBox').innerHTML = `<strong>Player Detail</strong><span class="tiny">${error.message || 'Unable to load player detail.'}</span>`;
                }
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
                    await loadPlayers(1);

                    renderPlan();
                    renderKpiAndReports();
                    renderRightPanel();
                    setStatus('Dashboard loaded.');
                } catch (error) {
                    setStatus(error.message || 'Dashboard load failed.', true);
                }
            }

            function collectPlayerFilters() {
                state.playersFilters.position = String(document.getElementById('playerPositionFilter').value || '').trim();
                state.playersFilters.city = String(document.getElementById('playerCityFilter').value || '').trim();
                state.playersFilters.ageMin = String(document.getElementById('playerAgeMinFilter').value || '').trim();
                state.playersFilters.ageMax = String(document.getElementById('playerAgeMaxFilter').value || '').trim();
                state.playersFilters.perPage = Number(document.getElementById('playerPerPageFilter').value || 20);
            }

            function activatePlayersTab() {
                tabPlayersBtn.classList.add('active');
                tabPlanBtn.classList.remove('active');
                tabReportBtn.classList.remove('active');
                tabPlayers.classList.remove('hidden');
                tabPlan.classList.add('hidden');
                tabReport.classList.add('hidden');
            }

            function activatePlanTab() {
                tabPlayersBtn.classList.remove('active');
                tabPlanBtn.classList.add('active');
                tabReportBtn.classList.remove('active');
                tabPlayers.classList.add('hidden');
                tabPlan.classList.remove('hidden');
                tabReport.classList.add('hidden');
            }

            function activateReportTab() {
                tabPlayersBtn.classList.remove('active');
                tabReportBtn.classList.add('active');
                tabPlanBtn.classList.remove('active');
                tabPlayers.classList.add('hidden');
                tabReport.classList.remove('hidden');
                tabPlan.classList.add('hidden');
            }

            document.getElementById('loadBtn').addEventListener('click', loadDashboard);
            tabPlayersBtn.addEventListener('click', activatePlayersTab);
            tabPlanBtn.addEventListener('click', activatePlanTab);
            tabReportBtn.addEventListener('click', activateReportTab);
            document.getElementById('playerFilterBtn').addEventListener('click', async () => {
                try {
                    collectPlayerFilters();
                    await loadPlayers(1);
                    setStatus('Player list refreshed.');
                } catch (error) {
                    setStatus(error.message || 'Player list refresh failed.', true);
                }
            });
            document.getElementById('playersPrevBtn').addEventListener('click', async () => {
                const page = state.playersMeta.current_page - 1;
                if (page < 1) return;
                await loadPlayers(page);
            });
            document.getElementById('playersNextBtn').addEventListener('click', async () => {
                const page = state.playersMeta.current_page + 1;
                if (page > state.playersMeta.last_page) return;
                await loadPlayers(page);
            });
            document.getElementById('playersTableWrap').addEventListener('click', async (event) => {
                const button = event.target.closest('[data-player-id]');
                if (!button) return;
                const playerId = Number(button.getAttribute('data-player-id'));
                if (!playerId) return;
                await inspectPlayer(playerId);
            });

            document.getElementById('taskSaveBtn').addEventListener('click', () => {
                const opp = document.getElementById('taskOpportunity').value;
                const scout = document.getElementById('taskScout').value;
                const player = document.getElementById('taskPlayer').value;
                const note = document.getElementById('taskNote').value.trim();
                if (!opp || !scout || !player) {
                    document.getElementById('taskStatus').textContent = 'Select opportunity, scout and player first.';
                    return;
                }
                document.getElementById('taskStatus').textContent = `Assignment saved: player #${player} -> scout #${scout} on opportunity #${opp}${note ? `: ${note}` : ''}`;
                document.getElementById('taskNote').value = '';
            });

            document.getElementById('globalSearch').addEventListener('input', (event) => {
                const query = String(event.target.value || '').trim().toLowerCase();
                if (!query) {
                    if (tabPlayersBtn.classList.contains('active')) {
                        renderPlayers();
                        return;
                    }
                    renderPlan();
                    return;
                }

                if (tabPlayersBtn.classList.contains('active')) {
                    const filtered = state.players.filter((player) => {
                        const hay = [
                            player.name,
                            player.city,
                            player.position,
                            player.current_team,
                            player.dominant_foot,
                        ].map((v) => String(v || '').toLowerCase()).join(' ');
                        return hay.includes(query);
                    });
                    const backup = state.players;
                    state.players = filtered;
                    renderPlayers();
                    state.players = backup;
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
