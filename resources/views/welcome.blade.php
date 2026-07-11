<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCC-CS | Consumable Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .landing-wrap {
            display: flex;
            width: 100%;
            max-width: 960px;
            height: 600px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.14);
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            flex: 1.1;
            position: relative;
            background: #1a6b3a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            overflow: hidden;
        }

        .left-bg {
            position: absolute; inset: 0;
            background: url('{{ asset("images/ucc-background.jpg") }}') center/cover no-repeat;
            opacity: 0.15;
        }

        .left-content {
            position: relative;
            text-align: center;
            color: #fff;
        }

        .ucc-logo {
            width: 88px; height: 88px;
            border-radius: 50%;
            background: rgba(255,255,255,0.92);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
            overflow: hidden;
        }

        .ucc-logo img {
            width: 76px; height: 76px;
            object-fit: contain;
        }

        .left-est {
            font-size: 10px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.65);
            background: rgba(255,255,255,0.1);
            border: 0.5px solid rgba(255,255,255,0.2);
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 1.4rem;
            display: inline-block;
        }

        .left-title {
            font-size: 30px;
            font-weight: 700;
            line-height: 1.2;
            color: #fff;
        }

        .left-title span { color: #6ed694; }

        .left-subtitle {
            font-size: 13px;
            color: rgba(255,255,255,0.65);
            line-height: 1.6;
            max-width: 280px;
            margin: 0.85rem auto 0;
        }

        .left-dots {
            position: absolute;
            bottom: 1.4rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex; gap: 6px;
        }

        .dot { width: 7px; height: 7px; border-radius: 50%; background: rgba(255,255,255,0.35); }
        .dot.active { background: #fff; }

        /* ── RIGHT PANEL ── */
        .right-panel {
            flex: 0.9;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.8rem;
        }

        .brand-row {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 2.5rem;
        }

        .brand-icon {
            width: 40px; height: 40px;
            background: #1a56db;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px;
        }

        .brand-name { font-size: 14px; font-weight: 600; color: #111; line-height: 1.2; }
        .brand-sub  { font-size: 11px; color: #888; }

        .welcome-title {
            font-size: 26px;
            font-weight: 700;
            color: #111;
            margin-bottom: 0.5rem;
        }

        .welcome-desc {
            font-size: 13px;
            color: #888;
            line-height: 1.6;
            margin-bottom: 2.2rem;
            max-width: 280px;
        }

        .action-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #aaa;
            margin-bottom: 0.85rem;
            font-weight: 500;
        }

        .btn-login {
            display: flex; align-items: center; justify-content: space-between;
            width: 100%;
            padding: 15px 20px;
            background: #1a56db;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 0.8rem;
            transition: background 0.18s, transform 0.12s;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
        }
        .btn-login:hover { background: #1648c0; transform: translateY(-1px); color: #fff; }

        .btn-register {
            display: flex; align-items: center; justify-content: space-between;
            width: 100%;
            padding: 15px 20px;
            background: transparent;
            color: #1a56db;
            border: 1.5px solid #1a56db;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s, transform 0.12s;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
        }
        .btn-register:hover { background: #eff6ff; transform: translateY(-1px); }

        .btn-label { display: flex; align-items: center; gap: 10px; }

        .btn-icon {
            width: 28px; height: 28px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
        }
        .btn-login    .btn-icon { background: rgba(255,255,255,0.18); color: #fff; }
        .btn-register .btn-icon { background: rgba(26,86,219,0.1);   color: #1a56db; }

        .divider {
            display: flex; align-items: center; gap: 10px;
            margin: 0.6rem 0;
            color: #ccc; font-size: 11px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 0.5px; background: #e5e5e5;
        }

        .dev-link {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; font-size: 12px; color: #aaa;
            text-decoration: none; margin-top: 1.5rem;
            cursor: pointer; background: none; border: none;
            font-family: 'Inter', sans-serif;
            transition: color 0.15s;
        }
        .dev-link:hover { color: #1a56db; }

        .footer-note {
            font-size: 11px;
            color: #bbb;
            text-align: center;
            margin-top: 0.75rem;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }

        /* ── MODAL ── */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 300;
            align-items: center; justify-content: center;
            padding: 1rem;
        }
        .modal-overlay.open { display: flex; }

        .modal-box {
            background: #fff; border-radius: 16px;
            padding: 2rem; width: 100%; max-width: 640px;
            max-height: 90vh; overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0,0,0,0.18);
            animation: dropIn 0.2s ease;
        }

        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .modal-title {
            font-size: 18px; font-weight: 700; color: #111;
            display: flex; align-items: center; gap: 8px;
        }
        .modal-title i { color: #1a56db; }

        .modal-sub { font-size: 13px; color: #888; margin-bottom: 1.5rem; }

        .modal-close {
            width: 30px; height: 30px; border-radius: 8px;
            border: 1px solid #e0e0e0; background: #fff;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: #999; font-size: 15px;
        }
        .modal-close:hover { background: #fff5f5; color: #e24b4a; border-color: #e24b4a; }

        .dev-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;
        }
        @media(max-width:560px) { .dev-grid { grid-template-columns: repeat(2, 1fr); } }

        .dev-card {
            text-align: center; padding: 1.25rem 0.75rem;
            border: 1.5px solid #eee; border-radius: 14px;
            transition: all 0.2s;
        }
        .dev-card:hover { border-color: #1a56db; box-shadow: 0 6px 16px rgba(0,0,0,0.06); transform: translateY(-2px); }

        .dev-avatar {
            width: 64px; height: 64px; border-radius: 50%;
            margin: 0 auto 0.85rem; overflow: hidden;
            border: 3px solid #eff6ff;
        }
        .dev-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .dev-avatar-fallback {
            width: 100%; height: 100%; background: #1a56db; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 700;
        }

        .dev-name { font-size: 13px; font-weight: 700; color: #111; margin-bottom: 3px; line-height: 1.3; }
        .dev-role { font-size: 11px; color: #1a56db; font-weight: 500; line-height: 1.4; }

        .dev-footer {
            text-align: center; font-size: 12px; color: #999;
            margin-top: 1.5rem; padding-top: 1rem;
            border-top: 1px solid #eee;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }

        .secure-note {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; font-size: 11.5px; color: #aaa; margin-top: 0.6rem;
        }

        /* ── System Restored Toast ── */
        .system-up-toast {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 400;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #fff;
            border-left: 4px solid #1a6b3a;
            border-radius: 10px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.16);
            padding: 14px 16px;
            max-width: 340px;
            animation: toastSlideIn 0.35s ease;
        }
        .system-up-toast.toast-hide {
            animation: toastSlideOut 0.35s ease forwards;
        }
        @keyframes toastSlideIn {
            from { transform: translateX(30px); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
        @keyframes toastSlideOut {
            from { transform: translateX(0); opacity: 1; }
            to   { transform: translateX(30px); opacity: 0; }
        }
        .toast-icon {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #f0faf4;
            color: #1a6b3a;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }
        .toast-title { font-size: 13px; font-weight: 700; color: #111; margin-bottom: 2px; }
        .toast-reason { font-size: 12px; color: #666; line-height: 1.4; }
        .toast-close {
            background: none; border: none; cursor: pointer;
            color: #bbb; font-size: 13px; padding: 2px;
            flex-shrink: 0; margin-left: auto;
        }
        .toast-close:hover { color: #888; }

        @media (max-width: 640px) {
            .landing-wrap { flex-direction: column; height: auto; }
            .left-panel { min-height: 220px; padding: 2rem; }
            .right-panel { padding: 2rem 1.5rem; }
            .left-dots { display: none; }
            .system-up-toast { left: 1rem; right: 1rem; max-width: none; top: 1rem; }
        }
    </style>
</head>
<body>

@if($justRestored ?? null)
<div class="system-up-toast" id="system-up-toast">
    <div class="toast-icon"><i class="ti ti-circle-check"></i></div>
    <div style="flex:1;">
        <div class="toast-title">System is back online</div>
        <div class="toast-reason">{{ $justRestored->reason ?? 'The system has been restored and is now accessible.' }}</div>
    </div>
    <button class="toast-close" onclick="dismissSystemToast()"><i class="ti ti-x"></i></button>
</div>
<script>
    (function() {
        const toast = document.getElementById('system-up-toast');
        if (!toast) return;
        setTimeout(function() { dismissSystemToast(); }, 6000);
    })();
    function dismissSystemToast() {
        const toast = document.getElementById('system-up-toast');
        if (!toast) return;
        toast.classList.add('toast-hide');
        setTimeout(function() { toast.remove(); }, 350);
    }
</script>
@endif

<div class="landing-wrap">

    {{-- LEFT: Campus Hero --}}
    <div class="left-panel">
        <div class="left-bg"></div>
        <div class="left-content">
            <div class="ucc-logo">
                <img src="{{ asset('images/ucc.png') }}" alt="UCC Logo" onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\'font-size:28px; font-weight:700; color:#1a56db;\'>UCC</span>'">
            </div>
            <div class="left-est">Est. 1975 · Caloocan City</div>
            <div class="left-title">
                University of<br>
                <span>Caloocan City</span>
            </div>
            <p class="left-subtitle">
                Track and manage office supplies, laboratory consumables,
                and inventory levels — all in one unified platform.
            </p>
        </div>
        <div class="left-dots">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>

    {{-- RIGHT: Action Panel --}}
    <div class="right-panel">

        <div class="brand-row">
            <div class="brand-icon"><i class="ti ti-package"></i></div>
            <div>
                <div class="brand-name">UCC-CS</div>
                <div class="brand-sub">Consumable System</div>
            </div>
        </div>

        <div class="welcome-title">Get Started</div>
        <p class="welcome-desc">
            Access your consumables dashboard or create a new account
            to begin requesting university supplies.
        </p>

        <div class="action-label">Choose an option</div>

        <a href="{{ route('login') }}" class="btn-login">
            <div class="btn-label">
                <div class="btn-icon"><i class="ti ti-login"></i></div>
                Log in your account
            </div>
            <span>→</span>
        </a>

        <div class="divider">or</div>

        <a href="{{ route('register') }}" class="btn-register">
            <div class="btn-label">
                <div class="btn-icon"><i class="ti ti-user-plus"></i></div>
                Register an account
            </div>
            <span>→</span>
        </a>

        <button class="dev-link" onclick="document.getElementById('dev-modal').classList.add('open')">
            <i class="ti ti-code"></i> Development Team
            <i class="ti ti-chevron-right" style="font-size:11px;"></i>
        </button>

        <div class="secure-note">
            <i class="ti ti-shield-check" style="color:#1a56db; font-size:13px;"></i>
            Secure System • v2.7.1
        </div>

        <div class="footer-note">
            © {{ date('Y') }} University of Caloocan City
        </div>

    </div>
</div>

{{-- DEVELOPER MODAL --}}
<div class="modal-overlay" id="dev-modal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title"><i class="ti ti-users"></i> Meet the Developers</div>
            <button class="modal-close" onclick="document.getElementById('dev-modal').classList.remove('open')"><i class="ti ti-x"></i></button>
        </div>
        <p class="modal-sub">The team behind the UCC Consumable System.</p>

        <div class="dev-grid">
            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/matt.jpg') }}" alt="Ryan Mateo"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">RM</div>
                </div>
                <div class="dev-name">Ryan Mateo</div>
                <div class="dev-role">System Analyst / Project Manager</div>
            </div>
            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/greg.png') }}" alt="James Ryan Gregorio"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">JG</div>
                </div>
                <div class="dev-name">James Ryan Gregorio</div>
                <div class="dev-role">Full Stack Developer</div>
            </div>
            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/ureta.png') }}" alt="Jan Ermaine Ureta"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">JU</div>
                </div>
                <div class="dev-name">Jan Ermaine Ureta</div>
                <div class="dev-role">UI / Front-end Developer</div>
            </div>
            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/renz.jpg') }}" alt="Renzel Rodriguez"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">RR</div>
                </div>
                <div class="dev-name">Renzel Rodriguez</div>
                <div class="dev-role">Full Stack Developer</div>
            </div>
            <div class="dev-card">
                <div class="dev-avatar">
                    <img src="{{ asset('images/developers/ian.jpg') }}" alt="Iankyron Chan"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dev-avatar-fallback" style="display:none;">IC</div>
                </div>
                <div class="dev-name">Iankyron Chan</div>
                <div class="dev-role">Backend Developer / DBA</div>
            </div>
        </div>

        <div class="dev-footer">
            <i class="ti ti-heart" style="color:#1a56db;"></i>
            Built with dedication for the University of Caloocan City
        </div>
    </div>
</div>

<script>
document.getElementById('dev-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
</script>

</body>
</html>