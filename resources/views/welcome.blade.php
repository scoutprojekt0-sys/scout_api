<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ScoutZone Platform</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            :root {
                --bg: #0a0a0c;
                --bg-soft: #111115;
                --panel: #17171c;
                --line: #2a2a31;
                --ink: #f5f5f7;
                --muted: #acacb6;
                --red: #e0203a;
                --red-strong: #a80f24;
                --white: #ffffff;
            }

            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: "Segoe UI", Tahoma, sans-serif;
                color: var(--ink);
                background:
                    radial-gradient(circle at 8% 8%, rgba(224, 32, 58, 0.24), transparent 30%),
                    radial-gradient(circle at 92% 88%, rgba(224, 32, 58, 0.15), transparent 28%),
                    var(--bg);
            }

            .page {
                max-width: 1180px;
                margin: 0 auto;
                padding: 22px;
                display: grid;
                gap: 16px;
            }

            .topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 12px 14px;
                border: 1px solid var(--line);
                border-radius: 14px;
                background: rgba(23, 23, 28, 0.86);
                backdrop-filter: blur(5px);
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 800;
                letter-spacing: 0.2px;
            }

            .logo {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                display: grid;
                place-items: center;
                background: linear-gradient(130deg, var(--red), var(--red-strong));
                color: var(--white);
            }

            .top-links {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .btn {
                border: 1px solid var(--line);
                border-radius: 10px;
                padding: 10px 12px;
                font-weight: 700;
                text-decoration: none;
                color: var(--ink);
                background: #18181f;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 40px;
            }

            .btn-primary {
                border-color: transparent;
                background: linear-gradient(130deg, var(--red), var(--red-strong));
                color: var(--white);
            }

            .hero {
                border: 1px solid var(--line);
                border-radius: 18px;
                background:
                    linear-gradient(155deg, rgba(224, 32, 58, 0.15), transparent 36%),
                    var(--panel);
                padding: 28px;
                display: grid;
                grid-template-columns: 1.2fr 1fr;
                gap: 20px;
                align-items: center;
            }

            .eyebrow {
                color: #ffc6ce;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.8px;
                text-transform: uppercase;
            }

            .hero h1 {
                margin: 6px 0 10px;
                font-size: 38px;
                line-height: 1.05;
            }

            .hero p {
                margin: 0;
                color: var(--muted);
                line-height: 1.5;
                max-width: 580px;
            }

            .hero-actions {
                display: flex;
                gap: 8px;
                margin-top: 16px;
                flex-wrap: wrap;
            }

            .metrics {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px;
            }

            .metric {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 12px;
                background: #141419;
                display: grid;
                gap: 6px;
            }

            .metric strong {
                font-size: 24px;
            }

            .metric span {
                color: var(--muted);
                font-size: 12px;
            }

            .stack {
                display: grid;
                gap: 12px;
            }

            .roadmap {
                border: 1px solid var(--line);
                border-radius: 14px;
                padding: 14px;
                background: #141419;
            }

            .roadmap h3 {
                margin: 0 0 8px;
                font-size: 16px;
            }

            .timeline {
                display: grid;
                gap: 8px;
            }

            .timeline-item {
                border: 1px solid var(--line);
                border-radius: 10px;
                padding: 9px 10px;
                display: grid;
                grid-template-columns: auto 1fr;
                gap: 8px;
                align-items: center;
                background: #111116;
            }

            .dot {
                width: 9px;
                height: 9px;
                border-radius: 50%;
                background: var(--red);
            }

            .sections {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
            }

            .card {
                border: 1px solid var(--line);
                border-radius: 14px;
                padding: 14px;
                background: #131318;
                display: grid;
                gap: 8px;
            }

            .card h4 {
                margin: 0;
                font-size: 16px;
            }

            .card p {
                margin: 0;
                color: var(--muted);
                font-size: 13px;
                line-height: 1.45;
            }

            .footer-cta {
                border: 1px solid var(--line);
                border-radius: 14px;
                padding: 14px;
                background: #15151c;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .footer-cta span {
                color: var(--muted);
                font-size: 13px;
            }

            @media (max-width: 980px) {
                .hero { grid-template-columns: 1fr; }
                .sections { grid-template-columns: 1fr; }
            }

            @media (max-width: 760px) {
                .page { padding: 14px; }
                .hero { padding: 18px; }
                .hero h1 { font-size: 31px; }
                .metrics { grid-template-columns: 1fr; }
                .topbar { align-items: flex-start; flex-direction: column; }
                .top-links { width: 100%; }
                .btn { width: 100%; }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <header class="topbar">
                <div class="brand">
                    <div class="logo">SZ</div>
                    <span>ScoutZone Platform</span>
                </div>
                <nav class="top-links">
                    <a class="btn" href="/admin">Admin Console</a>
                    <a class="btn" href="/api/news/live">Live News API</a>
                    <a class="btn btn-primary" href="/admin">Open Dashboard</a>
                </nav>
            </header>

            <section class="hero">
                <div>
                    <span class="eyebrow">Scouting Operations Backbone</span>
                    <h1>Secure talent workflow for clubs, scouts, and players.</h1>
                    <p>
                        ScoutZone centralizes opportunity management, applications, messaging, and media proof
                        into one auditable and launch-ready operation layer.
                    </p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="/admin">Go to Admin</a>
                        <a class="btn" href="/api/news/live">Check Public API</a>
                    </div>
                </div>
                <div class="stack">
                    <div class="metrics">
                        <article class="metric">
                            <strong>8</strong>
                            <span>Week delivery cycle completed</span>
                        </article>
                        <article class="metric">
                            <strong>4</strong>
                            <span>CI gate families active</span>
                        </article>
                        <article class="metric">
                            <strong>24/7</strong>
                            <span>Operational readiness model</span>
                        </article>
                    </div>
                    <article class="roadmap">
                        <h3>Launch Cadence</h3>
                        <div class="timeline">
                            <div class="timeline-item"><span class="dot"></span><span>Pre-prod dry run and RC gate</span></div>
                            <div class="timeline-item"><span class="dot"></span><span>Final UAT + accessibility closure</span></div>
                            <div class="timeline-item"><span class="dot"></span><span>Go-live and week-1 monitoring</span></div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="sections">
                <article class="card">
                    <h4>Core Product Flow</h4>
                    <p>Role-based opportunities, applications, and profile operations with sorting, filtering, and pagination.</p>
                </article>
                <article class="card">
                    <h4>Communication + Media</h4>
                    <p>Inbox/sent workflow and media lifecycle management in a single consistent backend contract.</p>
                </article>
                <article class="card">
                    <h4>Security + Reliability</h4>
                    <p>Pentest-style tests, backup/rollback runbooks, monitoring alerts, and release gate governance.</p>
                </article>
            </section>

            <section class="footer-cta">
                <span>Ready for customer-facing demos and controlled production rollout.</span>
                <a class="btn btn-primary" href="/admin">Enter Admin Console</a>
            </section>
        </div>
    </body>
</html>
