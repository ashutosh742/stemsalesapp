<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Quantum Access | Maintenance Mode</title>
    <!-- Google Fonts + Font Awesome 6 (Premium Modern Icons) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(ellipse at 20% 30%, #0B1120, #0A0F1C);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated grain / noise texture for premium depth */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='1' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* floating orbs (modern abstract) */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            z-index: 0;
            animation: floatOrb 18s infinite alternate ease-in-out;
        }
        .orb-1 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #4f46e5, #7c3aed);
            top: -100px;
            left: -100px;
        }
        .orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #0ea5e9, #3b82f6);
            bottom: -150px;
            right: -120px;
            animation-duration: 22s;
        }
        .orb-3 {
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, #8b5cf6, #d946ef);
            top: 40%;
            right: 10%;
            animation-duration: 25s;
            opacity: 0.25;
        }
        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 40px) scale(1.1); }
        }

        /* main premium card */
        .premium-card {
            max-width: 1280px;
            width: 100%;
            background: rgba(18, 25, 45, 0.75);
            backdrop-filter: blur(12px);
            border-radius: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 30px 55px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            z-index: 2;
        }

        .premium-card:hover {
            box-shadow: 0 40px 70px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            transform: translateY(-2px);
        }

        /* split grid */
        .card-grid {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 0;
        }

        /* LEFT SIDE — artistic illustration zone */
        .visual-zone {
            background: linear-gradient(145deg, #11182b, #0a0f1c);
            padding: 2.8rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(4px);
        }

        .icon-cluster {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.8rem;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .icon-circle {
            background: rgba(79, 70, 229, 0.18);
            backdrop-filter: blur(8px);
            width: 90px;
            height: 90px;
            border-radius: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: #c4b5fd;
            border: 1px solid rgba(139, 92, 246, 0.5);
            transition: all 0.25s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.3);
        }

        .icon-circle:hover {
            transform: translateY(-6px);
            background: rgba(139, 92, 246, 0.3);
            border-color: #a78bfa;
            color: #ede9fe;
            box-shadow: 0 20px 30px -10px rgba(79, 70, 229, 0.4);
        }

        .status-code-block {
            text-align: center;
            margin-top: 1rem;
        }

        .glitch-text {
            font-size: 4.2rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            background: linear-gradient(130deg, #e0e7ff, #a78bfa, #c084fc);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            text-shadow: 0 2px 10px rgba(139, 92, 246, 0.3);
        }

        .maintenance-badge-left {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(4px);
            padding: 0.45rem 1.2rem;
            border-radius: 60px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #b9c3e6;
            letter-spacing: 0.3px;
            margin-top: 1.8rem;
            border: 0.5px solid rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* RIGHT SIDE — content & actions */
        .content-zone {
            padding: 3rem 3rem 3rem 2.8rem;
            background: rgba(12, 18, 32, 0.55);
            backdrop-filter: blur(4px);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(220, 38, 38, 0.18);
            padding: 0.45rem 1.1rem;
            border-radius: 80px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #fca5a5;
            border: 0.5px solid rgba(248, 113, 113, 0.35);
            margin-bottom: 1.8rem;
            backdrop-filter: blur(2px);
        }

        .status-badge i {
            font-size: 0.9rem;
        }

        h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(to right, #ffffff, #c7d2fe, #a5b4fc);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 1.2rem;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .description {
            color: #cbd5e1;
            font-size: 1.05rem;
            line-height: 1.55;
            margin-bottom: 1.5rem;
            font-weight: 400;
            max-width: 90%;
        }

        .warning-note {
            background: rgba(0, 0, 0, 0.35);
            border-left: 3px solid #f97316;
            padding: 1rem 1.2rem;
            border-radius: 18px;
            margin: 1.5rem 0 2rem;
            font-size: 0.9rem;
            color: #b9c8ff;
            backdrop-filter: blur(4px);
        }

        .warning-note i {
            color: #fb923c;
            margin-right: 8px;
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(105deg, #4f46e5, #7c3aed);
            border: none;
            padding: 0.9rem 1.9rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.25s ease;
            box-shadow: 0 8px 20px -5px rgba(79, 70, 229, 0.4);
            cursor: pointer;
            text-decoration: none;
            letter-spacing: 0.2px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(105deg, #6366f1, #8b5cf6);
            box-shadow: 0 15px 30px -8px rgba(79, 70, 229, 0.6);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.9rem 1.9rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.95rem;
            color: #e2e8ff;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.25s ease;
            cursor: pointer;
            text-decoration: none;
            backdrop-filter: blur(2px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.4);
            color: white;
            transform: translateY(-2px);
        }

        .help-link {
            margin-top: 2.5rem;
            font-size: 0.85rem;
            color: #7e8aa8;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .help-link a {
            color: #bdc7ff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            border-bottom: 1px dashed rgba(165, 180, 252, 0.4);
        }

        .help-link a:hover {
            color: white;
            border-bottom-color: #a5b4fc;
        }

        /* additional decorative elements */
        .pulse-dot {
            width: 10px;
            height: 10px;
            background-color: #f97316;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 8px #f97316;
            animation: pulse-animation 1.5s infinite;
        }

        @keyframes pulse-animation {
            0% { opacity: 0.4; transform: scale(0.8);}
            100% { opacity: 1; transform: scale(1.2);}
        }

        /* responsive */
        @media (max-width: 950px) {
            .card-grid {
                grid-template-columns: 1fr;
            }
            .visual-zone {
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.08);
                padding: 2.5rem 1.5rem;
            }
            .content-zone {
                padding: 2.5rem;
            }
            h1 {
                font-size: 2.3rem;
            }
            .description {
                max-width: 100%;
            }
        }

        @media (max-width: 550px) {
            body {
                padding: 1rem;
            }
            .content-zone {
                padding: 1.8rem;
            }
            .icon-circle {
                width: 70px;
                height: 70px;
                font-size: 2rem;
            }
            .glitch-text {
                font-size: 2.8rem;
            }
            .btn-primary, .btn-secondary {
                padding: 0.7rem 1.4rem;
                font-size: 0.85rem;
            }
        }

        /* custom scrollbar modern */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f1422;
        }
        ::-webkit-scrollbar-thumb {
            background: #4f46e5;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="premium-card">
    <div class="card-grid">
        <!-- LEFT: Modern visual cluster / premium illustration -->
        <div class="visual-zone">
            <div class="icon-cluster">
                <div class="icon-circle">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="icon-circle">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="icon-circle">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="icon-circle">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="status-code-block">
                <div class="glitch-text">403 | 503</div>
                <div class="maintenance-badge-left">
                    <span class="pulse-dot"></span> 
                    <span>SECURE ZONE • MAINTENANCE CYCLE ACTIVE</span>
                    <i class="fas fa-microchip" style="font-size: 0.75rem; opacity: 0.8;"></i>
                </div>
            </div>
            <div style="margin-top: 2rem; text-align: center; max-width: 260px;">
                <p style="font-size: 0.8rem; color: #8f9bb5; letter-spacing: 0.3px;">
                    <i class="fas fa-sync-alt fa-fw"></i>  Scheduled upgrades in progress
                </p>
            </div>
        </div>

        <!-- RIGHT: Information + premium CTAs -->
        <div class="content-zone">
            <div class="status-badge">
                <i class="fas fa-exclamation-triangle"></i> 
                <span>ACCESS RESTRICTED • UNDER MAINTENANCE</span>
                <i class="fas fa-cog fa-spin"></i>
            </div>
            <h1>
                Access Restricted <br> <span style="font-weight: 500;">& System Refresh</span>
            </h1>
            <p class="description">
                We're currently performing scheduled system upgrades to enhance security and performance. 
                During this window, access is temporarily limited. For authorized personnel, maintenance protocols are active.
            </p>
            <div class="warning-note">
                <i class="fas fa-circle-info"></i> 
                <strong>Maintenance window:</strong> Estimated completion within 2 hours.  
                If you believe this is an error, please contact our infrastructure team.
            </div>
            <div class="btn-group">
                <a href="#" class="btn-primary" id="homepageBtn">
                    <i class="fas fa-arrow-left"></i> Return to Homepage
                </a>
                <a href="#" class="btn-secondary" id="supportBtn">
                    <i class="fas fa-headset"></i> Priority Support
                </a>
            </div>
            <div class="help-link">
                <i class="fas fa-envelope"></i> 
                <span>Admin contact: </span>
                <a href="mailto:security@domain.com">security@domain.com</a>
                <span class="dot-sep">•</span>
                <a href="#" id="statusLink"><i class="fas fa-chart-line"></i> System status</a>
            </div>
            <!-- subtle micro note -->
            <div style="margin-top: 1.8rem; font-size: 0.7rem; color: #505e7e; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
                <i class="fas fa-shield-hooded"></i> Zero-trust proxy active • Gateway  • <span id="liveTimestamp"></span>
            </div>
        </div>
    </div>
</div>

<script>
    // Create timestamp for modern dynamic feel (premium touch)
    function updateTimestamp() {
        const now = new Date();
        const formatted = now.toLocaleString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
        const datePart = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        const timestampSpan = document.getElementById('liveTimestamp');
        if (timestampSpan) {
            timestampSpan.innerHTML = `<i class="far fa-calendar-alt"></i> ${datePart} • ${formatted} UTC`;
        }
    }
    updateTimestamp();
    setInterval(updateTimestamp, 1000);

    // Button actions: (demo elegant notifications, but keep premium without page reload)
    const homepageBtn = document.getElementById('homepageBtn');
    const supportBtn = document.getElementById('supportBtn');
    const statusLink = document.getElementById('statusLink');

    if (homepageBtn) {
        homepageBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // Premium feedback: this could redirect to root but we simulate gentle toast / alert? but better modern slide alert?
            // For demo, we give micro interaction (modern but not intrusive)
            const toastMsg = document.createElement('div');
            toastMsg.innerText = '↻ Redirecting to homepage ...';
            toastMsg.style.position = 'fixed';
            toastMsg.style.bottom = '24px';
            toastMsg.style.left = '50%';
            toastMsg.style.transform = 'translateX(-50%)';
            toastMsg.style.background = '#1e293bdd';
            toastMsg.style.backdropFilter = 'blur(12px)';
            toastMsg.style.padding = '12px 24px';
            toastMsg.style.borderRadius = '60px';
            toastMsg.style.color = '#eef2ff';
            toastMsg.style.fontWeight = '500';
            toastMsg.style.fontSize = '0.85rem';
            toastMsg.style.border = '1px solid #4f46e5';
            toastMsg.style.zIndex = '9999';
            toastMsg.style.fontFamily = "'Inter', sans-serif";
            toastMsg.style.boxShadow = '0 10px 25px -5px rgba(0,0,0,0.3)';
            document.body.appendChild(toastMsg);
            setTimeout(() => {
                toastMsg.style.opacity = '0';
                setTimeout(() => toastMsg.remove(), 500);
            }, 1800);
            // simulate homepage redirect after 0.6s (dummy, just effect)
            setTimeout(() => {
                window.location.href = '/';
            }, 700);
        });
    }

    if (supportBtn) {
        supportBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const supportOverlay = document.createElement('div');
            supportOverlay.innerHTML = `
                <div style="position:fixed; bottom:30px; right:30px; background:#0f172ad9; backdrop-filter:blur(16px); padding: 1rem 1.8rem; border-radius: 40px; color:white; font-family: 'Inter', sans-serif; border:1px solid #a78bfa; z-index:9999; box-shadow:0 20px 35px -10px black; display:flex; align-items:center; gap:12px;">
                    <i class="fas fa-life-ring" style="color:#a78bfa; font-size:1.5rem;"></i>
                    <div><strong>Support ticket opened</strong><br><span style="font-size:0.75rem;">Our team will respond within 15min</span></div>
                </div>
            `;
            document.body.appendChild(supportOverlay);
            setTimeout(() => {
                supportOverlay.style.transition = 'opacity 0.4s';
                supportOverlay.style.opacity = '0';
                setTimeout(() => supportOverlay.remove(), 500);
            }, 4000);
        });
    }

    if (statusLink) {
        statusLink.addEventListener('click', (e) => {
            e.preventDefault();
            const notif = document.createElement('div');
            notif.innerText = '📡 Live system status: core services operational · maintenance mode active';
            notif.style.position = 'fixed';
            notif.style.bottom = '80px';
            notif.style.left = '20px';
            notif.style.background = '#111827ee';
            notif.style.backdropFilter = 'blur(12px)';
            notif.style.padding = '10px 20px';
            notif.style.borderRadius = '28px';
            notif.style.fontSize = '0.8rem';
            notif.style.borderLeft = '3px solid #3b82f6';
            notif.style.color = '#e2e8f0';
            notif.style.fontFamily = "'Inter', sans-serif";
            notif.style.zIndex = '9999';
            document.body.appendChild(notif);
            setTimeout(() => {
                notif.style.transition = 'all 0.3s';
                notif.style.opacity = '0';
                setTimeout(() => notif.remove(), 400);
            }, 3000);
        });
    }

    // adding subtle parallax effect for floating orbs (optional modern feel)
    document.addEventListener('mousemove', (e) => {
        const orbs = document.querySelectorAll('.orb');
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;
        orbs.forEach((orb, idx) => {
            const moveX = (x - 0.5) * 20 * (idx + 1);
            const moveY = (y - 0.5) * 20 * (idx + 1);
            orb.style.transform = `translate(${moveX}px, ${moveY}px) scale(1.05)`;
        });
    });
</script>
</body>
</html>