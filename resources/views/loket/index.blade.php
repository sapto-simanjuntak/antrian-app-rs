@extends('layouts.app')

@section('title', "Operator Loket $loketId")
@section('role-icon', 'person-workspace')
@section('role-label', "Operator Loket $loketId — {$loketInfo['short']}")

@push('styles')
    <style>
        /* ── Theme per loket ── */
        :root {
            @if ($loketId == 1)
                --lk: var(--c-primary);
                --lk-soft: var(--c-primary-soft);
                --lk-dark: var(--c-primary-dark);
            @elseif($loketId == 2)
                --lk: var(--c-green);
                --lk-soft: var(--c-green-soft);
                --lk-dark: #009970;
            @else
                --lk: var(--c-amber);
                --lk-soft: var(--c-amber-soft);
                --lk-dark: #c28000;
            @endif
        }

        .loket-wrap {
            max-width: 1280px;
            margin: 0 auto;
        }

        /* Nav pills */
        .loket-nav {
            display: flex;
            gap: .4rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }

        .lnav-pill {
            padding: 6px 16px;
            border-radius: 999px;
            border: 1.5px solid var(--c-border);
            background: var(--c-white);
            color: var(--c-muted);
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .lnav-pill.active {
            background: var(--lk);
            color: #fff;
            border-color: var(--lk);
        }

        .lnav-pill:hover:not(.active) {
            border-color: var(--lk);
            color: var(--lk);
        }

        .lnav-pill.tv-link {
            margin-left: auto;
        }

        /* ── Active queue card ── */
        .active-card {
            background: linear-gradient(135deg, var(--lk) 0%, var(--lk-dark) 100%);
            border-radius: 20px;
            padding: 1.75rem;
            color: #fff;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .18);
            margin-bottom: 1.25rem;
            min-height: 150px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .active-card::before {
            content: attr(data-kode);
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 7rem;
            font-weight: 900;
            opacity: .08;
            letter-spacing: -4px;
            line-height: 1;
            font-family: var(--font-mono);
        }

        .ac-left {
            flex: 1;
        }

        .ac-sublabel {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: 2px;
            opacity: .75;
            text-transform: uppercase;
        }

        .ac-number {
            font-family: var(--font-mono);
            font-size: 4.5rem;
            font-weight: 500;
            line-height: 1;
            letter-spacing: -2px;
        }

        .ac-kode {
            font-size: 1.2rem;
            font-weight: 800;
            opacity: .9;
            letter-spacing: 2px;
        }

        .ac-status-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-top: .6rem;
        }

        .ac-pill {
            background: rgba(255, 255, 255, .2);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .ac-time {
            font-size: .75rem;
            opacity: .7;
        }

        .ac-empty {
            opacity: .7;
        }

        .ac-empty i {
            font-size: 2rem;
            margin-right: .75rem;
        }

        /* pulse dot */
        .pdot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            animation: pdot 1.1s ease infinite;
        }

        @keyframes pdot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1)
            }

            50% {
                opacity: .4;
                transform: scale(1.4)
            }
        }

        /* ── Action buttons ── */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: .7rem;
            margin-bottom: 1.25rem;
        }

        @media(max-width:640px) {
            .action-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .abt {
            padding: 1rem .75rem;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-family: var(--font);
            font-weight: 700;
            font-size: .82rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .3rem;
            transition: transform .15s, filter .15s, box-shadow .15s;
            position: relative;
        }

        .abt i {
            font-size: 1.35rem;
        }

        .abt:hover:not(:disabled) {
            transform: translateY(-2px);
            filter: brightness(1.06);
            box-shadow: 0 6px 18px rgba(0, 0, 0, .15);
        }

        .abt:active:not(:disabled) {
            transform: scale(.95);
        }

        .abt:disabled {
            opacity: .38;
            cursor: not-allowed;
        }

        .abt-panggil {
            background: var(--lk);
            color: #fff;
        }

        .abt-pause {
            background: var(--c-amber);
            color: #fff;
        }

        .abt-ulang {
            background: var(--c-violet);
            color: #fff;
        }

        .abt-selesai {
            background: #0EA5E9;
            color: #fff;
        }

        .abt-batal {
            background: var(--c-red);
            color: #fff;
        }

        /* loading state for buttons */
        .abt.loading::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 14px;
            background: rgba(255, 255, 255, .25);
        }

        /* ── Stats row ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: .7rem;
            margin-bottom: 1.25rem;
        }

        @media(max-width:640px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-box {
            background: var(--c-white);
            border: 1px solid var(--c-border);
            border-radius: 14px;
            padding: 1rem;
            text-align: center;
        }

        .stat-val {
            font-size: 1.9rem;
            font-weight: 900;
            color: var(--lk);
            line-height: 1;
        }

        .stat-lbl {
            font-size: .72rem;
            color: var(--c-muted);
            font-weight: 600;
            margin-top: .3rem;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ── History table ── */
        .htable {
            width: 100%;
            border-collapse: collapse;
            font-size: .85rem;
        }

        .htable th {
            background: var(--c-surface);
            padding: .65rem 1rem;
            text-align: left;
            color: var(--c-muted);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            border-bottom: 1px solid var(--c-border);
        }

        .htable td {
            padding: .65rem 1rem;
            border-bottom: 1px solid var(--c-border);
            vertical-align: middle;
        }

        .htable tr:last-child td {
            border: none;
        }

        .htable tr:hover td {
            background: var(--c-surface);
        }

        .kode-badge {
            font-family: var(--font-mono);
            font-weight: 500;
            font-size: .9rem;
            color: var(--c-text);
            background: var(--c-surface);
            border-radius: 6px;
            padding: 2px 8px;
            border: 1px solid var(--c-border);
        }

        /* ── Right panel header tab ── */
        .rtab {
            display: flex;
            gap: 0;
            border-bottom: 1px solid var(--c-border);
            padding: 0 1rem;
        }

        .rtab-item {
            padding: .75rem 1rem;
            font-size: .83rem;
            font-weight: 700;
            color: var(--c-muted);
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .rtab-item.active {
            color: var(--lk);
            border-bottom-color: var(--lk);
        }

        .rtab-content {
            display: none;
        }

        .rtab-content.active {
            display: block;
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--c-muted);
        }

        .empty-state i {
            font-size: 2.5rem;
            opacity: .3;
            display: block;
            margin-bottom: .5rem;
        }
    </style>
