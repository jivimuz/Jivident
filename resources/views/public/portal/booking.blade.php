@extends('public/portal/layout')

@section('style')
    <style>
        select,
        input[type="date"] {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .slot-btn {
            display: inline-block;
            padding: 7px 12px;
            margin: 3px;
            border-radius: 6px;
            border: 1px solid #3a57e8;
            background: #fff;
            color: #3a57e8;
            font-size: 13px;
            cursor: pointer;
        }

        .slot-btn.selected {
            background: #3a57e8;
            color: #fff;
        }

        .slot-btn.disabled {
            border-color: #d1d5db;
            color: #9ca3af;
            cursor: not-allowed;
        }

        #portalMsg {
            font-size: 13px;
            padding: 8px 0;
        }
    </style>
@endsection

@section('content')
    <div class="portal-card">
        <h3>Booking Jadwal Dokter</h3>
        <label>Dokter</label>
        <select id="bkDokter">
            <option value="">- Pilih Dokter -</option>
        </select>

        <label>Ruang Praktek</label>
        <select id="bkRuangPraktek" disabled>
            <option value="">- Pilih dokter dulu -</option>
        </select>

        <label>Tanggal</label>
        <input type="date" id="bkTanggal" min="{{ now()->toDateString() }}">

        <label>Jam Tersedia</label>
        <div id="bkSlots" class="portal-muted">Pilih dokter, ruang praktek, dan tanggal dulu.</div>

        <div id="portalMsg"></div>

        <button type="button" class="portal-btn" id="bkConfirm" style="display:none;margin-top:12px">Booking
            Sekarang</button>
    </div>

    <div class="portal-card">
        <h3>Booking Saya</h3>
        @if ($appointments->isEmpty())
            <p class="portal-muted">Belum ada booking.</p>
        @endif
        @foreach ($appointments as $a)
            <div class="portal-row">
                <div>
                    <div>{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('d F Y') }}
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
            el.style.color = isError ? '#dc2626' : '#16a34a';
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
