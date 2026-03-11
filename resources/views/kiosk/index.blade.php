@extends('layouts.app')

@section('title', 'Ambil Nomor Antrian')
@section('role-icon', 'ticket-perforated')
@section('role-label', 'Terminal Pasien')

@push('styles')
    <style>
        body {
            background: linear-gradient(150deg, #EEF4FF 0%, #F0FFF9 60%, #FFF8E1 100%);
        }

        .kiosk-wrap {
            max-width: 860px;
            margin: 0 auto;
        }

        /* ── Hospital header ── */
        .rs-card {
            background: var(--c-white);
            border-radius: 20px;
            border: 1px solid var(--c-border);
            box-shadow: var(--shadow-md);
            padding: 2rem 2.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        @media(max-width:600px) {
            .rs-card {
                flex-direction: column;
                text-align: center;
            }
        }

        .rs-emblem {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-dark));
            border-radius: 22px;
            display: grid;
            place-items: center;
            font-size: 2.2rem;
            color: #fff;
            box-shadow: 0 8px 24px rgba(0, 63, 187, .25);
        }

        .rs-info h1 {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--c-text);
            margin: 0;
        }

        .rs-info p {
            color: var(--c-muted);
            font-size: .88rem;
            margin: .3rem 0 0;
        }

        .rs-info .rs-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .8rem;
        }

        .rs-chip {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 999px;
            padding: 4px 12px;
            font-size: .78rem;
            color: var(--c-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        /* ── Section title ── */
        .section-title {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .section-title h2 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--c-text);
        }

        .section-title p {
            color: var(--c-muted);
            font-size: .88rem;
        }

        /* ── Loket grid ── */
        .loket-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        @media(max-width:640px) {
            .loket-grid {
                grid-template-columns: 1fr;
            }
        }

        .lk-btn {
            background: var(--c-white);
            border: 2px solid var(--c-border);
            border-radius: 18px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            cursor: pointer;
            transition: transform .18s, box-shadow .18s, border-color .18s;
            position: relative;
            overflow: hidden;
            user-select: none;
        }

        .lk-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 18px 18px 0 0;
        }

        .lk-1::after {
            background: var(--c-primary);
        }

        .lk-2::after {
            background: var(--c-green);
        }

        .lk-3::after {
            background: var(--c-amber);
        }

        .lk-btn:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .lk-btn:hover.lk-1 {
            border-color: var(--c-primary);
        }

        .lk-btn:hover.lk-2 {
            border-color: var(--c-green);
        }

        .lk-btn:hover.lk-3 {
            border-color: var(--c-amber);
        }

        .lk-btn:active {
            transform: translateY(-2px) scale(.98);
        }

        .lk-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 1.7rem;
            margin: 0 auto .9rem;
        }

        .lk-1 .lk-icon {
            background: var(--c-primary-soft);
            color: var(--c-primary);
        }

        .lk-2 .lk-icon {
            background: var(--c-green-soft);
            color: var(--c-green);
        }

        .lk-3 .lk-icon {
            background: var(--c-amber-soft);
            color: var(--c-amber);
        }

        .lk-num {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--c-muted);
            margin-bottom: .2rem;
        }

        .lk-title {
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: .25rem;
        }

        .lk-desc {
            font-size: .8rem;
            color: var(--c-muted);
            margin-bottom: 1rem;
        }

        .lk-wait {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
        }

        .lk-1 .lk-wait {
            background: var(--c-primary-soft);
            color: var(--c-primary);
        }

        .lk-2 .lk-wait {
            background: var(--c-green-soft);
            color: var(--c-green);
        }

        .lk-3 .lk-wait {
            background: var(--c-amber-soft);
            color: #8a6200;
        }

        /* ── Quick links ── */
        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .ql-btn {
            padding: 6px 16px;
            border-radius: 999px;
            border: 1.5px solid var(--c-border);
            background: var(--c-white);
            color: var(--c-muted);
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .ql-btn:hover {
            border-color: var(--c-primary);
            color: var(--c-primary);
            background: var(--c-primary-soft);
        }

        /* ── Loading overlay ── */
        #loadingOverlay {
            position: fixed;
            inset: 0;
            z-index: 9990;
            background: rgba(240, 244, 255, .75);
            backdrop-filter: blur(4px);
            display: none;
            place-items: center;
        }

        #loadingOverlay.show {
            display: grid;
        }

        .loader-box {
            background: var(--c-white);
            border-radius: 16px;
            padding: 2rem 2.5rem;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }

        .loader-ring {
            width: 52px;
            height: 52px;
            margin: 0 auto 1rem;
            border: 4px solid var(--c-border);
            border-top-color: var(--c-primary);
            border-radius: 50%;
            animation: spinning .75s linear infinite;
        }

        /* ── Receipt modal ── */
        #receiptOverlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15, 24, 40, .6);
            backdrop-filter: blur(6px);
            display: none;
            place-items: center;
            padding: 1rem;
        }

        #receiptOverlay.show {
            display: grid;
        }

        .receipt {
            background: var(--c-white);
            width: 100%;
            max-width: 360px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .3);
            animation: popUp .32s cubic-bezier(.34, 1.56, .64, 1);
        }

        @keyframes popUp {
            from {
                transform: scale(.72) translateY(30px);
                opacity: 0;
            }

            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        .receipt-head {
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-dark));
            color: #fff;
            text-align: center;
            padding: 1.75rem 1.5rem 1.25rem;
        }

        .receipt-head .rh-icon {
            font-size: 2.2rem;
            margin-bottom: .5rem;
        }

        .receipt-head h2 {
            font-size: 1.1rem;
            font-weight: 900;
            margin: 0 0 .2rem;
            letter-spacing: .5px;
        }

        .receipt-head p {
            font-size: .75rem;
            opacity: .8;
            margin: 0;
        }

        /* Dashed divider */
        .receipt-dash {
            height: 1px;
            background: repeating-linear-gradient(90deg, var(--c-border) 0, var(--c-border) 8px, transparent 8px, transparent 14px);
            margin: 0;
        }

        .receipt-body {
            padding: 1.5rem;
            text-align: center;
        }

        .rc-loket-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--c-primary-soft);
            color: var(--c-primary);
            font-weight: 700;
            font-size: .8rem;
            padding: 5px 14px;
            border-radius: 999px;
            margin-bottom: 1rem;
        }

        .rc-number {
            font-family: var(--font-mono);
            font-size: 5.5rem;
            font-weight: 500;
            color: var(--c-text);
            line-height: 1;
            letter-spacing: -3px;
            margin-bottom: .3rem;
        }

        .rc-kode {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--c-primary);
            letter-spacing: 3px;
            margin-bottom: 1rem;
        }

        .rc-info {
            background: var(--c-surface);
            border-radius: 10px;
            padding: .9rem;
            margin-bottom: 1rem;
        }

        .rc-info-row {
            display: flex;
            justify-content: space-between;
            font-size: .82rem;
            padding: .2rem 0;
        }

        .rc-info-row .label {
            color: var(--c-muted);
        }

        .rc-info-row .value {
            font-weight: 700;
        }

        .rc-note {
            font-size: .78rem;
            color: var(--c-muted);
            line-height: 1.5;
        }

        .receipt-foot {
            padding: 0 1.5rem 1.5rem;
        }

        .receipt-foot .rc-time {
            text-align: center;
            font-size: .72rem;
            color: var(--c-muted);
            margin-bottom: .9rem;
        }

        .btn-receipt-close {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-dark));
            color: #fff;
            font-family: var(--font);
            font-weight: 700;
            font-size: .95rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: filter .15s;
        }

        .btn-receipt-close:hover {
            filter: brightness(1.08);
        }
    </style>
