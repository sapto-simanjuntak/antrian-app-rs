<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Operator — {{ config('hospital.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --c-primary: #0057FF;
            --c-primary-dark: #003FBB;
            --c-primary-soft: #EEF4FF;
            --c-text: #0F1828;
            --c-muted: #6B7A99;
            --c-border: #E4EAF4;
            --c-surface: #F5F7FF;
            --c-white: #FFFFFF;
            --c-red: #FF3B5C;
            --c-red-soft: #FFF0F3;
            --c-green: #00C896;
            --font: 'Outfit', sans-serif;
            --font-mono: 'DM Mono', monospace;
            --shadow-sm: 0 1px 6px rgba(0, 40, 180, .07);
            --shadow-md: 0 4px 24px rgba(0, 40, 180, .12);
            --shadow-lg: 0 16px 48px rgba(0, 40, 180, .18);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            font-family: var(--font);
            background: var(--c-surface);
            color: var(--c-text);
        }

        /* ── Full-page layout ── */
        .login-root {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 480px;
        }

        /* ── Left panel — branding ── */
        .login-left {
            background: linear-gradient(145deg, var(--c-primary) 0%, var(--c-primary-dark) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 4rem;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles */
        .login-left::before,
        .login-left::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .06);
        }

        .login-left::before {
            width: 500px;
            height: 500px;
            top: -150px;
            right: -150px;
        }

        .login-left::after {
            width: 300px;
            height: 300px;
            bottom: -80px;
            left: -80px;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, .15);
            border: 2px solid rgba(255, 255, 255, .25);
            border-radius: 22px;
            display: grid;
            place-items: center;
            font-size: 2.4rem;
            color: #fff;
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }

        .brand-name {
            font-size: 1.9rem;
            font-weight: 900;
            color: #fff;
            text-align: center;
            line-height: 1.2;
            margin-bottom: .5rem;
            position: relative;
            z-index: 1;
        }

        .brand-tagline {
            font-size: .92rem;
            color: rgba(255, 255, 255, .7);
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            z-index: 1;
        }

        /* Info cards */
        .brand-cards {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            width: 100%;
            max-width: 340px;
            position: relative;
            z-index: 1;
        }

        .brand-card {
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 14px;
            padding: .9rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #fff;
        }

        .brand-card-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, .15);
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 1.15rem;
        }

        .brand-card-title {
            font-size: .85rem;
            font-weight: 700;
        }

        .brand-card-sub {
            font-size: .74rem;
            color: rgba(255, 255, 255, .65);
            margin-top: .1rem;
        }

        /* ── Right panel — form ── */
        .login-right {
            background: var(--c-white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3rem;
            box-shadow: -8px 0 40px rgba(0, 40, 180, .08);
        }

        .login-form-wrap {
            max-width: 360px;
            width: 100%;
            margin: 0 auto;
        }

        .login-heading {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--c-text);
            margin-bottom: .4rem;
        }

        .login-sub {
            font-size: .88rem;
            color: var(--c-muted);
            margin-bottom: 2rem;
        }

        /* Error alert */
        .alert-error {
            background: var(--c-red-soft);
            border: 1px solid rgba(255, 59, 92, .2);
            border-radius: 12px;
            padding: .85rem 1rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .85rem;
            font-weight: 600;
            color: var(--c-red);
            margin-bottom: 1.25rem;
        }

        /* Success alert (after redirect with message) */
        .alert-success {
            background: #E6FBF5;
            border: 1px solid rgba(0, 200, 150, .2);
            border-radius: 12px;
            padding: .85rem 1rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .85rem;
            font-weight: 600;
            color: #006844;
            margin-bottom: 1.25rem;
        }

        /* Form group */
        .fgroup {
            margin-bottom: 1.25rem;
        }

        .flabel {
            display: block;
            margin-bottom: .45rem;
            font-size: .83rem;
            font-weight: 700;
            color: var(--c-text);
        }

        .finput-wrap {
            position: relative;
        }

        .finput-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--c-muted);
            font-size: 1rem;
            pointer-events: none;
        }

        .finput {
            width: 100%;
            height: 48px;
            padding: 0 1rem 0 2.75rem;
            border: 1.5px solid var(--c-border);
            border-radius: 12px;
            font-family: var(--font);
            font-size: .92rem;
            color: var(--c-text);
            background: var(--c-surface);
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        .finput:focus {
            border-color: var(--c-primary);
            background: var(--c-white);
            box-shadow: 0 0 0 3px rgba(0, 87, 255, .12);
        }

        .finput.is-invalid {
            border-color: var(--c-red);
        }

        .finput.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(255, 59, 92, .12);
        }

        /* Password toggle */
        .finput-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--c-muted);
            font-size: 1rem;
            padding: 0;
            transition: color .15s;
        }

        .finput-toggle:hover {
            color: var(--c-primary);
        }

        .finput-error {
            font-size: .78rem;
            color: var(--c-red);
            font-weight: 600;
            margin-top: .35rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* Remember me */
        .fcheck {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .84rem;
            color: var(--c-muted);
            cursor: pointer;
            user-select: none;
            margin-bottom: 1.5rem;
        }

        .fcheck input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--c-primary);
            cursor: pointer;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, var(--c-primary) 0%, var(--c-primary-dark) 100%);
            color: #fff;
            border: none;
            border-radius: 13px;
            font-family: var(--font);
            font-size: .95rem;
            font-weight: 800;
            cursor: pointer;
            letter-spacing: .3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: transform .15s, box-shadow .15s, filter .15s;
            box-shadow: 0 4px 16px rgba(0, 87, 255, .3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 87, 255, .35);
            filter: brightness(1.06);
        }

        .btn-login:active {
            transform: scale(.97);
        }

        .btn-login:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
            filter: none;
        }

        /* Divider */
        .login-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--c-border);
        }

        .login-divider span {
            font-size: .75rem;
            color: var(--c-muted);
            font-weight: 600;
            white-space: nowrap;
        }

        /* Public links */
        .public-links {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .65rem;
        }

        .pub-link {
            padding: .75rem;
            border: 1.5px solid var(--c-border);
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .3rem;
            color: var(--c-muted);
            font-size: .78rem;
            font-weight: 700;
            transition: border-color .15s, color .15s, background .15s;
            text-align: center;
        }

        .pub-link i {
            font-size: 1.2rem;
        }

        .pub-link:hover {
            border-color: var(--c-primary);
            color: var(--c-primary);
            background: var(--c-primary-soft);
        }

        /* Footer */
        .login-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: .75rem;
            color: var(--c-muted);
        }

        /* Spinner */
        .spin {
            width: 18px;
            height: 18px;
            border: 2.5px solid rgba(255, 255, 255, .35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spinning .7s linear infinite;
            display: none;
        }

        @keyframes spinning {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-root {
                grid-template-columns: 1fr;
            }

            .login-left {
                display: none;
            }

            .login-right {
                padding: 2.5rem 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-root">

        {{-- ── Left: Branding ── --}}
        <div class="login-left">
            <div class="brand-logo"><i class="bi bi-hospital-fill"></i></div>
            <div class="brand-name">{{ config('hospital.name') }}</div>
            <div class="brand-tagline">Melayani dengan Hati dan Profesional</div>

            <div class="brand-cards">
                <div class="brand-card">
                    <div class="brand-card-icon"><i class="bi bi-shield-check-fill"></i></div>
                    <div>
                        <div class="brand-card-title">Loket 1 — BPJS Kesehatan</div>
                        <div class="brand-card-sub">Peserta BPJS aktif & Jamkesda</div>
                    </div>
                </div>
                <div class="brand-card">
                    <div class="brand-card-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <div>
                        <div class="brand-card-title">Loket 2 — Pasien Umum</div>
                        <div class="brand-card-sub">Pasien umum & asuransi swasta</div>
                    </div>
                </div>
                <div class="brand-card">
                    <div class="brand-card-icon"><i class="bi bi-person-hearts"></i></div>
                    <div>
                        <div class="brand-card-title">Loket 3 — Pasien Lansia</div>
                        <div class="brand-card-sub">Prioritas usia 60 tahun ke atas</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Right: Form ── --}}
        <div class="login-right">
            <div class="login-form-wrap">

                <div class="login-heading">Masuk Operator</div>
                <div class="login-sub">Gunakan akun yang diberikan administrator RS</div>

                {{-- Error alert --}}
                @if ($errors->any())
                    <div class="alert-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Session message (misal setelah logout) --}}
                @if (session('error'))
                    <div class="alert-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                    @csrf

                    {{-- Email --}}
                    <div class="fgroup">
                        <label class="flabel" for="email">Email</label>
                        <div class="finput-wrap">
                            <i class="bi bi-envelope-fill finput-icon"></i>
                            <input type="email" id="email" name="email"
                                class="finput {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                value="{{ old('email') }}" placeholder="operator@{{ config('hospital.email') }}" autocomplete="email"
                                autofocus required>
                        </div>
                        @error('email')
                            <div class="finput-error">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="fgroup">
                        <label class="flabel" for="password">Password</label>
                        <div class="finput-wrap">
                            <i class="bi bi-lock-fill finput-icon"></i>
                            <input type="password" id="password" name="password"
                                class="finput {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="••••••••"
                                autocomplete="current-password" required>
                            <button type="button" class="finput-toggle" onclick="togglePassword()" id="eyeBtn">
                                <i class="bi bi-eye-fill" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <label class="fcheck">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya di perangkat ini
                    </label>

                    {{-- Submit --}}
                    <button type="submit" class="btn-login" id="btnLogin">
                        <div class="spin" id="btnSpin"></div>
                        <i class="bi bi-box-arrow-in-right" id="btnIcon"></i>
                        <span id="btnText">Masuk ke Sistem</span>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="login-divider">
                    <span>Akses Publik</span>
                </div>

                {{-- Public links --}}
                <div class="public-links">
                    <a href="{{ route('kiosk.index') }}" class="pub-link">
                        <i class="bi bi-ticket-perforated-fill"></i>
                        Terminal Pasien
                    </a>
                    <a href="{{ route('display.index') }}" class="pub-link">
                        <i class="bi bi-tv-fill"></i>
                        Display Antrian
                    </a>
                </div>

                <div class="login-footer">
                    &copy; {{ date('Y') }} {{ config('hospital.name') }} — Sistem Antrian Digital
                </div>
            </div>
        </div>

    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill';
        }

        // Loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnLogin');
            const spin = document.getElementById('btnSpin');
            const icon = document.getElementById('btnIcon');
            const txt = document.getElementById('btnText');

            btn.disabled = true;
            spin.style.display = 'block';
            icon.style.display = 'none';
            txt.textContent = 'Memverifikasi...';
        });
    </script>
</body>

</html>
