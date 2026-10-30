@extends('public/portal/layout')

@section('style')
    <style>
        .pt-field {
            margin-bottom: 14px;
        }

        .pt-field label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ink);
            display: block;
            margin-bottom: 5px;
        }

        select,
        input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: var(--paper);
            color: var(--ink);
            appearance: none;
            -webkit-appearance: none;
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none' stroke='%236B6F76' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 8l5 5 5-5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 34px;
        }

        select:disabled {
            opacity: .55;
        }

        select:focus,
        input[type="date"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
            background-color: var(--paper-raised);
        }

        #bkSlots {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .slot-btn {
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--primary);
            background: var(--paper-raised);
            color: var(--primary);
            font-size: 13px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
        }

        .slot-btn.selected {
            background: var(--primary);
            color: #fff;
        }

        .slot-btn.disabled {
            border-color: var(--line);
            color: var(--ink-soft);
            background: var(--paper);
            cursor: not-allowed;
        }

        #portalMsg {
            font-size: 13px;
            padding: 8px 0;
            font-weight: 500;
        }

        .pt-booking-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 11px 0;
            border-bottom: 1px solid var(--line);
        }

        .pt-booking-item:last-child {
            border-bottom: none;
        }

        .pt-booking-item .date {
            font-weight: 600;
            font-size: 13.5px;
        }
    </style>
@endsection

@section('content')
    <div class="portal-card">
        <h3>Booking Jadwal Dokter</h3>

        <div class="pt-field">
            <label for="bkDokter">Dokter</label>
            <select id="bkDokter">
                <option value="">- Pilih Dokter -</option>
            </select>
        </div>

        <div class="pt-field">
            <label for="bkRuangPraktek">Ruang Praktek</label>
            <select id="bkRuangPraktek" disabled>
                <option value="">- Pilih dokter dulu -</option>
            </select>
        </div>

        <div class="pt-field">
            <label for="bkTanggal">Tanggal</label>
            <input type="date" id="bkTanggal" min="{{ now()->toDateString() }}">
        </div>

        <div class="pt-field" style="margin-bottom:6px">
            <label>Jam Tersedia</label>
            <div id="bkSlots" class="portal-muted">Pilih dokter, ruang praktek, dan tanggal dulu.</div>
        </div>

        <div id="portalMsg"></div>

        <button type="button" class="portal-btn" id="bkConfirm" style="display:none;width:100%">Booking
            Sekarang</button>
    </div>

    <div class="pt-section-head">
        <h2>Booking Saya</h2>
    </div>

    <div class="portal-card">
        @if ($appointments->isEmpty())
            <p class="portal-muted" style="margin:0">Belum ada booking.</p>
        @endif
        @foreach ($appointments as $a)
            <div class="pt-booking-item">
                <div>
                    <div class="date">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('d F Y') }}
                        {{ substr($a->jam_mulai, 0, 5) }}</div>
                    <div class="portal-muted">{{ $a->dokter->employee->fullname ?? '-' }} —
                        {{ $a->ruangPraktek->name ?? '-' }}</div>
                </div>
                <div>
                    @if ($a->status === 'booked')
                        <button type="button" class="portal-btn outline" onclick="cancelBooking({{ $a->id }})">Batalkan</button>
                    @else
                        <span class="portal-badge {{ $a->status === 'cancelled' ? 'belum' : 'lunas' }}">{{ $a->status }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('script')
    <script>
        let selectedSlot = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';

        function showMsg(text, isError) {
            const el = document.getElementById('portalMsg');
            el.style.color = isError ? '#AE3F3C' : '#4C7A67';
            el.textContent = text;
        }

        async function loadDokterOptions() {
            const res = await fetch("{{ url('portal/booking/getDokterList') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const json = await res.json();
            const sel = document.getElementById('bkDokter');
            json.data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id;
                opt.textContent = d.fullname;
                sel.appendChild(opt);
            });
        }

        async function onDokterChange() {
            const dokterId = document.getElementById('bkDokter').value;
            const ruangSel = document.getElementById('bkRuangPraktek');
            ruangSel.innerHTML = '<option value="">- Pilih Ruang Praktek -</option>';
            ruangSel.disabled = true;
            resetSlots();
            if (!dokterId) return;

            const res = await fetch("{{ url('portal/booking/getRuangPraktekByDokter') }}/" + dokterId);
            const json = await res.json();
            if (json.data.length === 0) {
                ruangSel.innerHTML = '<option value="">Dokter ini belum punya ruang praktek</option>';
                return;
            }
            json.data.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name;
                ruangSel.appendChild(opt);
            });
            ruangSel.disabled = false;
        }

        function resetSlots() {
            selectedSlot = null;
            document.getElementById('bkSlots').innerHTML = 'Pilih dokter, ruang praktek, dan tanggal dulu.';
            document.getElementById('bkConfirm').style.display = 'none';
        }

        async function loadSlots() {
            const dokterId = document.getElementById('bkDokter').value;
            const ruangId = document.getElementById('bkRuangPraktek').value;
            const tanggal = document.getElementById('bkTanggal').value;
            selectedSlot = null;
            document.getElementById('bkConfirm').style.display = 'none';
            if (!dokterId || !ruangId || !tanggal) return;

            const params = new URLSearchParams({
                fk_dokter: dokterId,
                fk_ruangpraktek: ruangId,
                tanggal: tanggal
            });
            const res = await fetch("{{ url('portal/booking/getAvailableSlots') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params
            });
            const json = await res.json();
            const container = document.getElementById('bkSlots');
            if (json.data.length === 0) {
                container.innerHTML = 'Dokter tidak praktek pada tanggal ini.';
                return;
            }
            container.innerHTML = '';
            json.data.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = slot.jam_mulai;
                btn.className = 'slot-btn' + (slot.available ? '' : ' disabled');
                if (slot.available) {
                    btn.onclick = function() {
                        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                        btn.classList.add('selected');
                        selectedSlot = slot.jam_mulai;
                        document.getElementById('bkConfirm').style.display = 'inline-block';
                    };
                } else {
                    btn.disabled = true;
                }
                container.appendChild(btn);
            });
        }

        document.getElementById('bkDokter').addEventListener('change', onDokterChange);
        document.getElementById('bkRuangPraktek').addEventListener('change', loadSlots);
        document.getElementById('bkTanggal').addEventListener('change', loadSlots);

        document.getElementById('bkConfirm').addEventListener('click', async function() {
            if (!selectedSlot) return;
            const params = new URLSearchParams({
                fk_dokter: document.getElementById('bkDokter').value,
                fk_ruangpraktek: document.getElementById('bkRuangPraktek').value,
                tanggal: document.getElementById('bkTanggal').value,
                jam_mulai: selectedSlot
            });
            const res = await fetch("{{ url('portal/booking/store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params
            });
            const json = await res.json();
            if (res.ok) {
                showMsg('Booking berhasil! Halaman akan dimuat ulang...', false);
                setTimeout(() => location.reload(), 1200);
            } else {
                showMsg(json.error ?? 'Gagal booking', true);
            }
        });

        async function cancelBooking(id) {
            if (!confirm('Batalkan booking ini?')) return;
            const res = await fetch("{{ url('portal/booking/cancel') }}/" + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (res.ok) {
                location.reload();
            } else {
                const json = await res.json();
                showMsg(json.error ?? 'Gagal membatalkan', true);
            }
        }

        loadDokterOptions();
    </script>
@endsection