@endpush

@section('content')
    <div class="loket-wrap">

        {{-- Loket Nav --}}
        <div class="loket-nav">
            <a href="/loket/1" class="lnav-pill {{ $loketId == 1 ? 'active' : '' }}">
                <i class="bi bi-shield-check-fill"></i> Loket 1 — BPJS
            </a>
            <a href="/loket/2" class="lnav-pill {{ $loketId == 2 ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Loket 2 — Umum
            </a>
            <a href="/loket/3" class="lnav-pill {{ $loketId == 3 ? 'active' : '' }}">
                <i class="bi bi-person-hearts"></i> Loket 3 — Lansia
            </a>
            <a href="/display" class="lnav-pill tv-link">
                <i class="bi bi-tv-fill"></i> Display TV
            </a>
        </div>

        <div class="row g-3">

            {{-- Left: active + actions + stats ── --}}
            <div class="col-lg-7">

                {{-- Active card --}}
                <div class="active-card" id="activeCard" data-kode="">
                    <div class="ac-left">
                        <div id="activeContent">
                            <div class="ac-empty d-flex align-items-center">
                                <i class="bi bi-inbox"></i>
                                <div>
                                    <div style="font-weight:800; font-size:1rem;">Belum ada antrian aktif</div>
                                    <div style="font-size:.8rem; opacity:.7;">Tekan "Panggil" untuk memanggil antrian
                                        berikutnya</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-grid">
                    <button class="abt abt-panggil" id="btn-panggil" onclick="doAction('panggil')">
                        <i class="bi bi-megaphone-fill"></i>Panggil
                    </button>
                    <button class="abt abt-ulang" id="btn-ulang" onclick="doAction('panggil-ulang')" disabled>
                        <i class="bi bi-arrow-clockwise"></i>Panggil Ulang
                    </button>
                    <button class="abt abt-pause" id="btn-pause" onclick="doAction('pause')" disabled>
                        <i class="bi bi-pause-circle-fill"></i>Pause
                    </button>
                    <button class="abt abt-selesai" id="btn-selesai" onclick="doAction('selesai')" disabled>
                        <i class="bi bi-check-circle-fill"></i>Selesai
                    </button>
                    <button class="abt abt-batal" id="btn-batal" onclick="doAction('batal')" disabled>
                        <i class="bi bi-x-circle-fill"></i>Batal
                    </button>
                </div>

                {{-- Stats --}}
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-val" id="s-waiting">—</div>
                        <div class="stat-lbl">Menunggu</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-val" id="s-done">—</div>
                        <div class="stat-lbl">Selesai</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-val" id="s-cancelled">—</div>
                        <div class="stat-lbl">Dibatalkan</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-val" id="s-avg">—</div>
                        <div class="stat-lbl">Rata-rata</div>
                    </div>
                </div>
            </div>

            {{-- Right: history ── --}}
            <div class="col-lg-5">
                <div class="qcard h-100" style="display:flex; flex-direction:column;">

                    <div class="rtab">
                        <div class="rtab-item active" onclick="switchTab('history')" id="tab-history">
                            <i class="bi bi-clock-history"></i> Riwayat
                        </div>
                        <div class="rtab-item" onclick="switchTab('waiting')" id="tab-waiting">
                            <i class="bi bi-hourglass-split"></i> Antrian Menunggu
                        </div>
                    </div>

                    <div style="overflow-y:auto; flex:1; max-height:520px;">
                        {{-- History tab --}}
                        <div id="pane-history" class="rtab-content active">
                            <table class="htable">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Status</th>
                                        <th>Durasi</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody id="historyBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <div class="spin" style="margin:0 auto; border-top-color:var(--lk);"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Waiting tab --}}
                        <div id="pane-waiting" class="rtab-content">
                            <table class="htable">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nomor</th>
                                        <th>Waktu Daftar</th>
                                    </tr>
                                </thead>
                                <tbody id="waitingBody">
                                    <tr>
                                        <td colspan="3" class="empty-state">
                                            <i class="bi bi-inbox"></i>Tidak ada antrian menunggu
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const LOKET = {{ $loketId }};
        const BASE = `/loket/${LOKET}`;
        let busy = false;
        let lastUpdate = 0;

        // ── Render active queue ───────────────────────────────────────────────────────
        function renderActive(a) {
            const card = document.getElementById('activeCard');
            const content = document.getElementById('activeContent');
            card.dataset.kode = a ? a.kode_antrian : '';

            if (!a) {
                content.innerHTML = `
            <div class="ac-empty d-flex align-items-center">
                <i class="bi bi-inbox"></i>
                <div>
                    <div style="font-weight:800;font-size:1rem;">Belum ada antrian aktif</div>
                    <div style="font-size:.8rem;opacity:.7;">Tekan "Panggil" untuk memanggil antrian berikutnya</div>
                </div>
            </div>`;
                return;
            }

            const sMap = {
                calling: ['Sedang Dilayani', 'person-check-fill', true],
                paused: ['Ditunda', 'pause-circle-fill', false],
            };
            const [stxt, sicon, showPulse] = sMap[a.status] || ['—', 'circle', false];
            const pulseHtml = showPulse ? '<div class="pdot"></div>' : '';

            content.innerHTML = `
        <div class="ac-sublabel">Antrian Aktif</div>
        <div class="ac-number">${a.formatted}</div>
        <div class="ac-kode">${a.kode_antrian}</div>
        <div class="ac-status-row">
            <div class="ac-pill">${pulseHtml}<i class="bi bi-${sicon}"></i> ${stxt}</div>
            <span class="ac-time">Dipanggil: ${a.called_at || '—'}</span>
        </div>`;
        }

        // ── Update button states ──────────────────────────────────────────────────────
        function syncButtons(a) {
            const isActive = ['calling', 'paused'].includes(a?.status);
            const isPaused = a?.status === 'paused';

            // Panggil: hanya aktif jika tidak ada antrian aktif sama sekali
            setBtn('panggil', !isActive);
            // Panggil Ulang: aktif kalau ada antrian aktif (calling/paused)
            setBtn('ulang', isActive);
            // Pause: aktif kalau status calling (sedang dilayani)
            setBtn('pause', a?.status === 'calling');
            // Selesai & Batal: aktif kalau ada antrian aktif
            setBtn('selesai', isActive);
            setBtn('batal', isActive);
        }

        function setBtn(id, enabled) {
            document.getElementById(`btn-${id}`).disabled = !enabled;
        }

        // ── Render stats ──────────────────────────────────────────────────────────────
        function renderStats(s, w) {
            document.getElementById('s-waiting').textContent = w ?? '0';
            document.getElementById('s-done').textContent = s?.total_done ?? '0';
            document.getElementById('s-cancelled').textContent = s?.total_cancelled ?? '0';
            document.getElementById('s-avg').textContent = s?.avg_duration_human ?? '-';
        }

        // ── Render history ────────────────────────────────────────────────────────────
        function renderHistory(rows) {
            const tb = document.getElementById('historyBody');
            if (!rows?.length) {
                tb.innerHTML =
                    `<tr><td colspan="4"><div class="empty-state"><i class="bi bi-inbox"></i>Belum ada riwayat hari ini</div></td></tr>`;
                return;
            }
            tb.innerHTML = rows.map(r => `
        <tr>
            <td><span class="kode-badge">${r.kode_antrian}</span></td>
            <td><span class="qbadge qbadge-${r.status}">${r.status_label}</span></td>
            <td style="color:var(--c-muted)">${r.duration_human}</td>
            <td style="color:var(--c-muted);font-size:.78rem">${r.done_at || '—'}</td>
        </tr>`).join('');
        }

        // ── Render waiting list ───────────────────────────────────────────────────────
        function renderWaiting(waiting, waitingList) {
            // Update badge di tab — hanya tampil kalau ada antrian
            const tab = document.getElementById('tab-waiting');
            tab.innerHTML = waiting > 0 ?
                `<i class="bi bi-hourglass-split"></i> Menunggu <span style="background:var(--lk);color:#fff;border-radius:999px;padding:1px 8px;font-size:.72rem;margin-left:.3rem">${waiting}</span>` :
                `<i class="bi bi-hourglass-split"></i> Antrian Menunggu`;

            // Render tabel
            const tb = document.getElementById('waitingBody');
            if (!waitingList?.length) {
                tb.innerHTML =
                    `<tr><td colspan="3"><div class="empty-state"><i class="bi bi-inbox"></i>Tidak ada antrian menunggu</div></td></tr>`;
                return;
            }
            tb.innerHTML = waitingList.map((r, i) => `
        <tr>
            <td><span class="kode-badge">${r.kode_antrian}</span></td>
            <td style="color:var(--c-muted);font-size:.78rem;text-align:center">${i + 1}</td>
            <td style="color:var(--c-muted);font-size:.78rem">${r.created_at || '—'}</td>
        </tr>`).join('');
        }

        // ── Fetch state ───────────────────────────────────────────────────────────────
        async function fetchState(showBusy = false) {
            try {
                const {
                    ok,
                    data
                } = await api(`${BASE}/state`);
                if (!ok) return;
                renderActive(data.active);
                syncButtons(data.active);
                renderStats(data.stats, data.waiting);
                renderHistory(data.history);
                renderWaiting(data.waiting, data.waiting_list);
                lastUpdate = Date.now();
            } catch {}
        }

        // ── Do action ─────────────────────────────────────────────────────────────────
        async function doAction(action) {
            if (busy) return;
            busy = true;
            document.querySelectorAll('.abt').forEach(b => b.disabled = true);
            try {
                const {
                    ok,
                    data
                } = await api(`${BASE}/${action}`, 'POST');
                toast(data.message || 'Berhasil', data.success ? 'success' : 'error');
                await fetchState();
            } catch {
                toast('Kesalahan koneksi.', 'error');
            } finally {
                busy = false;
            }
        }

        // ── Tab switch ────────────────────────────────────────────────────────────────
        function switchTab(name) {
            ['history', 'waiting'].forEach(t => {
                document.getElementById(`tab-${t}`).classList.toggle('active', t === name);
                document.getElementById(`pane-${t}`).classList.toggle('active', t === name);
            });
        }

        // ── Start polling ─────────────────────────────────────────────────────────────
        fetchState();
        setInterval(fetchState, 3000);
    </script>
@endpush
