<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NextScout - Scout Platform')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #0066cc;
            --primary-light: #0080ff;
            --primary-dark: #0052a3;
            --secondary: #667eea;
            --accent: #764ba2;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --gray: #e9ecef;
            --text-dark: #1a202c;
            --text-light: #6b7280;
            --shadow: 0 2px 8px rgba(0, 102, 204, 0.1);
            --shadow-lg: 0 10px 30px rgba(0, 102, 204, 0.15);
        }

        body {
            font-family: 'Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', sans-serif;
            background: var(--white);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* NAVBAR */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--gray);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        .logo-icon {
            font-size: 28px;
        }

        .nav-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-login {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-login:hover {
            background: var(--primary);
            color: var(--white);
        }

        .btn-signup {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            border: none;
        }

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
            color: var(--white);
            padding: 80px 40px;
            text-align: center;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 700px;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-hero-primary {
            background: var(--white);
            color: var(--primary);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            border: 2px solid var(--white);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* BUTTONS */
        .buttons-container {
            max-width: 1400px;
            margin: -60px auto 0;
            padding: 0 40px;
            position: relative;
            z-index: 10;
        }

        .buttons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 60px;
        }

        .button-card {
            background: var(--white);
            border: 2px solid var(--gray);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .button-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .button-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .button-card:hover::before {
            transform: scaleX(1);
        }

        .button-icon {
            font-size: 32px;
            margin-bottom: 12px;
            display: block;
        }

        .button-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }

        /* FEATURES */
        .features-section {
            background: var(--light-gray);
            padding: 80px 40px;
            margin-top: 40px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        .section-title p {
            font-size: 16px;
            color: var(--text-light);
            max-width: 500px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--white);
            border-radius: 12px;
            padding: 30px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border-left: 5px solid var(--primary);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-left-color: var(--secondary);
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 15px;
            display: inline-block;
        }

        .feature-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-dark);
        }

        .feature-card p {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.8;
        }

        /* STATS */
        .stats-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            padding: 60px 40px;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stat-box {
            padding: 20px;
        }

        .stat-number {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 14px;
            text-transform: uppercase;
            opacity: 0.9;
            letter-spacing: 1px;
        }

        /* CTA */
        .cta-section {
            padding: 80px 40px;
            text-align: center;
            background: var(--white);
        }

        .cta-content h2 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .cta-content p {
            font-size: 16px;
            color: var(--text-light);
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* FOOTER */
        .footer {
            background: var(--text-dark);
            color: var(--white);
            padding: 40px;
            text-align: center;
            border-top: 1px solid var(--gray);
        }

        .footer p {
            opacity: 0.8;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .footer-links {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--white);
            text-decoration: none;
            font-size: 13px;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .footer-links a:hover {
            opacity: 1;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .buttons-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 12px;
            }

            .button-card {
                padding: 15px;
            }

            .button-icon {
                font-size: 24px;
            }

            .button-label {
                font-size: 11px;
            }

            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 16px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 12px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .hero {
                padding: 60px 20px;
                min-height: auto;
            }

            .hero h1 {
                font-size: 28px;
            }

            .hero p {
                font-size: 14px;
            }

            .buttons-container {
                padding: 0 20px;
                margin: -40px auto 0;
            }

            .buttons-grid {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 10px;
            }

            .features-section,
            .cta-section {
                padding: 60px 20px;
            }

            .section-title h2 {
                font-size: 28px;
            }

            .features-grid {
                gap: 20px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-hero {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .logo {
                font-size: 18px;
            }

            .nav-button {
                padding: 8px 15px;
                font-size: 12px;
            }

            .hero h1 {
                font-size: 24px;
            }

            .buttons-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .button-card {
                padding: 12px;
            }

            .button-icon {
                font-size: 20px;
                margin-bottom: 8px;
            }

            .button-label {
                font-size: 9px;
            }

            .stat-number {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    @yield('content')

    <!-- FOOTER -->
    <footer class="footer">
        <p>&copy; 2026 NextScout Platform. Tüm hakları saklıdır.</p>
        <div class="footer-links">
            <a href="#gizlilik">Gizlilik Politikası</a>
            <a href="#kosullar">Kullanım Şartları</a>
            <a href="#iletisim">İletişim</a>
            <a href="#blog">Blog</a>
        </div>
    </footer>
</body>
</html>
