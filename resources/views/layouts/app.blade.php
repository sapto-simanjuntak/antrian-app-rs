<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Antrian') — {{ config('hospital.name') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ── Design Tokens ── */
        :root {
            --c-primary: #0057FF;
            --c-primary-dark: #003FBB;
            --c-primary-soft: #EEF4FF;
            --c-green: #00C896;
            --c-green-soft: #E6FBF5;
            --c-amber: #FFB800;
            --c-amber-soft: #FFF8E1;
            --c-red: #FF3B5C;
            --c-red-soft: #FFF0F3;
            --c-violet: #7C3AED;
            --c-violet-soft: #F3EEFF;
            --c-text: #0F1828;
            --c-muted: #6B7A99;
            --c-border: #E4EAF4;
            --c-surface: #F5F7FF;
            --c-white: #FFFFFF;
            --radius-lg: 16px;
            --radius-md: 10px;
            --shadow-sm: 0 1px 6px rgba(0, 40, 180, .07);
            --shadow-md: 0 4px 20px rgba(0, 40, 180, .10);
            --shadow-lg: 0 8px 40px rgba(0, 40, 180, .15);
            --font: 'Outfit', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font);
            background: var(--c-surface);
            color: var(--c-text);
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .topbar {
            height: 62px;
            background: var(--c-white);
            border-bottom: 1px solid var(--c-border);
            display: flex;
            align-items: center;
            padding: 0 1.75rem;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: .65rem;
            text-decoration: none;
        }

        .topbar-brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-dark));
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .topbar-brand-text {
            font-size: 1rem;
            font-weight: 800;
            color: var(--c-text);
            line-height: 1.1;
        }

        .topbar-brand-sub {
            font-size: .7rem;
            color: var(--c-muted);
            font-weight: 500;
        }

        .topbar-role {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .role-chip {
            background: var(--c-primary-soft);
            color: var(--c-primary);
            font-weight: 700;
            font-size: .78rem;
            padding: 5px 14px;
            border-radius: 999px;
        }

        /* ── Card ── */
        .qcard {
            background: var(--c-white);
            border: 1px solid var(--c-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .qcard-header {
            padding: 1rem 1.4rem;
            border-bottom: 1px solid var(--c-border);
            display: flex;
            align-items: center;
            gap: .55rem;
            font-weight: 700;
            font-size: .9rem;
            color: var(--c-primary);
        }

        /* ── Toast ── */
        #toast-wrap {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column-reverse;
            gap: .5rem;
        }

        .qt {
            min-width: 270px;
            max-width: 340px;
            padding: .85rem 1.1rem;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: .875rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            animation: qtIn .22s ease;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .18);
        }

        .qt-success {
            background: var(--c-green);
        }

        .qt-error {
            background: var(--c-red);
        }

        .qt-info {
            background: var(--c-primary);
        }

        .qt-warn {
            background: var(--c-amber);
            color: var(--c-text);
        }

        @keyframes qtIn {
            from {
                transform: translateY(16px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* ── Spinner ── */
        .spin {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spinning .7s linear infinite;
            display: inline-block;
        }

        @keyframes spinning {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Badge ── */
        .qbadge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
        }

        .qbadge-waiting {
            background: var(--c-border);
            color: var(--c-muted);
        }

        .qbadge-calling {
            background: var(--c-primary-soft);
            color: var(--c-primary);
        }

        .qbadge-serving {
            background: var(--c-violet-soft);
            color: var(--c-violet);
        }

        .qbadge-paused {
            background: var(--c-amber-soft);
            color: #8a6200;
        }

        .qbadge-done {
            background: var(--c-green-soft);
            color: #006844;
        }

        .qbadge-cancelled {
            background: var(--c-red-soft);
            color: #c0002a;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--c-border);
            border-radius: 99px;
        }
    </style>

    @stack('styles')
</head>

<body>

    <nav class="topbar">
        <a href="/kiosk" class="topbar-brand">
            <div class="topbar-brand-icon"><i class="bi bi-hospital-fill"></i></div>
            <div>
                <div class="topbar-brand-text">{{ config('hospital.name') }}</div>
                <div class="topbar-brand-sub">Sistem Antrian Digital</div>
            </div>
        </a>
        <div class="topbar-role">
            @yield('nav-extra')
            <div class="role-chip"><i class="bi bi-@yield('role-icon', 'grid')"></i> @yield('role-label', 'Dashboard')</div>

            @auth
                <div style="display:flex;align-items:center;gap:.5rem;margin-left:.5rem;">
                    <div style="font-size:.78rem;color:var(--c-muted);font-weight:600;line-height:1.2;text-align:right;">
                        <div style="color:var(--c-text);font-weight:700;">{{ Auth::user()->name }}</div>
                        <div>{{ Auth::user()->isAdmin() ? 'Administrator' : 'Operator' }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" title="Keluar"
                            style="background:var(--c-red-soft);color:var(--c-red);border:1px solid rgba(255,59,92,.2);
                           border-radius:8px;padding:6px 10px;cursor:pointer;font-size:.82rem;
                           font-weight:700;display:flex;align-items:center;gap:.3rem;
                           transition:background .15s;"
                            onmouseover="this.style.background='var(--c-red)';this.style.color='#fff'"
                            onmouseout="this.style.background='var(--c-red-soft)';this.style.color='var(--c-red)'">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="py-4 px-3 px-md-4">
        @yield('content')
    </main>

    <div id="toast-wrap"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* ── Global AJAX ── */
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        async function api(url, method = 'GET', body = null) {
            const cfg = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
            };
            if (body) cfg.body = JSON.stringify(body);
            const r = await fetch(url, cfg);
            return {
                ok: r.ok,
                status: r.status,
                data: await r.json()
            };
        }

        /* ── Toast ── */
        function toast(msg, type = 'success', ms = 3500) {
            const icons = {
                success: 'check-circle-fill',
                error: 'x-circle-fill',
                info: 'info-circle-fill',
                warn: 'exclamation-triangle-fill'
            };
            const el = Object.assign(document.createElement('div'), {
                className: `qt qt-${type}`,
                innerHTML: `<i class="bi bi-${icons[type]||'info-circle-fill'}"></i><span>${msg}</span>`,
            });
            document.getElementById('toast-wrap').prepend(el);
            setTimeout(() => el.remove(), ms);
        }
    </script>
    @stack('scripts')
</body>

</html>