@endpush

@section('content')
    <div class="kiosk-wrap">

        {{-- Hospital Info --}}
        <div class="rs-card">
            <div class="rs-emblem"><i class="bi bi-hospital-fill"></i></div>
            <div class="rs-info">
                <h1>Rumah Sakit Sehat </h1>
                <p>Melayani dengan sepenuh hati untuk kesehatan Anda</p>
                <div class="rs-meta">
                    <span class="rs-chip"><i class="bi bi-geo-alt-fill"></i> Jl. Kesehatan No. 1, Surabaya, Jawa Timur
                        60001</span>
                    <span class="rs-chip"><i class="bi bi-telephone-fill"></i> (031) 1234-5678</span>
                    <span class="rs-chip"><i class="bi bi-clock-fill"></i> 07:00 – 21:00 WIB</span>
                </div>
            </div>
        </div>

        {{-- Loket Selection --}}
        <div class="section-title">
            <h2><i class="bi bi-ticket-perforated"></i> Pilih Loket Pendaftaran</h2>
            <p>Tekan tombol sesuai dengan jenis layanan yang Anda butuhkan</p>
        </div>

        <div class="loket-grid">
            <div class="lk-btn lk-1" onclick="ambilAntrian(1)">
                <div class="lk-icon"><i class="bi bi-shield-check-fill"></i></div>
                <div class="lk-num">Loket 1</div>
                <div class="lk-title">BPJS Kesehatan</div>
                <div class="lk-desc">Peserta BPJS aktif & Jamkesda</div>
                <div class="lk-wait" id="wait-chip-1">
                    <i class="bi bi-people-fill"></i>
                    <span id="wait-count-1">{{ $stats[1]['waiting'] }}</span> menunggu
                </div>
            </div>

            <div class="lk-btn lk-2" onclick="ambilAntrian(2)">
                <div class="lk-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div class="lk-num">Loket 2</div>
                <div class="lk-title">Pasien Umum</div>
                <div class="lk-desc">Pasien umum & asuransi swasta</div>
                <div class="lk-wait" id="wait-chip-2">
                    <i class="bi bi-people-fill"></i>
                    <span id="wait-count-2">{{ $stats[2]['waiting'] }}</span> menunggu
                </div>
            </div>

            <div class="lk-btn lk-3" onclick="ambilAntrian(3)">
                <div class="lk-icon"><i class="bi bi-person-hearts"></i></div>
                <div class="lk-num">Loket 3</div>
                <div class="lk-title">Pasien Lansia</div>
                <div class="lk-desc">Prioritas usia 60 tahun ke atas</div>
                <div class="lk-wait" id="wait-chip-3">
                    <i class="bi bi-people-fill"></i>
                    <span id="wait-count-3">{{ $stats[3]['waiting'] }}</span> menunggu
                </div>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="quick-links">
            <a href="/display" class="ql-btn"><i class="bi bi-tv-fill"></i> Display TV</a>
            <a href="/loket/1" class="ql-btn"><i class="bi bi-person-workspace"></i> Operator Loket 1</a>
            <a href="/loket/2" class="ql-btn"><i class="bi bi-person-workspace"></i> Operator Loket 2</a>
            <a href="/loket/3" class="ql-btn"><i class="bi bi-person-workspace"></i> Operator Loket 3</a>
        </div>

    </div>

    {{-- Loading overlay --}}
    <div id="loadingOverlay">
        <div class="loader-box">
            <div class="loader-ring"></div>
            <div style="font-weight:700; color:var(--c-text);">Memproses...</div>
            <div style="font-size:.8rem; color:var(--c-muted); margin-top:.3rem;">Harap tunggu sebentar</div>
        </div>
    </div>

    {{-- Receipt overlay --}}
    <div id="receiptOverlay" onclick="closeReceipt(event)">
        <div class="receipt" id="receiptBox">
            <div class="receipt-head">
                <div class="rh-icon"><i class="bi bi-hospital-fill"></i></div>
                <h2>RUMAH SAKIT SEHAT</h2>
                <p>Jl. Kesehatan No. 1, Surabaya, Jawa Timur 60001</p>
                <p>Telp: (031) 1234-5678</p>
            </div>

            <div class="receipt-dash"></div>

            <div class="receipt-body">
                <div class="rc-loket-chip" id="rcLoketChip">
                    <i class="bi bi-shield-check-fill"></i> Loket 1 — BPJS
                </div>
                <div
                    style="font-size:.72rem; color:var(--c-muted); letter-spacing:2px; text-transform:uppercase; margin-bottom:.3rem;">
                    Nomor Antrian Anda</div>
                <div class="rc-number" id="rcNumber">001</div>
                <div class="rc-kode" id="rcKode">B001</div>

                <div class="rc-info">
                    <div class="rc-info-row">
                        <span class="label">Antrian sebelum Anda</span>
                        <span class="value" id="rcWaiting">—</span>
                    </div>
                    <div class="rc-info-row">
                        <span class="label">Loket</span>
                        <span class="value" id="rcLoketName">—</span>
                    </div>
                </div>

                <p class="rc-note">
                    Harap perhatikan layar display antrian.<br>
                    Jika dipanggil 3× tanpa hadir, antrian akan dibatalkan.
                </p>
            </div>

            <div class="receipt-dash"></div>

            <div class="receipt-foot">
                <div class="rc-time" id="rcTime">—</div>
                <button class="btn-receipt-close" onclick="closeReceipt()">
                    <i class="bi bi-check-lg"></i> Terima Kasih, Kembali ke Menu
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function ambilAntrian(loketId) {
            document.getElementById('loadingOverlay').classList.add('show');
            try {
                const {
                    ok,
                    data
                } = await api('/kiosk/ambil', 'POST', {
                    loket_id: loketId
                });
                if (ok && data.success) {
                    showReceipt(data.data);
                    // update waiting count
                    const newWait = parseInt(document.getElementById(`wait-count-${loketId}`).textContent) + 1;
                    document.getElementById(`wait-count-${loketId}`).textContent = newWait;
                } else {
                    toast(data.message || 'Gagal mengambil antrian.', 'error');
                }
            } catch (e) {
                toast('Kesalahan koneksi. Coba lagi.', 'error');
            } finally {
                document.getElementById('loadingOverlay').classList.remove('show');
            }
        }

        function showReceipt(d) {
            const icons = {
                1: 'shield-check-fill',
                2: 'person-badge-fill',
                3: 'person-hearts'
            };
            document.getElementById('rcLoketChip').innerHTML =
                `<i class="bi bi-${icons[d.loket_id]}"></i> Loket ${d.loket_id} — ${d.loket_info.short}`;
            document.getElementById('rcNumber').textContent = d.formatted;
            document.getElementById('rcKode').textContent = d.kode_antrian;
            document.getElementById('rcWaiting').textContent = Math.max(0, (d.waiting_count ?? 1) - 1) + ' orang';
            document.getElementById('rcLoketName').textContent = d.loket_info.label;
            document.getElementById('rcTime').textContent = 'Dicetak: ' + d.created_at;
            document.getElementById('receiptOverlay').classList.add('show');
        }

        function closeReceipt(e) {
            if (!e || e.target === document.getElementById('receiptOverlay')) {
                document.getElementById('receiptOverlay').classList.remove('show');
            }
        }
    </script>
@endpush
