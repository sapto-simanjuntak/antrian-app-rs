<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian — RS Sehat Sentosa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700;800;900&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bg: #EEF3FF;
            --panel: #FFFFFF;
            --border: #D8E3F8;
            --text: #0F1828;
            --muted: #7A8BB0;
            --surface: #F5F8FF;

            --b1: #1565C0;
            --b1-soft: #E3EDFF;
            --b1-mid: #BBCFEE;
            --b2: #00796B;
            --b2-soft: #DFF4F1;
            --b2-mid: #B2DFDB;
            --b3: #D84315;
            --b3-soft: #FBE9E7;
            --b3-mid: #FFCCBC;

            --font: 'Lexend', sans-serif;
            --mono: 'DM Mono', monospace;
            --radius: 20px;
            --shadow: 0 2px 12px rgba(21, 101, 192, .09);
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
            overflow: hidden;
            background: var(--bg);
            color: var(--text);
            font-family: var(--font);
        }

        .dsp-root {
            height: 100vh;
            display: grid;
            grid-template-rows: 76px 1fr 54px;
        }

        /* ── Header ── */
        .dsp-head {
            background: var(--b1);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            gap: 1.1rem;
            box-shadow: 0 4px 20px rgba(21, 101, 192, .3);
            position: relative;
            z-index: 2;
        }

        .dsp-logo {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, .18);
            border: 1.5px solid rgba(255, 255, 255, .3);
            border-radius: 13px;
            display: grid;
            place-items: center;
            font-size: 1.4rem;
            color: #fff;
        }

        .dsp-brand h1 {
            font-size: 1.15rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: .2px;
        }

        .dsp-brand p {
            font-size: .72rem;
            color: rgba(255, 255, 255, .72);
            margin-top: .1rem;
        }

        .dsp-clock {
            margin-left: auto;
            text-align: right;
        }

        .dsp-time {
            font-family: var(--mono);
            font-size: 2.5rem;
            font-weight: 500;
            color: #fff;
            line-height: 1;
        }

        .dsp-date {
            font-size: .72rem;
            color: rgba(255, 255, 255, .72);
            margin-top: .15rem;
        }

        /* ── Panels ── */
        .dsp-panels {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.2rem;
            padding: 1.2rem 1.4rem;
        }

        .lp {
            background: var(--panel);
            border: 2px solid var(--border);
            border-radius: var(--radius);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: border-color .3s, box-shadow .3s, transform .3s;
            box-shadow: var(--shadow);
            position: relative;
        }

        .lp.glow-1 {
            border-color: var(--b1);
            box-shadow: 0 0 0 3px var(--b1-mid), 0 12px 40px rgba(21, 101, 192, .18);
            transform: translateY(-4px);
        }

        .lp.glow-2 {
            border-color: var(--b2);
            box-shadow: 0 0 0 3px var(--b2-mid), 0 12px 40px rgba(0, 121, 107, .18);
            transform: translateY(-4px);
        }

        .lp.glow-3 {
            border-color: var(--b3);
            box-shadow: 0 0 0 3px var(--b3-mid), 0 12px 40px rgba(216, 67, 21, .18);
            transform: translateY(-4px);
        }

        .lp::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
        }

        .lp-1::before {
            background: linear-gradient(90deg, #1565C0, #42A5F5);
        }

        .lp-2::before {
            background: linear-gradient(90deg, #00796B, #4DB6AC);
        }

        .lp-3::before {
            background: linear-gradient(90deg, #D84315, #FF8A65);
        }

        /* Head */
        .lp-head {
            padding: 1rem 1.4rem .85rem;
            display: flex;
            align-items: center;
            gap: .85rem;
            border-bottom: 1.5px solid var(--border);
        }

        .lp-head-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .lp-1 .lp-head-icon {
            background: var(--b1-soft);
            color: var(--b1);
        }

        .lp-2 .lp-head-icon {
            background: var(--b2-soft);
            color: var(--b2);
        }

        .lp-3 .lp-head-icon {
            background: var(--b3-soft);
            color: var(--b3);
        }

        .lp-head h2 {
            font-size: .98rem;
            font-weight: 800;
            color: var(--text);
        }

        .lp-head p {
            font-size: .72rem;
            color: var(--muted);
            margin-top: .1rem;
        }

        /* Body */
        .lp-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.25rem 1rem;
            transition: background .3s;
        }

        .lp.glow-1 .lp-body {
            background: linear-gradient(180deg, var(--b1-soft) 0%, rgba(255, 255, 255, 0) 55%);
        }

        .lp.glow-2 .lp-body {
            background: linear-gradient(180deg, var(--b2-soft) 0%, rgba(255, 255, 255, 0) 55%);
        }

        .lp.glow-3 .lp-body {
            background: linear-gradient(180deg, var(--b3-soft) 0%, rgba(255, 255, 255, 0) 55%);
        }

        .calling-tag {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: 5px 16px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: .85rem;
            color: #fff;
            opacity: 0;
            transition: opacity .3s;
        }

        .lp.glow-1 .calling-tag {
            opacity: 1;
            background: var(--b1);
        }

        .lp.glow-2 .calling-tag {
            opacity: 1;
            background: var(--b2);
        }

        .lp.glow-3 .calling-tag {
            opacity: 1;
            background: var(--b3);
        }

        .lp-number {
            font-family: var(--mono);
            font-size: clamp(4.5rem, 8vw, 8.5rem);
            font-weight: 500;
            line-height: 1;
            letter-spacing: -4px;
            transition: color .3s;
        }

        .lp-1 .lp-number {
            color: var(--b1);
        }

        .lp-2 .lp-number {
            color: var(--b2);
        }

        .lp-3 .lp-number {
            color: var(--b3);
        }

        .lp-number.empty {
            color: #C8D5EE !important;
            font-size: clamp(2.5rem, 4vw, 3.5rem);
        }

        .lp-kode {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 5px;
            margin-top: .4rem;
            transition: color .3s;
        }

        .lp-1 .lp-kode {
            color: var(--b1);
        }

        .lp-2 .lp-kode {
            color: var(--b2);
        }

        .lp-3 .lp-kode {
            color: var(--b3);
        }

        .lp-kode.empty {
            color: #C8D5EE;
        }

        .lp-status-row {
            display: flex;
            align-items: center;
            gap: .45rem;
            margin-top: .85rem;
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
            transition: color .3s;
        }

        .lp-status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #C8D5EE;
            transition: background .3s;
        }

        .lp.glow-1 .lp-status-row {
            color: var(--b1);
        }

        .lp.glow-1 .lp-status-dot {
            background: var(--b1);
            animation: blink 1s ease infinite;
        }

        .lp.glow-2 .lp-status-row {
            color: var(--b2);
        }

        .lp.glow-2 .lp-status-dot {
            background: var(--b2);
            animation: blink 1s ease infinite;
        }

        .lp.glow-3 .lp-status-row {
            color: var(--b3);
        }

        .lp.glow-3 .lp-status-dot {
            background: var(--b3);
            animation: blink 1s ease infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .2
            }
        }

        /* Footer */
        .lp-foot {
            padding: .8rem 1.4rem;
            border-top: 1.5px solid var(--border);
            background: var(--surface);
            display: flex;
            gap: 1.5rem;
        }

        .lp-foot-stat {
            font-size: .76rem;
            color: var(--muted);
            font-weight: 600;
        }

        .lp-foot-stat strong {
            color: var(--text);
            font-weight: 800;
        }

        /* Flash */
        @keyframes numFlash {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            25%,
            75% {
                opacity: .15;
                transform: scale(.92);
            }
        }

        .flashing .lp-number {
            animation: numFlash .42s ease 4;
        }

        /* TTS indicator */
        .tts-indicator {
            position: fixed;
            top: 1rem;
            right: 1.4rem;
            z-index: 99;
            background: var(--b1);
            border-radius: 999px;
            padding: 7px 16px;
            font-size: .75rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: .3px;
            display: flex;
            align-items: center;
            gap: .5rem;
            opacity: 0;
            transition: opacity .3s;
            pointer-events: none;
            box-shadow: 0 4px 16px rgba(21, 101, 192, .4);
        }

        .tts-indicator.show {
            opacity: 1;
        }

        .tts-wave {
            display: flex;
            align-items: center;
            gap: 2px;
            height: 14px;
        }

        .tts-wave span {
            width: 3px;
            background: rgba(255, 255, 255, .9);
            border-radius: 2px;
            animation: wave .8s ease infinite;
        }

        .tts-wave span:nth-child(1) {
            height: 5px;
            animation-delay: 0s;
        }

        .tts-wave span:nth-child(2) {
            height: 12px;
            animation-delay: .1s;
        }

        .tts-wave span:nth-child(3) {
            height: 8px;
            animation-delay: .2s;
        }

        .tts-wave span:nth-child(4) {
            height: 14px;
            animation-delay: .3s;
        }

        .tts-wave span:nth-child(5) {
            height: 5px;
            animation-delay: .4s;
        }

        @keyframes wave {

            0%,
            100% {
                transform: scaleY(1)
            }

            50% {
                transform: scaleY(.35)
            }
        }

        /* Ticker */
        .dsp-ticker {
            background: var(--b1);
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .ticker-label {
            background: rgba(0, 0, 0, .2);
            color: #fff;
            font-weight: 800;
            font-size: .73rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 0 1.2rem;
            height: 100%;
            display: flex;
            align-items: center;
            flex-shrink: 0;
            border-right: 1px solid rgba(255, 255, 255, .15);
        }

        .ticker-track {
            flex: 1;
            overflow: hidden;
        }

        .ticker-inner {
            display: inline-flex;
            gap: 5rem;
            white-space: nowrap;
            animation: tickMove 32s linear infinite;
            color: rgba(255, 255, 255, .85);
            font-size: .82rem;
            padding: .75rem 0;
        }

        @keyframes tickMove {
            from {
                transform: translateX(100vw);
            }

            to {
                transform: translateX(-100%);
            }
        }

        .ticker-inner span {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }

        .ticker-inner span::before {
            content: '◆';
            font-size: .38rem;
            color: rgba(255, 255, 255, .45);
        }
    </style>
</head>

<body>
    <div class="dsp-root">

        {{-- Header --}}
        <header class="dsp-head">
            <div class="dsp-logo"><i class="bi bi-hospital-fill"></i></div>
            <div class="dsp-brand">
                <h1>RUMAH SAKIT SEHAT SENTOSA</h1>
                <p>Jl. Kesehatan No. 1, Surabaya, Jawa Timur &nbsp;|&nbsp; Sistem Antrian Digital</p>
            </div>
            <div class="dsp-clock">
                <div class="dsp-time" id="dClock">--:--:--</div>
                <div class="dsp-date" id="dDate">—</div>
            </div>
        </header>

        {{-- TTS indicator --}}
        <div class="tts-indicator" id="ttsIndicator">
            <div class="tts-wave">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            Memanggil...
        </div>

        {{-- 3 Panels --}}
        <div class="dsp-panels">
            @foreach ([['id' => 1, 'icon' => 'shield-check-fill', 'label' => 'Loket 1', 'sub' => 'BPJS Kesehatan'], ['id' => 2, 'icon' => 'person-badge-fill', 'label' => 'Loket 2', 'sub' => 'Pasien Umum'], ['id' => 3, 'icon' => 'person-hearts', 'label' => 'Loket 3', 'sub' => 'Pasien Lansia']] as $lk)
                <div class="lp lp-{{ $lk['id'] }}" id="panel-{{ $lk['id'] }}">
                    <div class="lp-head">
                        <div class="lp-head-icon"><i class="bi bi-{{ $lk['icon'] }}"></i></div>
                        <div>
                            <h2>{{ $lk['label'] }}</h2>
                            <p>{{ $lk['sub'] }}</p>
                        </div>
                    </div>
                    <div class="lp-body">
                        <div class="calling-tag" id="ctag-{{ $lk['id'] }}">
                            <i class="bi bi-person-check-fill"></i> Sedang Dilayani
                        </div>
                        <div class="lp-number empty" id="num-{{ $lk['id'] }}">—</div>
                        <div class="lp-kode   empty" id="kode-{{ $lk['id'] }}">—</div>
                        <div class="lp-status-row" id="srow-{{ $lk['id'] }}">
                            <div class="lp-status-dot" id="sdot-{{ $lk['id'] }}"></div>
                            <span id="stxt-{{ $lk['id'] }}">Menunggu antrian</span>
                        </div>
                    </div>
                    <div class="lp-foot">
                        <div class="lp-foot-stat">Menunggu: <strong id="fw-{{ $lk['id'] }}">—</strong></div>
                        <div class="lp-foot-stat">Selesai: <strong id="fd-{{ $lk['id'] }}">—</strong></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Ticker --}}
        <footer class="dsp-ticker">
            <div class="ticker-label">Info</div>
            <div class="ticker-track">
                <div class="ticker-inner">
                    <span>Selamat datang di RS Sehat Sentosa — Melayani dengan Hati dan Profesional</span>
                    <span>Harap perhatikan layar ini dan siapkan kartu identitas serta berkas yang diperlukan</span>
                    <span>Antrian yang tidak hadir setelah dipanggil 3 kali akan dibatalkan secara otomatis</span>
                    <span>Jam layanan: Senin – Sabtu 07:00 – 21:00 WIB | Minggu 08:00 – 14:00 WIB</span>
                    <span>Hotline: (031) 1234-5678 | WhatsApp: 0812-3456-7890</span>
                </div>
            </div>
        </footer>

    </div>

    <script>
        /* ── Clock ── */
        (function tick() {
            const n = new Date();
            document.getElementById('dClock').textContent = n.toLocaleTimeString('id-ID', {
                hour12: false
            });
            setTimeout(tick, 1000);
        })();

        /* ── TTS Engine ──────────────────────────────────────────────────────────────
           Web Speech API — suara keluar dari speaker Display TV.
           Queue dipakai agar panggilan tidak tumpang tindih.
        */
        const tts = {
            queue: [],
            busy: false,
            enabled: typeof speechSynthesis !== 'undefined',

            speak(text) {
                if (!this.enabled) return;
                this.queue.push(text);
                if (!this.busy) this._next();
            },

            _next() {
                if (!this.queue.length) {
                    this.busy = false;
                    this._hideIndicator();
                    return;
                }
                this.busy = true;
                this._showIndicator();

                const utt = new SpeechSynthesisUtterance(this.queue.shift());
                utt.lang = 'id-ID';
                utt.rate = 0.88;
                utt.pitch = 1.0;
                utt.volume = 1.0;

                utt.onend = utt.onerror = () => {
                    setTimeout(() => this._next(), 600);
                };

                speechSynthesis.speak(utt);
            },

            _showIndicator() {
                document.getElementById('ttsIndicator').classList.add('show');
            },
            _hideIndicator() {
                document.getElementById('ttsIndicator').classList.remove('show');
            },

            /**
             * Digit dibaca satu per satu agar jelas.
             * "B001" → "Nomor antrian B, nol, nol, satu. Silakan menuju Loket satu, BPJS Kesehatan."
             */
            buildText(kode, loketId, loketLabel) {
                const prefix = kode.charAt(0);
                const digits = kode.slice(1).split('').join(', ');
                const loketNum = ['satu', 'dua', 'tiga'][loketId - 1] ?? loketId;
                return `Nomor antrian ${prefix}, ${digits}. Silakan menuju Loket ${loketNum}, ${loketLabel}.`;
            },
        };

        /* ── State ── */
        const prev = {
            1: null,
            2: null,
            3: null
        };

        const STATUS_TEXT = {
            calling: '⬤ Sedang Dilayani',
            paused: 'Ditunda',
        };

        function renderLoket(id, data) {
            const panel = document.getElementById(`panel-${id}`);
            const numEl = document.getElementById(`num-${id}`);
            const kodeEl = document.getElementById(`kode-${id}`);
            const stxt = document.getElementById(`stxt-${id}`);

            document.getElementById(`fw-${id}`).textContent = data.waiting ?? '0';
            document.getElementById(`fd-${id}`).textContent = data.done_today ?? '0';

            if (!data.active) {
                panel.classList.remove(`glow-${id}`, 'flashing');
                numEl.textContent = '—';
                numEl.classList.add('empty');
                kodeEl.textContent = '—';
                kodeEl.classList.add('empty');
                stxt.textContent = 'Menunggu antrian';
                prev[id] = null;
                return;
            }

            const {
                formatted,
                kode_antrian,
                status
            } = data.active;

            numEl.classList.remove('empty');
            kodeEl.classList.remove('empty');
            numEl.textContent = formatted;
            kodeEl.textContent = kode_antrian;
            stxt.textContent = STATUS_TEXT[status] || status;

            panel.classList.toggle(`glow-${id}`, status === 'calling');

            // Suara + flash: nomor baru ATAU panggil ulang (called_at berubah → key beda)
            const callKey = `${kode_antrian}_${data.active.called_at}`;
            if (status === 'calling' && prev[id] !== callKey) {
                prev[id] = callKey;

                panel.classList.add('flashing');
                setTimeout(() => panel.classList.remove('flashing'), 2200);

                const loketLabel = data.loket_info?.label ?? `Loket ${id}`;
                tts.speak(tts.buildText(kode_antrian, id, loketLabel));
            }
        }

        /* ── Poll setiap 2 detik ── */
        async function poll() {
            try {
                const r = await fetch('/display/state', {
                    headers: {
                        Accept: 'application/json'
                    }
                });
                const d = await r.json();

                if (d.server_date) document.getElementById('dDate').textContent = d.server_date;

                for (const [id, ld] of Object.entries(d.lokets)) {
                    renderLoket(parseInt(id), ld);
                }
            } catch {
                /* silent — tampilkan data terakhir */ }
        }

        poll();
        setInterval(poll, 2000);
    </script>
</body>

</html>
