<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Home Page | STEM Sales CRM</title>

    <!-- Google Fonts & Base Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- AdminLTE + DataTables + Required Vendor CSS -->
    <link rel="stylesheet" href="https://stemapp.in/assets/css/adminlte.min.css">
    <link rel="stylesheet" href="https://stemapp.in/assets/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="https://stemapp.in/assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://stemapp.in/assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://stemapp.in/assets/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Bootstrap 4 core -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --glass-bg: rgba(255, 255, 248, 0.38);
            --glass-border: rgba(255, 255, 255, 0.55);
            --glass-shadow: 0 10px 30px rgba(31, 38, 135, 0.10), inset 0 1px 1px rgba(255, 255, 255, 0.45);
            --glass-blur: blur(16px);
            --text-dark: #0f172a;
            --text-soft: #334155;
            --accent-blue: #4f6ef6;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --accent-amber: #f59e0b;
            --accent-purple: #8b5cf6;
            --accent-teal: #14b8a6;
            --accent-pink: #ec4899;
            --accent-indigo: #6366f1;
            --accent-cyan: #06b6d4;
            --radius-xl: 28px;
            --radius-lg: 20px;
            --radius-md: 16px;
            --radius-pill: 50px;
            --transition-smooth: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-bounce: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Source Sans Pro', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 10% 0%, rgba(96, 165, 250, 0.35), transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(168, 85, 247, 0.30), transparent 50%),
                radial-gradient(circle at 50% 40%, rgba(147, 197, 253, 0.18), transparent 45%),
                linear-gradient(140deg, #eef2fa 0%, #ffffff 35%, #e8ecf8 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            -webkit-font-smoothing: antialiased;
        }

        /* Floating Orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.50;
            pointer-events: none;
            z-index: 0;
            animation: floatGlass 13s ease-in-out infinite;
            will-change: transform;
        }
        .orb1 {
            width: 340px;
            height: 340px;
            background: #60a5fa;
            top: -120px;
            left: -90px;
        }
        .orb2 {
            width: 420px;
            height: 420px;
            background: #a855f7;
            bottom: -140px;
            right: -100px;
            animation-delay: 2.5s;
        }
        .orb3 {
            width: 250px;
            height: 250px;
            background: #93c5fd;
            top: 50%;
            left: 72%;
            animation-delay: 5s;
        }
        .orb4 {
            width: 290px;
            height: 290px;
            background: #c084fc;
            bottom: 10%;
            left: 3%;
            animation-delay: 7.5s;
        }
        @keyframes floatGlass {
            0%,
            100% {
                transform: translateY(0px) translateX(0px);
            }
            33% {
                transform: translateY(-24px) translateX(10px);
            }
            66% {
                transform: translateY(10px) translateX(-14px);
            }
        }

        /* Wrapper transparent */
        .wrapper {
            background: transparent !important;
            position: relative;
            z-index: 1;
        }

        /* ----- Glass Content Wrapper ----- */
        .content-wrapper {
            background: rgba(255, 255, 255, 0.18) !important;
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-radius: var(--radius-xl);
            margin: 18px 18px 18px 18px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            overflow-x: hidden;
            transition: var(--transition-smooth);
        }

        /* ----- Glass Sidebar ----- */
        [class*=sidebar-dark-] {
            background: #1a0f1f;
            background: conic-gradient(from 180deg at 50% 50%, #1f0b1b, #1a0a1a, #0f0c22, #0c1f15, #150c22, #1f0b18);
            animation: spinGradient 22s linear infinite;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        @keyframes spinGradient {
            0% {
                background: conic-gradient(from 90deg at 50% 50%, #1f0b1b, #1a0a1a, #0f0c22, #0c1f15, #150c22, #1f0b18);
            }
            25% {
                background: radial-gradient(circle, #1f0b1b, #0f0c22, #0c1f15, #150c22, #1f0b18);
            }
            50%,
            100% {
                background: conic-gradient(from 180deg at 50% 50%, #1f0b1b, #0f0c22, #0c1f15, #150c22, #1f0b18, #1f0b1b);
            }
            75% {
                background: conic-gradient(from 270deg at 50% 50%, #1f0b1b, #0f0c22, #0c1f15, #150c22, #1f0b18, #1f0b1b);
            }
        }

        /* ----- Glass Navbar ----- */
        .navbar-white {
            background: rgba(255, 248, 235, 0.55) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border-radius: 0 0 20px 20px;
            margin: 0 8px;
            transition: var(--transition-smooth);
        }
        .main-header {
            border-bottom: none !important;
        }

        /* Glass Cards & Tables */
        .card,
        .card.card-outline-tabs,
        .small-box>.inner,
        table {
            background: rgba(255, 255, 245, 0.35) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: var(--radius-xl) !important;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
            transition: var(--transition-smooth);
        }
        .card-header,
        .card-body {
            background: transparent !important;
        }
        .table thead th {
            background: rgba(235, 245, 255, 0.5);
            border-bottom: 1px solid rgba(255, 255, 245, 0.8);
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: 0.02em;
        }
        .table td,
        .table th {
            border-top: 1px solid rgba(255, 255, 240, 0.4);
            color: #1e293b;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background: rgba(255, 255, 245, 0.7) !important;
        }

        /* Glass Stats Row */
        .glass-stats-row {
            display: flex;
            gap: 18px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .stat-glossy {
            flex: 1;
            min-width: 160px;
            background: rgba(255, 255, 245, 0.32);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: var(--radius-xl);
            padding: 22px 18px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--glass-shadow);
            transition: var(--transition-smooth);
            cursor: default;
            position: relative;
            overflow: hidden;
        }
        .stat-glossy::after {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            pointer-events: none;
        }
        .stat-glossy:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.55);
            box-shadow: 0 16px 36px rgba(31, 38, 135, 0.14), inset 0 1px 1px rgba(255, 255, 255, 0.5);
        }
        .stat-glossy .stat-icon {
            font-size: 2rem;
            margin-bottom: 8px;
            display: block;
            opacity: 0.85;
        }
        .stat-glossy h2 {
            font-size: 34px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }
        .stat-glossy span {
            font-weight: 500;
            color: var(--text-soft);
            font-size: 0.9rem;
        }
        .stat-icon-blue {
            color: #4f6ef6;
        }
        .stat-icon-green {
            color: #10b981;
        }
        .stat-icon-purple {
            color: #8b5cf6;
        }
        .stat-icon-amber {
            color: #f59e0b;
        }

        /* Buttons */
        .btn-glass-action {
            background: rgba(255, 255, 245, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: var(--radius-pill);
            padding: 8px 22px;
            font-weight: 600;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: var(--transition-smooth);
            color: var(--text-dark);
            font-size: 0.85rem;
        }
        .btn-glass-action:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            color: #000;
        }
        .btn-primary {
            border-radius: var(--radius-pill);
            background: linear-gradient(135deg, #4f6ef6 0%, #6d8cf8 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(79, 110, 246, 0.3);
            font-weight: 600;
            transition: var(--transition-smooth);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3d5de7 0%, #5b7af0 100%);
            box-shadow: 0 6px 18px rgba(79, 110, 246, 0.4);
            transform: translateY(-1px);
        }
        .btn-success {
            border-radius: var(--radius-pill);
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            font-weight: 600;
            transition: var(--transition-smooth);
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
        }

        /* Navbar icon badges */
        .navbar-badge {
            font-size: 0.6rem !important;
            font-weight: 700 !important;
            position: absolute;
            top: 2px;
            right: 2px;
            padding: 3px 5px !important;
            border-radius: 50% !important;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            animation: pulseBadge 2s ease-in-out infinite;
        }
        @keyframes pulseBadge {
            0%,
            100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.18);
            }
        }
        .badge-danger {
            background: #ef4444 !important;
        }
        .badge-warning {
            background: #f59e0b !important;
        }
        .badge-success {
            background: #10b981 !important;
        }

        /* Navbar icon links */
        .navbar-nav .nav-link {
            position: relative;
            margin: 0 4px;
            font-size: 1.15rem;
            color: #374151 !important;
            transition: var(--transition-smooth);
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.5);
            color: #1f2937 !important;
            transform: scale(1.08);
        }
        .navbar-nav .nav-link i {
            font-size: 1.2rem;
        }

        /* User display and profile dropdown */
        .user-display {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
            cursor: pointer;
        }
        .user-display:hover {
            background: rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        .user-avatar-sm {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .profile-dropdown-menu {
            background: rgba(255, 255, 250, 0.8) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 22px !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.15);
            color: #1f2937;
            padding: 0;
            min-width: 300px;
            overflow: hidden;
        }
        .profile-card-header {
            background: rgba(79, 110, 246, 0.08);
            padding: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .profile-avatar-lg {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }
        .profile-links {
            padding: 12px 0;
        }
        .profile-links a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
            font-size: 0.9rem;
        }
        .profile-links a:hover {
            background: rgba(79, 110, 246, 0.06);
            color: #1f2937;
        }
        .profile-links a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1rem;
            text-align: center;
            color: #4f6ef6;
        }
        .profile-stats {
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin: 8px 0;
        }
        .profile-stat {
            text-align: center;
        }
        .profile-stat .stat-value {
            font-weight: 700;
            color: var(--text-dark);
        }
        .profile-stat .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        /* Dropdown general */
        .dropdown-menu {
            background: rgba(255, 255, 250, 0.8) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 18px !important;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
            color: #1f2937;
        }
        .dropdown-menu .text-muted {
            color: #6b7280 !important;
        }
        .txn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 6px;
            font-size: 0.8rem;
        }
        .txn-icon-plus {
            background: rgba(16, 185, 129, 0.2);
            color: #059669;
        }
        .txn-icon-minus {
            background: rgba(239, 68, 68, 0.2);
            color: #dc2626;
        }

        /* Footer */
        .main-footer {
            background: rgba(255, 255, 245, 0.3);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: var(--radius-xl);
            margin: 14px 18px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            font-size: 0.8rem;
            color: #4b5563;
            padding: 10px 20px;
        }

        .main-sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(10px);
            border-radius: 14px;
            font-weight: 600;
        }
        .nav-sidebar .nav-item>.nav-link {
            margin-bottom: 0;
        }
        .nav-sidebar>.nav-item {
            box-shadow: rgba(60, 64, 67, 0.2) 0 1px 2px 0, rgba(60, 64, 67, 0.1) 0 2px 6px 2px;
            border-radius: 14px;
            font-size: 12px;
        }
        .main-footer,
        .navbar-white,
        .card-body-addnewlead {
            background-image: url("https://raw.githubusercontent.com/mobalti/open-props-interfaces/refs/heads/main/ai-hero-chat-popover/assets/bg-gradient.jpg");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link {
            color: #eef2ff;
            border-radius: 14px;
            transition: var(--transition-smooth);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .alert {
            border-radius: var(--radius-pill) !important;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(200, 230, 210, 0.7) !important;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            font-weight: 500;
        }

        body::-webkit-scrollbar {
            width: 12px;
        }
        body::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.03);
        }
        body::-webkit-scrollbar-thumb {
            border-radius: 15px;
            background: linear-gradient(180deg, #a8f268 0%, #f7025c 100%);
        }

        #mainlogo {
            filter: drop-shadow(0 0 1rem rgba(79, 110, 246, 0.6));
            animation: spinGradient_image 5s linear infinite;
            transition: 0.3s;
        }
        @keyframes spinGradient_image {
            0% {
                filter: drop-shadow(0 0 1rem #ff0f7b);
            }
            25% {
                filter: drop-shadow(0 0 1rem #fff95b);
            }
            50% {
                filter: drop-shadow(0 0 1rem #e60b09);
            }
            75% {
                filter: drop-shadow(0 0 1rem #59d102);
            }
            100% {
                filter: drop-shadow(0 0 1rem #6bdfdb);
            }
        }

        /* ============================================ */
        /* ✨ GLASS TABS – BEAUTIFUL CUSTOM STYLES ✨ */
        /* ============================================ */

        /* Glass Tab Container Card */
        .glass-tab-container {
            background: rgba(255, 255, 250, 0.28);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow);
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        /* Tab Header Wrapper */
        .glass-tab-header {
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.45);
            padding: 16px 20px 0 20px;
            position: relative;
            overflow: hidden;
        }
        .glass-tab-header::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(168, 85, 247, 0.12);
            pointer-events: none;
            filter: blur(30px);
        }
        .glass-tab-header .section-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--text-dark);
            letter-spacing: -0.01em;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 1;
        }
        .glass-tab-header .section-title i {
            font-size: 1.3rem;
            background: linear-gradient(135deg, #4f6ef6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass Nav Tabs */
        .glass-nav-tabs {
            border-bottom: none !important;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding-bottom: 0;
            position: relative;
            z-index: 1;
        }
        .glass-nav-tabs .nav-item {
            margin-bottom: 0;
        }
        .glass-nav-tabs .nav-link {
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            background: rgba(255, 255, 250, 0.38) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--radius-pill) !important;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.88rem;
            color: #374151 !important;
            letter-spacing: 0.01em;
            transition: var(--transition-bounce);
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            margin-right: 2px;
            user-select: none;
        }
        .glass-nav-tabs .nav-link:hover {
            background: rgba(255, 255, 255, 0.6) !important;
            border-color: rgba(255, 255, 255, 0.75) !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(31, 38, 135, 0.10);
            color: #1f2937 !important;
        }
        .glass-nav-tabs .nav-link.active {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.75), rgba(245, 248, 255, 0.7)) !important;
            border-color: rgba(255, 255, 255, 0.8) !important;
            color: #0f172a !important;
            font-weight: 700;
            box-shadow: 0 6px 20px rgba(79, 110, 246, 0.18), inset 0 1px 1px rgba(255, 255, 255, 0.7);
            transform: translateY(-2px);
            letter-spacing: 0.02em;
        }
        .glass-nav-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 20%;
            width: 60%;
            height: 3px;
            background: linear-gradient(90deg, #4f6ef6, #8b5cf6, #ec4899);
            border-radius: 10px;
            animation: shimmerBar 2.5s ease-in-out infinite;
        }
        @keyframes shimmerBar {
            0%,
            100% {
                opacity: 0.7;
                transform: scaleX(1);
            }
            50% {
                opacity: 1;
                transform: scaleX(1.15);
            }
        }

        /* Badge inside glass tabs */
        .glass-nav-tabs .nav-link .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 22px;
            padding: 0 8px;
            border-radius: var(--radius-pill);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            transition: var(--transition-bounce);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .badge-gradient-blue {
            background: linear-gradient(135deg, #4f6ef6, #6366f1);
            color: #fff;
        }
        .badge-gradient-green {
            background: linear-gradient(135deg, #10b981, #14b8a6);
            color: #fff;
        }
        .badge-gradient-purple {
            background: linear-gradient(135deg, #8b5cf6, #a855f7);
            color: #fff;
        }
        .badge-gradient-amber {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: #fff;
        }
        .badge-gradient-pink {
            background: linear-gradient(135deg, #ec4899, #f472b6);
            color: #fff;
        }
        .badge-gradient-teal {
            background: linear-gradient(135deg, #14b8a6, #06b6d4);
            color: #fff;
        }
        .badge-gradient-indigo {
            background: linear-gradient(135deg, #6366f1, #818cf8);
            color: #fff;
        }
        .badge-gradient-cyan {
            background: linear-gradient(135deg, #06b6d4, #22d3ee);
            color: #1e293b;
        }
        .badge-gradient-rose {
            background: linear-gradient(135deg, #f43f5e, #fb7185);
            color: #fff;
        }
        .badge-gradient-lime {
            background: linear-gradient(135deg, #84cc16, #a3e635);
            color: #1e293b;
        }
        .badge-gradient-sky {
            background: linear-gradient(135deg, #0ea5e9, #38bdf8);
            color: #fff;
        }
        .badge-gradient-violet {
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            color: #fff;
        }

        /* Active tab badge glow */
        .glass-nav-tabs .nav-link.active .tab-badge {
            box-shadow: 0 4px 16px rgba(79, 110, 246, 0.35);
            transform: scale(1.08);
            animation: badgeGlow 2s ease-in-out infinite;
        }
        @keyframes badgeGlow {
            0%,
            100% {
                box-shadow: 0 4px 16px rgba(79, 110, 246, 0.35);
            }
            50% {
                box-shadow: 0 6px 24px rgba(139, 92, 246, 0.5);
            }
        }

        /* Tab Content Area */
        .glass-tab-content {
            padding: 24px 20px;
            background: rgba(255, 255, 250, 0.15);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            min-height: 200px;
        }
        .glass-tab-pane {
            animation: fadeSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Glass mini cards inside tab panes */
        .glass-event-card {
            background: rgba(255, 255, 250, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            margin-bottom: 12px;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }
        .glass-event-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            border-radius: 0 8px 8px 0;
            background: linear-gradient(180deg, #4f6ef6, #8b5cf6);
            opacity: 0.7;
            transition: var(--transition-smooth);
        }
        .glass-event-card:hover {
            background: rgba(255, 255, 255, 0.6);
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(31, 38, 135, 0.10);
            border-color: rgba(255, 255, 255, 0.7);
        }
        .glass-event-card:hover::before {
            opacity: 1;
            width: 6px;
        }
        .glass-event-card .event-info {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 200px;
        }
        .glass-event-card .event-icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }
        .icon-circle-blue {
            background: rgba(79, 110, 246, 0.15);
            color: #4f6ef6;
        }
        .icon-circle-green {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }
        .icon-circle-purple {
            background: rgba(139, 92, 246, 0.15);
            color: #8b5cf6;
        }
        .icon-circle-amber {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }
        .icon-circle-teal {
            background: rgba(20, 184, 166, 0.15);
            color: #14b8a6;
        }
        .icon-circle-pink {
            background: rgba(236, 72, 153, 0.15);
            color: #ec4899;
        }
        .icon-circle-indigo {
            background: rgba(99, 102, 241, 0.15);
            color: #6366f1;
        }
        .icon-circle-cyan {
            background: rgba(6, 182, 212, 0.15);
            color: #06b6d4;
        }
        .icon-circle-rose {
            background: rgba(244, 63, 94, 0.15);
            color: #f43f5e;
        }
        .icon-circle-lime {
            background: rgba(132, 204, 22, 0.15);
            color: #84cc16;
        }
        .icon-circle-sky {
            background: rgba(14, 165, 233, 0.15);
            color: #0ea5e9;
        }
        .icon-circle-violet {
            background: rgba(124, 58, 237, 0.15);
            color: #7c3aed;
        }

        .glass-event-card .event-details h6 {
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 0.95rem;
            letter-spacing: -0.01em;
        }
        .glass-event-card .event-details small {
            color: #6b7280;
            font-weight: 500;
            font-size: 0.78rem;
        }
        .glass-event-card .event-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .glass-event-card .event-time-badge {
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: var(--radius-pill);
            padding: 6px 14px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #374151;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            white-space: nowrap;
        }
        .glass-event-card .event-status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            animation: pulseDot 2s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
        }
        .dot-green {
            background: #10b981;
        }
        .dot-amber {
            background: #f59e0b;
        }
        .dot-blue {
            background: #4f6ef6;
        }

        /* Empty state */
        .glass-empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }
        .glass-empty-state i {
            font-size: 3.5rem;
            display: block;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #cbd5e1, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-empty-state h5 {
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content-wrapper {
                margin: 10px;
                border-radius: 20px;
            }
            .glass-stats-row {
                flex-direction: column;
                gap: 12px;
            }
            .stat-glossy {
                min-width: auto;
            }
            .navbar-white {
                border-radius: 0 0 14px 14px;
                margin: 0 2px;
            }
            .glass-nav-tabs {
                gap: 4px;
            }
            .glass-nav-tabs .nav-link {
                padding: 8px 14px;
                font-size: 0.78rem;
                gap: 5px;
            }
            .glass-nav-tabs .nav-link .tab-badge {
                min-width: 22px;
                height: 18px;
                font-size: 0.65rem;
                padding: 0 6px;
            }
            .glass-tab-content {
                padding: 16px 12px;
            }
            .glass-event-card {
                padding: 14px;
                flex-direction: column;
                align-items: flex-start;
            }
            .glass-event-card .event-meta {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 480px) {
            .glass-nav-tabs .nav-link {
                padding: 7px 10px;
                font-size: 0.7rem;
                border-radius: 25px !important;
            }
            .glass-nav-tabs .nav-link .tab-badge {
                min-width: 18px;
                height: 16px;
                font-size: 0.6rem;
                padding: 0 5px;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Floating Orbs -->
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
    <div class="orb orb4"></div>

    <div class="wrapper mt-2">
        <!-- Navbar (Glass) -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link font-weight-bold" style="width:auto; border-radius:20px; padding: 6px 16px;">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <button type="button" class="btn btn-primary btn-sm" onclick="goBack()">
                        <i class="fas fa-arrow-left mr-1"></i> Go Back
                    </button>
                    <button type="button" class="btn btn-success btn-sm ml-2">
                        <span><b>💰 Our Cash : 6,384</b> <i class="fa fa-indian-rupee-sign"></i></span>
                    </button>
                </li>
            </ul>

            <!-- Right Side Navbar Items -->
            <ul class="navbar-nav ml-auto align-items-center">
                <!-- Messages Dropdown (Cash Transactions) -->
                <li class="nav-item dropdown mr-1">
                    <a class="nav-link" data-toggle="dropdown" href="#" title="Cash Transactions">
                        <i class="far fa-comments" style="font-size:1.3rem;"></i>
                        <span class="badge badge-danger navbar-badge">4</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-3" style="max-width: 440px; min-width: 340px;">
                        <h6 class="dropdown-header text-dark font-weight-bold mb-2" style="background:transparent; border:none;">
                            <i class="fas fa-exchange-alt mr-2 text-primary"></i> Recent Cash Transactions
                        </h6>
                        <div class="d-flex align-items-start mb-2 p-2 rounded-lg" style="background:rgba(16,185,129,0.08); border-radius:12px;">
                            <span class="txn-icon txn-icon-plus mr-2 mt-1"><i class="fas fa-plus-circle"></i></span>
                            <div>
                                <p class="text-sm text-success mb-0" style="font-weight:600;">+ ₹ 1,900 — Refund for rejected Meeting Expense request.</p>
                                <small class="text-muted"><i class="far fa-clock mr-1"></i> 2026-05-25 08:04 - Geetanjali Sharma</small>
                                <br><span class="badge badge-success badge-sm mt-1">Balance: ₹ 6,384</span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex align-items-start mb-2 p-2 rounded-lg" style="background:rgba(239,68,68,0.06); border-radius:12px;">
                            <span class="txn-icon txn-icon-minus mr-2 mt-1"><i class="fas fa-minus-circle"></i></span>
                            <div>
                                <p class="text-sm text-danger mb-0" style="font-weight:600;">- ₹ 500 — Cash Deducted for Creating Barg in Meeting</p>
                                <small class="text-muted"><i class="far fa-clock mr-1"></i> 2026-05-25 07:25 - Geetanjali Sharma</small>
                                <br><span class="badge badge-warning badge-sm mt-1">Balance: ₹ 4,484</span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex align-items-start mb-2 p-2 rounded-lg" style="background:rgba(239,68,68,0.06); border-radius:12px;">
                            <span class="txn-icon txn-icon-minus mr-2 mt-1"><i class="fas fa-minus-circle"></i></span>
                            <div>
                                <p class="text-sm text-danger mb-0" style="font-weight:600;">- ₹ 1,400 — Deducted for Meeting Expense Allocation</p>
                                <small class="text-muted"><i class="far fa-clock mr-1"></i> 2026-05-25 07:23 - Geetanjali Sharma</small>
                                <br><span class="badge badge-warning badge-sm mt-1">Balance: ₹ 4,984</span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex align-items-start mb-2 p-2 rounded-lg" style="background:rgba(16,185,129,0.08); border-radius:12px;">
                            <span class="txn-icon txn-icon-plus mr-2 mt-1"><i class="fas fa-plus-circle"></i></span>
                            <div>
                                <p class="text-sm text-success mb-0" style="font-weight:600;">+ ₹ 8,000 — Wallet Recharge - Approved by Accounts</p>
                                <small class="text-muted"><i class="far fa-clock mr-1"></i> 2026-05-20 10:11 - Geetanjali Sharma</small>
                                <br><span class="badge badge-success badge-sm mt-1">Balance: ₹ 8,284</span>
                            </div>
                        </div>
                        <hr>
                        <a href="#" class="btn btn-sm btn-outline-primary btn-block rounded-pill mt-2">
                            <i class="fas fa-list mr-1"></i> View All Transactions
                        </a>
                    </div>
                </li>

                <!-- Notifications -->
                <li class="nav-item dropdown mr-1">
                    <a class="nav-link" data-toggle="dropdown" href="#" title="Notifications">
                        <i class="far fa-bell" style="font-size:1.3rem;"></i>
                        <span class="badge badge-warning navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-3" style="max-width: 380px;">
                        <h6 class="dropdown-header text-dark font-weight-bold mb-2" style="background:transparent; border:none;">
                            <i class="fas fa-bell mr-2 text-warning"></i> Notifications
                        </h6>
                        <div class="d-flex align-items-start mb-2 p-2 rounded-lg" style="background:rgba(245,158,11,0.08); border-radius:12px;">
                            <i class="fas fa-calendar-check text-success mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm mb-0 font-weight-bold">Meeting Approved</p>
                                <small class="text-muted">Your meeting with ABC Corp has been approved.</small>
                                <br><small class="text-muted"><i class="far fa-clock mr-1"></i> 2 hours ago</small>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex align-items-start mb-2 p-2 rounded-lg" style="background:rgba(245,158,11,0.08); border-radius:12px;">
                            <i class="fas fa-wallet text-primary mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm mb-0 font-weight-bold">Wallet Recharged</p>
                                <small class="text-muted">₹ 8,000 added to your wallet.</small>
                                <br><small class="text-muted"><i class="far fa-clock mr-1"></i> 5 days ago</small>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex align-items-start mb-2 p-2 rounded-lg" style="background:rgba(245,158,11,0.08); border-radius:12px;">
                            <i class="fas fa-exclamation-triangle text-danger mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm mb-0 font-weight-bold">Task Overdue</p>
                                <small class="text-muted">3 tasks are pending beyond the due date.</small>
                                <br><small class="text-muted"><i class="far fa-clock mr-1"></i> 1 day ago</small>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Fullscreen -->
                <li class="nav-item mr-1">
                    <a class="nav-link" data-widget="fullscreen" href="#" title="Toggle Fullscreen">
                        <i class="fas fa-expand-arrows-alt" style="font-size:1.2rem;"></i>
                    </a>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown ml-1">
                    <div class="user-display" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="User Profile">
                        <img src="https://stemapp.in/assets/image/user/Team.jpg" class="user-avatar-sm" alt="User">
                        <span>Geetanjali Sharma</span>
                        <i class="fas fa-chevron-down text-muted" style="font-size:0.7rem;"></i>
                    </div>
                    <input type="hidden" id="ur_id" value="1000346">
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown-menu">
                        <div class="profile-card-header">
                            <img src="https://stemapp.in/assets/image/user/Team.jpg" class="profile-avatar-lg" alt="User Avatar">
                            <h6 class="mb-1 font-weight-bold">Geetanjali Sharma</h6>
                            <small class="text-muted">Senior Sales Executive</small>
                            <div class="mt-2">
                                <span class="badge badge-primary">ID: 1000346</span>
                            </div>
                        </div>
                        <div class="profile-stats">
                            <div class="profile-stat">
                                <div class="stat-value">₹ 6,384</div>
                                <div class="stat-label">Wallet</div>
                            </div>
                            <div class="profile-stat">
                                <div class="stat-value">42</div>
                                <div class="stat-label">Handovers</div>
                            </div>
                            <div class="profile-stat">
                                <div class="stat-value">87%</div>
                                <div class="stat-label">Completion</div>
                            </div>
                        </div>
                        <div class="profile-links">
                            <a href="#"><i class="fas fa-user-circle"></i> My Profile</a>
                            <a href="#"><i class="fas fa-cog"></i> Account Settings</a>
                            <a href="#"><i class="fas fa-wallet"></i> Wallet Details</a>
                            <a href="#"><i class="fas fa-chart-bar"></i> Performance Report</a>
                            <a href="#"><i class="fas fa-question-circle"></i> Help & Support</a>
                            <hr class="my-1">
                            <a href="#"><i class="fas fa-sign-out-alt text-danger"></i> Logout</a>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <div class="text-center pt-3">
                <img id="mainlogo" src="https://stemlearning.in/wp-content/uploads/2020/07/stem-new-logo-2-1.png" width="70%" class="p-2" alt="STEM Logo">
                <h5 class="text-white mt-1"><b>STEM APP</b></h5>
            </div>
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://stemapp.in/assets/image/user/Team.jpg" class="img-circle elevation-2" alt="User">
                </div>
                <div class="info">
                    <a href="#" class="d-block text-white font-weight-bold">Geetanjali Sharma</a>
                </div>
            </div>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/Dashboard" class="nav-link">
                                <i class="fas fa-home nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/DayManagement" class="nav-link">
                                <i class="fas fa-calendar-day nav-icon"></i>
                                <p> Day Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/GetAllCompaniesThatDoNotHavePrimaryContactDetailsData" class="nav-link">
                                <i class="fas fa-building nav-icon"></i>
                                <p>No Primary Contact Companies</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/NewFunnel" class="nav-link">
                                <i class="fas fa-filter nav-icon"></i>
                                <p>New Funnel Added</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/UserTaskViewPage" class="nav-link">
                                <i class="fas fa-tasks nav-icon"></i>
                                <p>Todays Task Status</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/GetAllPlannerLogPlannedByUsers" class="nav-link">
                                <i class="fas fa-sync-alt nav-icon"></i>
                                <p>Todays Replanned Task</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/PendingForWriteMomMeetingList" class="nav-link">
                                <i class="fas fa-edit nav-icon"></i>
                                <p>Pending MOM Meeting List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/AddSpecialCommentOnTask" class="nav-link">
                                <i class="fas fa-comment-dots nav-icon"></i>
                                <p>Add Special Comment On Task (Pending)</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/AddThanksCommentOnTask" class="nav-link">
                                <i class="fas fa-thumbs-up nav-icon"></i>
                                <p>Add Thanks Comment On Task (Complete)</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/SpecialRequestForLeaveSomeTime" class="nav-link">
                                <i class="fas fa-user-clock nav-icon"></i>
                                <p>Our Special Request For Leave Some Time</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/AllReviewPlaing" class="nav-link">
                                <i class="fas fa-clipboard-check nav-icon"></i>
                                <p>All Review Planning</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/StartAnnualReviews" class="nav-link">
                                <i class="fas fa-calendar-check nav-icon"></i>
                                <p>Start Annual Review</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=base_url();?>Menu/StartAnnualReviews">
                                <i class="fas fa-calendar-check nav-icon"></i>
                                <p>Start Annual Review</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=base_url();?>SalesReviews/index">
                                <i class="fas fa-calendar-check nav-icon"></i>
                                <p>BD Wise Sales Review</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/logout" class="nav-link">
                                <i class="fas fa-sign-out-alt nav-icon"></i>
                                <p> Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark font-weight-bold">
                                <i class="fas fa-calendar-alt mr-2 text-primary"></i> Today's Task Calendar
                            </h5>
                        </div>
                        <!-- <div class="col-sm-6 text-right">
                            <button class="btn-glass-action">
                                <i class="fas fa-sync-alt mr-1"></i> Sync Dashboard
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Flash message -->
                    <div class="alert alert-success alert-dismissible fade show rounded-pill" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> <strong>✅ Sync Success!</strong> Handover dashboard updated with latest project data.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>




                    <div class="row">
                        <div class="col-md-9">
                             <!-- ============================================ -->
                    <!-- ✨ BEAUTIFUL GLASS TABS SECTION ✨ -->
                    <!-- ============================================ -->
                    <div class="glass-tab-container">
                        <!-- Tab Header -->
                        <div class="glass-tab-header">
                            <div class="section-title">
                                <i class="fas fa-layer-group"></i> Task Categories — <?= date('d M Y') ?>
                            </div>
                            <!-- Glass Nav Tabs -->
                            <ul class="nav glass-nav-tabs" id="actionTabs" role="tablist">
                                <?php
                                // Define badge color classes array for cycling
                                $badge_classes = [
                                    'badge-gradient-blue',
                                    'badge-gradient-green',
                                    'badge-gradient-purple',
                                    'badge-gradient-amber',
                                    'badge-gradient-pink',
                                    'badge-gradient-teal',
                                    'badge-gradient-indigo',
                                    'badge-gradient-cyan',
                                    'badge-gradient-rose',
                                    'badge-gradient-lime',
                                    'badge-gradient-sky',
                                    'badge-gradient-violet',
                                ];

                                $badge_index = 0;
                                $is_first = true;
                                foreach ($action_counts as $row):
                                    $badge_class = $badge_classes[$badge_index % count($badge_classes)];
                                    $badge_index++;
                                    $tab_id = 'tab-' . $row->action_name_small;
                                ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $is_first ? 'active' : '' ?>"
                                        id="<?= $tab_id ?>-tab"
                                        data-toggle="pill"
                                        href="#<?= $tab_id ?>"
                                        role="tab"
                                        aria-selected="<?= $is_first ? 'true' : 'false' ?>">
                                        <?= htmlspecialchars($row->name) ?>
                                        <span class="tab-badge <?= $badge_class ?>">
                                            <?= $row->total_count ?>
                                        </span>
                                    </a>
                                </li>
                                <?php
                                $is_first = false;
                            endforeach;
                            ?>

                            <?php if (empty($action_counts)): ?>
                                <!-- Fallback: show sample tabs if no data -->
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab-calls-tab" data-toggle="pill" href="#tab-calls" role="tab">
                                        📞 Calls <span class="tab-badge badge-gradient-blue">8</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-meetings-tab" data-toggle="pill" href="#tab-meetings" role="tab">
                                        🤝 Meetings <span class="tab-badge badge-gradient-green">5</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-follow_ups-tab" data-toggle="pill" href="#tab-follow_ups" role="tab">
                                        🔄 Follow-ups <span class="tab-badge badge-gradient-purple">12</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-demos-tab" data-toggle="pill" href="#tab-demos" role="tab">
                                        💻 Demos <span class="tab-badge badge-gradient-amber">3</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-site_visits-tab" data-toggle="pill" href="#tab-site_visits" role="tab">
                                        🏢 Site Visits <span class="tab-badge badge-gradient-pink">2</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-proposals-tab" data-toggle="pill" href="#tab-proposals" role="tab">
                                        📝 Proposals <span class="tab-badge badge-gradient-teal">6</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="glass-tab-content">
                        <div class="tab-content" id="actionTabContent">
                            <?php
                            // Reset for tab panes
                            $badge_index = 0;
                            $is_first = true;

                            if (!empty($action_counts)):
                                foreach ($action_counts as $row):
                                    $badge_class = $badge_classes[$badge_index % count($badge_classes)];
                                    $badge_index++;
                                    $tab_id = 'tab-' . $row->action_name_small;

                                    // Determine icon based on action name
                                    $action_lower = strtolower($row->name);
                                    $icon_class = 'icon-circle-blue';
                                    $tab_icon = 'fa-phone-alt';
                                    if (strpos($action_lower, 'meet') !== false) {
                                        $icon_class = 'icon-circle-green';
                                        $tab_icon = 'fa-handshake';
                                    } elseif (strpos($action_lower, 'follow') !== false) {
                                        $icon_class = 'icon-circle-purple';
                                        $tab_icon = 'fa-sync-alt';
                                    } elseif (strpos($action_lower, 'demo') !== false) {
                                        $icon_class = 'icon-circle-amber';
                                        $tab_icon = 'fa-laptop';
                                    } elseif (strpos($action_lower, 'visit') !== false || strpos($action_lower, 'site') !== false) {
                                        $icon_class = 'icon-circle-pink';
                                        $tab_icon = 'fa-building';
                                    } elseif (strpos($action_lower, 'proposal') !== false || strpos($action_lower, 'quote') !== false) {
                                        $icon_class = 'icon-circle-teal';
                                        $tab_icon = 'fa-file-alt';
                                    } elseif (strpos($action_lower, 'call') !== false) {
                                        $icon_class = 'icon-circle-blue';
                                        $tab_icon = 'fa-phone-alt';
                                    } elseif (strpos($action_lower, 'email') !== false) {
                                        $icon_class = 'icon-circle-indigo';
                                        $tab_icon = 'fa-envelope';
                                    } elseif (strpos($action_lower, 'task') !== false) {
                                        $icon_class = 'icon-circle-cyan';
                                        $tab_icon = 'fa-tasks';
                                    } elseif (strpos($action_lower, 'review') !== false) {
                                        $icon_class = 'icon-circle-rose';
                                        $tab_icon = 'fa-clipboard-check';
                                    } elseif (strpos($action_lower, 'note') !== false) {
                                        $icon_class = 'icon-circle-lime';
                                        $tab_icon = 'fa-sticky-note';
                                    } elseif (strpos($action_lower, 'remind') !== false) {
                                        $icon_class = 'icon-circle-sky';
                                        $tab_icon = 'fa-bell';
                                    } elseif (strpos($action_lower, 'webinar') !== false || strpos($action_lower, 'online') !== false) {
                                        $icon_class = 'icon-circle-violet';
                                        $tab_icon = 'fa-video';
                                    }
                                    ?>
                                    <div class="tab-pane fade <?= $is_first ? 'show active' : '' ?> glass-tab-pane"
                                    id="<?= $tab_id ?>"
                                    role="tabpanel"
                                    aria-labelledby="<?= $tab_id ?>-tab">

                                    <?php if ($row->total_count > 0): ?>
                                        <!-- Sample event cards (in production, loop through actual events) -->
                                        <?php
                                        $display_count = min($row->total_count, 4);
                                        for ($i = 1; $i <= $display_count; $i++):
                                            $sample_times = ['09:15 AM', '10:30 AM', '01:00 PM', '03:45 PM', '05:00 PM'];
                                            $sample_time = $sample_times[($i - 1) % count($sample_times)];
                                            $sample_titles = [
                                                'Client Discussion – ' . htmlspecialchars($row->name),
                                                'Follow-up on ' . htmlspecialchars($row->name),
                                                'Review Session: ' . htmlspecialchars($row->name),
                                                'Scheduled ' . htmlspecialchars($row->name) . ' Appointment',
                                            ];
                                            $sample_title = $sample_titles[($i - 1) % count($sample_titles)];
                                            ?>
                                            <div class="glass-event-card">
                                                <div class="event-info">
                                                    <div class="event-icon-circle <?= $icon_class ?>">
                                                        <i class="fas <?= $tab_icon ?>"></i>
                                                    </div>
                                                    <div class="event-details">
                                                        <h6><?= $sample_title ?></h6>
                                                        <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                                    </div>
                                                </div>
                                                <div class="event-meta">
                                                    <span class="event-time-badge">
                                                        <i class="far fa-clock mr-1"></i> <?= $sample_time ?>
                                                    </span>
                                                    <span class="event-status-dot dot-green" title="Confirmed"></span>
                                                    <span style="font-size:0.75rem; font-weight:600; color:#10b981;">Confirmed</span>
                                                </div>
                                            </div>
                                        <?php endfor; ?>

                                        <?php if ($row->total_count > 4): ?>
                                            <div class="text-center mt-3">
                                                <button class="btn btn-glass-action">
                                                    <i class="fas fa-ellipsis-h mr-1"></i> View All <?= $row->total_count ?> Records
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <!-- Empty State -->
                                        <div class="glass-empty-state">
                                            <i class="fas <?= $tab_icon ?>"></i>
                                            <h5>No <?= htmlspecialchars($row->name) ?> Scheduled</h5>
                                            <p class="text-muted">There are no tasks in this category for today. Enjoy your free time! 🎉</p>
                                            <button class="btn btn-primary btn-sm mt-2 rounded-pill">
                                                <i class="fas fa-plus-circle mr-1"></i> Add New <?= htmlspecialchars($row->name) ?>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                $is_first = false;
                            endforeach;
                        else:
                            ?>
                            <!-- Fallback Tab Panes when no data -->
                            <div class="tab-pane fade show active glass-tab-pane" id="tab-calls" role="tabpanel">
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-blue"><i class="fas fa-phone-alt"></i></div>
                                        <div class="event-details">
                                            <h6>Client Call – ABC Corporation</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 09:15 AM</span>
                                        <span class="event-status-dot dot-green"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#10b981;">Confirmed</span>
                                    </div>
                                </div>
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-blue"><i class="fas fa-phone-alt"></i></div>
                                        <div class="event-details">
                                            <h6>Follow-up Call – XYZ Ltd</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 11:00 AM</span>
                                        <span class="event-status-dot dot-amber"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#f59e0b;">Pending</span>
                                    </div>
                                </div>
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-blue"><i class="fas fa-phone-alt"></i></div>
                                        <div class="event-details">
                                            <h6>Cold Call – New Leads Batch</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 02:30 PM</span>
                                        <span class="event-status-dot dot-blue"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#4f6ef6;">Scheduled</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade glass-tab-pane" id="tab-meetings" role="tabpanel">
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-green"><i class="fas fa-handshake"></i></div>
                                        <div class="event-details">
                                            <h6>Client Meeting – DEF Group</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 10:30 AM</span>
                                        <span class="event-status-dot dot-green"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#10b981;">Confirmed</span>
                                    </div>
                                </div>
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-green"><i class="fas fa-handshake"></i></div>
                                        <div class="event-details">
                                            <h6>Strategy Meeting – Internal Team</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 04:00 PM</span>
                                        <span class="event-status-dot dot-green"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#10b981;">Confirmed</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade glass-tab-pane" id="tab-follow_ups" role="tabpanel">
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-purple"><i class="fas fa-sync-alt"></i></div>
                                        <div class="event-details">
                                            <h6>Follow-up – GHI Industries</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 12:00 PM</span>
                                        <span class="event-status-dot dot-amber"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#f59e0b;">Pending</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade glass-tab-pane" id="tab-demos" role="tabpanel">
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-amber"><i class="fas fa-laptop"></i></div>
                                        <div class="event-details">
                                            <h6>Product Demo – JKL Enterprises</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 01:00 PM</span>
                                        <span class="event-status-dot dot-green"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#10b981;">Confirmed</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade glass-tab-pane" id="tab-site_visits" role="tabpanel">
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-pink"><i class="fas fa-building"></i></div>
                                        <div class="event-details">
                                            <h6>Site Visit – MNO Corp HQ</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 03:45 PM</span>
                                        <span class="event-status-dot dot-blue"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#4f6ef6;">Scheduled</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade glass-tab-pane" id="tab-proposals" role="tabpanel">
                                <div class="glass-event-card">
                                    <div class="event-info">
                                        <div class="event-icon-circle icon-circle-teal"><i class="fas fa-file-alt"></i></div>
                                        <div class="event-details">
                                            <h6>Proposal Review – PQR Solutions</h6>
                                            <small><i class="far fa-user mr-1"></i> Assigned to: Geetanjali Sharma</small>
                                        </div>
                                    </div>
                                    <div class="event-meta">
                                        <span class="event-time-badge"><i class="far fa-clock mr-1"></i> 05:00 PM</span>
                                        <span class="event-status-dot dot-amber"></span>
                                        <span style="font-size:0.75rem; font-weight:600; color:#f59e0b;">Pending</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- END Glass Tab Container -->

     
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Unde mollitia necessitatibus sapiente voluptatem, maxime accusantium nesciunt, sed ab, voluptate eveniet odit nam animi tenetur repellat et autem modi. Repudiandae beatae laudantium vitae omnis. Nesciunt molestiae tempore repellendus consequuntur dignissimos tempora consectetur unde iste, ipsa, dicta nihil magnam laboriosam itaque a pariatur assumenda distinctio quos blanditiis quis voluptatem nemo quia! Natus, error nulla. Incidunt aspernatur aliquid doloremque dolorem autem praesentium minus. Fugit totam laudantium, recusandae quas saepe laborum distinctio quo autem quos omnis, assumenda voluptatum ducimus officiis minima dignissimos! Explicabo eum modi obcaecati est esse quae, repellendus at inventore, qui eveniet iusto. Tenetur deleniti molestiae maxime, id officiis itaque dicta accusamus aliquam porro possimus incidunt molestias fugiat debitis. Laborum velit debitis veritatis minus necessitatibus soluta ex, optio reiciendis tempora? Fuga commodi unde reprehenderit sint asperiores suscipit a? Culpa veritatis corrupti, voluptates natus unde a assumenda delectus labore placeat eaque magnam accusamus vel. Velit neque repudiandae quam. Alias aliquam, reprehenderit necessitatibus atque eius, illum voluptatibus neque asperiores ratione expedita deleniti eos labore dolorum. Voluptatum distinctio animi aut cum illum magni, ut assumenda explicabo laboriosam nesciunt suscipit asperiores quis beatae dolor cupiditate optio. Impedit cupiditate 
                            </div>
                        </div>
                    </div>







<style>
 .card {
    position: relative;
    padding: 20px;
    border-radius: 24px;

    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.22);

    backdrop-filter: blur(18px) saturate(140%);
    -webkit-backdrop-filter: blur(18px) saturate(140%);

    box-shadow:
        0 6px 18px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.35);

    overflow: hidden;
}

/* soft highlight */
.card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.25),
        transparent 50%
    );
    pointer-events: none;
}
</style>




                    
                  






















        </div>
    </section>
</div>

<!-- Footer -->
<footer class="main-footer">
    <strong>Copyright &copy; 2021-2026 <a href="#" style="color:#4f6ef6;">Stemlearning Glass Portal</a>.</strong> All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.0 <span class="badge badge-primary badge-sm ml-1">Glass Fusion</span>
    </div>
</footer>
</div>

<!-- Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js">
</script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js">
</script>
<script src="https://stemapp.in/assets/js/adminlte.js">
</script>
<script src="https://stemapp.in/assets/js/jquery.overlayScrollbars.min.js">
</script>
<!-- DataTables core -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js">
</script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js">
</script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js">
</script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js">
</script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js">
</script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js">
</script>

<script>
    $(document).ready(function() {
        // Initialize DataTable if the table exists
        if ($('#example1').length) {
            $('#example1').DataTable({
                responsive: true,
                lengthChange: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn-sm btn-light rounded-pill' },
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn-sm btn-light rounded-pill' },
                    { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn-sm btn-light rounded-pill' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print',
                        className: 'btn-sm btn-light rounded-pill' }
                ],
                language: { search: "Filter records:" }
            });
            $('.dt-buttons').addClass('mb-2');
        }

        // Sidebar active link highlight
        $('.nav-link').each(function() {
            if (this.href === window.location.href && this.href.indexOf('#') === -1) {
                $(this).addClass('active');
            }
        });

        // Go back function
        window.goBack = function() {
            window.history.back();
        };

        // Sync button toast
        $('.btn-glass-action').first().on('click', function() {
            alert('📊 Dashboard sync ready — Glass interface active!');
        });

        // Enable Bootstrap tooltips
        $('[title]').tooltip({ placement: 'bottom', delay: { show: 300, hide: 100 } });

        // ✨ Tab switch animation enhancement
        $('.glass-nav-tabs .nav-link').on('shown.bs.tab', function(e) {
            // Re-trigger fade animation on the newly active pane
            var targetPane = $($(e.target).attr('href'));
            targetPane.removeClass('glass-tab-pane');
            void targetPane[0].offsetWidth;
            targetPane.addClass('glass-tab-pane');
        });

        // ✨ Ripple effect on tab click
        $('.glass-nav-tabs .nav-link').on('click', function(e) {
            var $this = $(this);
            var ripple = $('<span class="tab-ripple"></span>');
            $this.append(ripple);
            var x = e.pageX - $this.offset().left;
            var y = e.pageY - $this.offset().top;
            ripple.css({
                left: x + 'px',
                top: y + 'px',
                width: '0',
                height: '0',
                position: 'absolute',
                borderRadius: '50%',
                background: 'rgba(255,255,255,0.5)',
                pointerEvents: 'none',
                transform: 'translate(-50%, -50%)',
                animation: 'rippleEffect 0.6s ease-out forwards'
            });
            setTimeout(function() {
                ripple.remove();
            }, 700);
        });

        // Add ripple keyframes dynamically
        var rippleStyle = document.createElement('style');
        rippleStyle.textContent = `
                @keyframes rippleEffect {
                    0% { width: 0; height: 0; opacity: 0.6; }
                    100% { width: 300px; height: 300px; opacity: 0; }
                }
            `;
        document.head.appendChild(rippleStyle);
    });

    function trackLocation() { console.log("location ready"); }

    function GetTodaysReviewPlan() {}

    function handleGeolocationSuccess(pos) {}

    function handleGeolocationError() {}
</script>
</body>
</html>