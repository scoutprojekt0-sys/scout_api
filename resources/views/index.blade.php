<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextScout - AI Scout Platform</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F8FAFC;
            color: #1F2937;
            line-height: 1.6;
        }

        /* ========== HEADER ========== */
        header {
            background: #FFFFFF;
            border-bottom: 2px solid #3B82F6;
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        }

        .header-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 40px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        /* Header Left Section */
        .header-left {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        /* Header Center Section */
        .header-center {
            flex: 1;
            display: flex;
            justify-content: center;
            max-width: 600px;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            font-size: 28px;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
            color: #3B82F6;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .logo:hover {
            color: #0EA5E9;
            transform: scale(1.05);
        }

        .logo i {
            font-size: 32px;
        }

        /* Auth Section (Right side) */
        .auth-section {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        /* ========== ADVANCED SEARCH MODAL ========== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-card {
            background: #FFFFFF;
            border-radius: 16px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            border-bottom: 2px solid #E0E7FF;
        }

        .modal-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-header h2 i {
            color: #3B82F6;
        }

        .modal-close {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: none;
            background: #F0F9FF;
            color: #3B82F6;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: #3B82F6;
            color: #FFFFFF;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #1F2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group label i {
            color: #3B82F6;
            font-size: 16px;
        }

        .form-control {
            padding: 12px 16px;
            border: 2px solid #E0E7FF;
            border-radius: 8px;
            font-size: 14px;
            color: #1F2937;
            background: #F9FAFB;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #3B82F6;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .range-inputs {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .range-inputs input {
            flex: 1;
        }

        .range-inputs span {
            color: #64748B;
            font-weight: 600;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            padding-top: 20px;
            border-top: 2px solid #E0E7FF;
        }

        .btn-primary, .btn-secondary {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3B82F6, #0EA5E9);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0EA5E9, #06B6D4);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #F0F9FF;
            color: #3B82F6;
            border: 2px solid #3B82F6;
        }

        .btn-secondary:hover {
            background: #DBEAFE;
        }

        /* Responsive Modal */
        @media (max-width: 768px) {
            .modal-card {
                width: 95%;
                max-height: 95vh;
            }

            .modal-header, .modal-body {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }

        /* ========== LOGIN MODAL ========== */
        .login-modal {
            max-width: 480px;
        }

        .social-login {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid;
        }

        .google-btn {
            background: #FFFFFF;
            color: #1F2937;
            border-color: #E5E7EB;
        }

        .google-btn:hover {
            background: #F9FAFB;
            border-color: #D1D5DB;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .facebook-btn {
            background: #1877F2;
            color: #FFFFFF;
            border-color: #1877F2;
        }

        .facebook-btn:hover {
            background: #166FE5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(24, 119, 242, 0.3);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #94A3B8;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #E0E7FF;
        }

        .divider span {
            padding: 0 15px;
        }

        .password-input {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-input input {
            flex: 1;
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            padding: 8px;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #3B82F6;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            font-size: 14px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #64748B;
        }

        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3B82F6;
        }

        .forgot-password {
            color: #3B82F6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #0EA5E9;
            text-decoration: underline;
        }

        .btn-full {
            width: 100%;
            justify-content: center;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E0E7FF;
            color: #64748B;
            font-size: 14px;
        }

        .register-link a {
            color: #3B82F6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #0EA5E9;
            text-decoration: underline;
        }

        /* ========== REGISTER MODAL ========== */
        .register-modal {
            max-width: 520px;
        }

        /* Notification Button */
        .notification-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: transparent;
            border: 2px solid #06B6D4;
            color: #06B6D4;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            text-decoration: none;
        }

        .notification-btn:hover {
            background: #06B6D4;
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #FF0000;
            color: #FFFFFF;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            border: 2px solid #F8FAFC;
        }

        /* Live Matches Button */
        .live-matches-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: transparent;
            border: 2px solid #3B82F6;
            color: #3B82F6;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            white-space: nowrap;
            text-decoration: none;
        }

        .live-matches-btn:hover {
            background: #3B82F6;
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .live-badge {
            width: 8px;
            height: 8px;
            background: #FF0000;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
            display: inline-block;
        }

        .live-count-badge {
            background: #FF0000;
            color: #FFFFFF;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            margin-left: 8px;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Search Container */
        .search-container {
            display: flex;
            align-items: center;
            background: #F0F9FF;
            border: 2px solid #BAE6FD;
            border-radius: 8px;
            padding: 0 15px;
            width: 100%;
            max-width: 500px;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .search-container:focus-within {
            border-color: #3B82F6;
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.2);
        }

        .search-container input {
            flex: 1;
            background: transparent;
            border: none;
            color: #1F2937;
            padding: 12px 0;
            font-size: 14px;
            outline: none;
        }

        .search-container input::placeholder {
            color: #94A3B8;
        }

        .search-icon {
            color: #3B82F6;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-icon:hover {
            color: #0EA5E9;
            transform: scale(1.1);
        }

        .search-divider {
            width: 1px;
            height: 24px;
            background: #BAE6FD;
        }

        .advanced-search-icon {
            color: #06B6D4;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            padding: 8px;
            background: transparent;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .advanced-search-icon:hover {
            color: #0EA5E9;
            transform: scale(1.1);
            background: #EFF6FF;
            border-radius: 4px;
        }

        /* Advanced Search */
        .advanced-search-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            background: transparent;
            border: 2px solid #06B6D4;
            color: #06B6D4;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            white-space: nowrap;
            text-decoration: none;
        }

        .advanced-search-btn:hover {
            background: #06B6D4;
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }

        /* Languages Dropdown */
        .language-dropdown {
            position: relative;
            display: flex;
            align-items: center;
        }

        .lang-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: #3B82F6;
            border: 2px solid #3B82F6;
            color: #FFFFFF;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        .lang-btn:hover {
            border-color: #0EA5E9;
            background: #0EA5E9;
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
            transform: translateY(-2px);
        }

        .lang-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: #FFFFFF;
            border: 2px solid #3B82F6;
            border-radius: 8px;
            min-width: 150px;
            margin-top: 8px;
            display: none;
            flex-direction: column;
            z-index: 2000;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .lang-dropdown-menu.active {
            display: flex;
        }

        .lang-option {
            padding: 12px 16px;
            color: #1F2937;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 1px solid #E0E7FF;
        }

        .lang-option:last-child {
            border-bottom: none;
        }

        .lang-option:hover {
            background: #EFF6FF;
            color: #3B82F6;
            padding-left: 20px;
        }

        /* Login Button */
        .login-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #3B82F6, #0EA5E9);
            border: none;
            color: #FFFFFF;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            text-decoration: none;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #0EA5E9, #06B6D4);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        /* Register Button */
        .register-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: transparent;
            border: 2px solid #3B82F6;
            color: #3B82F6;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            white-space: nowrap;
        }

        .register-btn:hover {
            background: #EFF6FF;
            border-color: #0EA5E9;
            color: #0EA5E9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        /* ========== HERO SECTION ========== */
        .hero {
            padding: 80px 40px;
            max-width: 1600px;
            margin: 0 auto;
            text-align: center;
        }

        .hero h1 {
            font-size: 64px;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.1;
        }

        .hero-red {
            color: #3B82F6;
        }

        .hero-white {
            color: #1F2937;
        }

        .hero p {
            font-size: 20px;
            color: #64748B;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            margin-top: 60px;
            padding-top: 60px;
            border-top: 2px solid #3B82F6;
        }

        .stat-card {
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #3B82F6;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(59, 130, 246, 0.15);
            border-color: #0EA5E9;
        }

        .stat-number {
            font-size: 42px;
            font-weight: 800;
            color: #3B82F6;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 14px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1200px) {
            .header-container {
                padding: 0 20px;
                gap: 15px;
            }

            .header-left {
                gap: 15px;
            }

            .header-center {
                max-width: 400px;
            }

            .auth-section {
                gap: 15px;
            }

            .hero {
                padding: 50px 20px;
            }

            .hero h1 {
                font-size: 42px;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-wrap: wrap;
                gap: 10px;
                padding: 10px 15px;
                height: auto;
                min-height: 70px;
            }

            .header-left {
                order: 1;
                width: 100%;
                justify-content: space-between;
            }

            .logo {
                font-size: 20px;
            }

            .header-center {
                order: 3;
                width: 100%;
                margin-top: 10px;
                max-width: 100%;
            }

            .search-container {
                max-width: 100%;
            }

            .auth-section {
                order: 2;
                width: 100%;
                justify-content: flex-end;
                gap: 8px;
                margin-top: 10px;
            }

            .live-matches-btn,
            .lang-btn,
            .register-btn,
            .login-btn {
                padding: 8px 12px;
                font-size: 12px;
            }

            .notification-btn {
                width: 36px;
                height: 36px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- ========== HEADER ========== -->
    <header>
        <div class="header-container">
            <!-- Header Left -->
            <div class="header-left">
                <!-- Logo -->
                <a href="/" class="logo">
                    <i class="fas fa-search"></i>
                    NextScout
                </a>

                <!-- Live Matches Button -->
                <a href="/live-matches" class="live-matches-btn">
                    <span class="live-badge"></span>
                    <i class="fas fa-fire"></i>
                    Canlı Maçlar
                </a>
            </div>

            <!-- Header Center -->
            <div class="header-center">
                <!-- Search Box with Advanced Search Icon -->
                <form class="search-container" onsubmit="handleSearch(event)">
                    <input type="text" id="searchInput" placeholder="Oyuncu, takım ara...">
                    <i class="fas fa-search search-icon" onclick="handleSearch(event)"></i>
                    <div class="search-divider"></div>
                    <button type="button" class="advanced-search-icon" onclick="openAdvancedSearch()" title="Detaylı Arama">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                </form>
            </div>

            <!-- Auth Section (Right side) -->
            <div class="auth-section">
                <!-- Notification Icon -->
                <a href="/notifications" class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>

                <!-- Language Dropdown -->
                <div class="language-dropdown">
                    <button type="button" class="lang-btn" id="langBtn" onclick="toggleLangMenu()">
                        <i class="fas fa-globe"></i>
                        <span id="currentLang">TR</span>
                    </button>
                    <div class="lang-dropdown-menu" id="langMenu">
                        <div class="lang-option" onclick="selectLanguage('TR', '🇹🇷')">🇹🇷 Türkçe</div>
                        <div class="lang-option" onclick="selectLanguage('EN', '🇬🇧')">🇬🇧 English</div>
                        <div class="lang-option" onclick="selectLanguage('ES', '🇪🇸')">🇪🇸 Español</div>
                        <div class="lang-option" onclick="selectLanguage('DE', '🇩🇪')">🇩🇪 Deutsch</div>
                    </div>
                </div>

                @guest
                    <!-- Register Button -->
                    <button type="button" class="register-btn" onclick="openRegisterModal()">
                        <i class="fas fa-user-plus"></i>
                        Kayıt Ol
                    </button>
                @endguest

                <!-- Login Button -->
                @auth
                    <a href="/dashboard" class="login-btn">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                @else
                    <button type="button" class="login-btn" onclick="openLoginModal()">
                        <i class="fas fa-sign-in-alt"></i>
                        Giriş
                    </button>
                @endauth
            </div>
        </div>
    </header>

    <!-- ========== ADVANCED SEARCH MODAL ========== -->
    <div class="modal-overlay" id="advancedSearchModal" onclick="closeAdvancedSearch()">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2><i class="fas fa-sliders-h"></i> Detaylı Arama</h2>
                <button class="modal-close" onclick="closeAdvancedSearch()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="advancedSearchForm">
                    <div class="form-grid">
                        <!-- Pozisyon -->
                        <div class="form-group">
                            <label><i class="fas fa-running"></i> Pozisyon</label>
                            <select class="form-control" name="position">
                                <option value="">Tümü</option>
                                <option value="kaleci">Kaleci</option>
                                <option value="defans">Defans</option>
                                <option value="orta-saha">Orta Saha</option>
                                <option value="forvet">Forvet</option>
                            </select>
                        </div>

                        <!-- Yaş Aralığı -->
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Yaş Aralığı</label>
                            <div class="range-inputs">
                                <input type="number" class="form-control" name="age_min" placeholder="Min" min="16" max="40">
                                <span>-</span>
                                <input type="number" class="form-control" name="age_max" placeholder="Max" min="16" max="40">
                            </div>
                        </div>

                        <!-- Ülke -->
                        <div class="form-group">
                            <label><i class="fas fa-flag"></i> Ülke</label>
                            <select class="form-control" name="country">
                                <option value="">Tümü</option>
                                <option value="tr">🇹🇷 Türkiye</option>
                                <option value="de">🇩🇪 Almanya</option>
                                <option value="es">🇪🇸 İspanya</option>
                                <option value="gb">🇬🇧 İngiltere</option>
                                <option value="fr">🇫🇷 Fransa</option>
                                <option value="br">🇧🇷 Brezilya</option>
                            </select>
                        </div>

                        <!-- Piyasa Değeri -->
                        <div class="form-group">
                            <label><i class="fas fa-euro-sign"></i> Piyasa Değeri</label>
                            <div class="range-inputs">
                                <input type="text" class="form-control" name="value_min" placeholder="Min €">
                                <span>-</span>
                                <input type="text" class="form-control" name="value_max" placeholder="Max €">
                            </div>
                        </div>

                        <!-- Boy -->
                        <div class="form-group">
                            <label><i class="fas fa-ruler-vertical"></i> Boy (cm)</label>
                            <div class="range-inputs">
                                <input type="number" class="form-control" name="height_min" placeholder="Min" min="150" max="220">
                                <span>-</span>
                                <input type="number" class="form-control" name="height_max" placeholder="Max" min="150" max="220">
                            </div>
                        </div>

                        <!-- Tercih Edilen Ayak -->
                        <div class="form-group">
                            <label><i class="fas fa-shoe-prints"></i> Tercih Edilen Ayak</label>
                            <select class="form-control" name="foot">
                                <option value="">Tümü</option>
                                <option value="sag">Sağ Ayak</option>
                                <option value="sol">Sol Ayak</option>
                                <option value="her-ikisi">Her İkisi</option>
                            </select>
                        </div>

                        <!-- Takım -->
                        <div class="form-group">
                            <label><i class="fas fa-users"></i> Takım Durumu</label>
                            <select class="form-control" name="team_status">
                                <option value="">Tümü</option>
                                <option value="takim-var">Takımı Var</option>
                                <option value="serbest">Serbest</option>
                            </select>
                        </div>

                        <!-- Video -->
                        <div class="form-group">
                            <label><i class="fas fa-video"></i> Video Durumu</label>
                            <select class="form-control" name="video_status">
                                <option value="">Tümü</option>
                                <option value="var">Video Var</option>
                                <option value="yok">Video Yok</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="modal-footer">
                        <button type="reset" class="btn-secondary" onclick="resetAdvancedSearch()">
                            <i class="fas fa-redo"></i> Sıfırla
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-search"></i> Ara
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== LOGIN MODAL ========== -->
    <div class="modal-overlay" id="loginModal" onclick="closeLoginModal()">
        <div class="modal-card login-modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2><i class="fas fa-sign-in-alt"></i> Giriş Yap</h2>
                <button class="modal-close" onclick="closeLoginModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- Social Login Buttons -->
                <div class="social-login">
                    <button class="social-btn google-btn" type="button">
                        <i class="fab fa-google"></i>
                        Google ile Giriş Yap
                    </button>
                    <button class="social-btn facebook-btn" type="button">
                        <i class="fab fa-facebook-f"></i>
                        Facebook ile Giriş Yap
                    </button>
                </div>

                <div class="divider">
                    <span>veya</span>
                </div>

                <!-- Login Form -->
                <form id="loginForm" action="/login" method="POST">
                    @csrf
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" class="form-control" name="email" placeholder="ornek@email.com" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Şifre</label>
                        <div class="password-input">
                            <input type="password" class="form-control" name="password" id="loginPassword" placeholder="••••••••" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('loginPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember">
                            <span>Beni Hatırla</span>
                        </label>
                        <a href="/forgot-password" class="forgot-password">Şifremi Unuttum?</a>
                    </div>

                    <button type="submit" class="btn-primary btn-full">
                        <i class="fas fa-sign-in-alt"></i> Giriş Yap
                    </button>

                    <div class="register-link">
                        Hesabınız yok mu? <a href="/register" onclick="event.preventDefault(); closeLoginModal(); openRegisterModal();">Kayıt Ol</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== REGISTER MODAL ========== -->
    <div class="modal-overlay" id="registerModal" onclick="closeRegisterModal()">
        <div class="modal-card register-modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Kayıt Ol</h2>
                <button class="modal-close" onclick="closeRegisterModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- Social Register Buttons -->
                <div class="social-login">
                    <button class="social-btn google-btn" type="button">
                        <i class="fab fa-google"></i>
                        Google ile Kayıt Ol
                    </button>
                    <button class="social-btn facebook-btn" type="button">
                        <i class="fab fa-facebook-f"></i>
                        Facebook ile Kayıt Ol
                    </button>
                </div>

                <div class="divider">
                    <span>veya</span>
                </div>

                <!-- Register Form -->
                <form id="registerForm" action="/register" method="POST">
                    @csrf
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Ad Soyad</label>
                        <input type="text" class="form-control" name="name" placeholder="Ad Soyad" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" class="form-control" name="email" placeholder="ornek@email.com" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Şifre</label>
                        <div class="password-input">
                            <input type="password" class="form-control" name="password" id="registerPassword" placeholder="••••••••" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('registerPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Şifre Tekrar</label>
                        <div class="password-input">
                            <input type="password" class="form-control" name="password_confirmation" id="registerPasswordConfirm" placeholder="••••••••" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('registerPasswordConfirm')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-users"></i> Hesap Tipi</label>
                        <select class="form-control" name="role" required>
                            <option value="">Seçiniz</option>
                            <option value="player">Oyuncu</option>
                            <option value="scout">Scout</option>
                            <option value="manager">Menajer</option>
                            <option value="club">Kulüp</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            <span><a href="/terms" class="forgot-password" target="_blank">Kullanım Şartları</a>'nı kabul ediyorum</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary btn-full">
                        <i class="fas fa-user-plus"></i> Kayıt Ol
                    </button>

                    <div class="register-link">
                        Zaten hesabınız var mı? <a href="#" onclick="event.preventDefault(); closeRegisterModal(); openLoginModal();">Giriş Yap</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== HERO SECTION ========== -->
    <section class="hero">
        <h1>
            <span class="hero-red">Scout Yap,</span>
            <span class="hero-white">Transfer Yap</span>
        </h1>
        <p>AI destekli scouting platformu ile geleceğin yıldızlarını bugün keşfet</p>

        <div class="hero-stats">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['scouts'] ?? '15K' }}+</div>
                <div class="stat-label">Aktif Scout</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['videos'] ?? '50K' }}+</div>
                <div class="stat-label">Video Analiz</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['transfers'] ?? '1,234' }}</div>
                <div class="stat-label">Transfer Kapandı</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['satisfaction'] ?? '92' }}%</div>
                <div class="stat-label">Memnuniyet Oranı</div>
            </div>
        </div>
    </section>

    <script>
        // API Base URL
        const API_BASE_URL = '/api';

        // ========== NOTIFICATION API ==========
        async function fetchNotificationCount() {
            try {
                const response = await fetch(`${API_BASE_URL}/notifications/count`);
                const data = await response.json();

                if (data.success) {
                    updateNotificationBadge(data.count);
                }
            } catch (error) {
                console.error('Bildirim sayısı alınamadı:', error);
            }
        }

        function updateNotificationBadge(count) {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        // ========== LIVE MATCH API ==========
        async function fetchLiveMatchCount() {
            try {
                const response = await fetch(`${API_BASE_URL}/live-matches/count`);
                const data = await response.json();

                if (data.success) {
                    updateLiveMatchIndicator(data.count);
                }
            } catch (error) {
                console.error('Canlı maç sayısı alınamadı:', error);
            }
        }

        function updateLiveMatchIndicator(count) {
            const liveBtn = document.querySelector('.live-matches-btn');
            if (liveBtn && count > 0) {
                // Badge varsa güncelle, yoksa ekle
                let countBadge = liveBtn.querySelector('.live-count-badge');
                if (!countBadge) {
                    countBadge = document.createElement('span');
                    countBadge.className = 'live-count-badge';
                    liveBtn.appendChild(countBadge);
                }
                countBadge.textContent = count;
            }
        }

        // ========== AUTO REFRESH ==========
        function startAutoRefresh() {
            // İlk yükleme
            fetchNotificationCount();
            fetchLiveMatchCount();

            // Her 30 saniyede bir güncelle
            setInterval(() => {
                fetchNotificationCount();
                fetchLiveMatchCount();
            }, 30000); // 30 seconds
        }

        // Sayfa yüklendiğinde başlat
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
        });

        // Language Dropdown Toggle
        function toggleLangMenu() {
            const menu = document.getElementById('langMenu');
            menu.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const langDropdown = document.querySelector('.language-dropdown');
            if (!langDropdown.contains(event.target)) {
                document.getElementById('langMenu').classList.remove('active');
            }
        });

        // Select Language
        function selectLanguage(lang, flag) {
            document.getElementById('currentLang').textContent = lang;
            document.getElementById('langMenu').classList.remove('active');
            console.log('Dil değiştirildi:', lang);
        }

        // Search functionality
        function handleSearch(event) {
            event.preventDefault();
            const searchInput = document.getElementById('searchInput');
            if (searchInput.value.trim()) {
                window.location.href = `/search?q=${encodeURIComponent(searchInput.value)}`;
            }
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleSearch(e);
            }
        });

        // ========== ADVANCED SEARCH MODAL ==========
        function openAdvancedSearch() {
            document.getElementById('advancedSearchModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeAdvancedSearch() {
            document.getElementById('advancedSearchModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function resetAdvancedSearch() {
            document.getElementById('advancedSearchForm').reset();
        }

        // Advanced Search Form Submit
        document.getElementById('advancedSearchForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Form verilerini topla
            const formData = new FormData(this);
            const searchParams = new URLSearchParams();

            for (let [key, value] of formData.entries()) {
                if (value) {
                    searchParams.append(key, value);
                }
            }

            // Arama sayfasına yönlendir
            window.location.href = `/search?${searchParams.toString()}`;
        });

        // ESC tuşu ile modal kapatma
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAdvancedSearch();
                closeLoginModal();
            }
        });

        // ========== LOGIN MODAL ==========
        function openLoginModal() {
            document.getElementById('loginModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.toggle-password i');

            if (input.type === 'password') {
                input.type = 'text';
                button.classList.remove('fa-eye');
                button.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                button.classList.remove('fa-eye-slash');
                button.classList.add('fa-eye');
            }
        }

        // ========== REGISTER MODAL ==========
        function openRegisterModal() {
            document.getElementById('registerModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeRegisterModal() {
            document.getElementById('registerModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // ESC tuşu ile modal kapatma
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAdvancedSearch();
                closeLoginModal();
                closeRegisterModal();
            }
        });
    </script>
</body>
</html>
