@extends('public/portal/layout')

@section('content')
    <div class="portal-card">
        <h3>Riwayat Resep Obat</h3>
        @if ($reseps->isEmpty())
            <p class="portal-muted">Belum ada riwayat resep.</p>
        @endif
    </div>

    @foreach ($reseps as $daftarpasienId => $items)
        <div class="portal-card">
            <div class="portal-row">
                <strong>{{ \Carbon\Carbon::parse($items->first()->tglregistrasi)->translatedFormat('d F Y') }}</strong>
            </div>
            @foreach ($items as $item)
                <div class="portal-row">
                    <span>{{ $item->type === 'racik' ? ($item->nama_racikan ?? 'Racikan') : $item->obat_name }}</span>
                    <span class="portal-muted">{{ $item->qty }} {{ $item->unit ?? '' }}</span>
                </div>
                @if ($item->signa)
                    <div class="portal-row">
                        <span class="portal-muted" style="font-size:11px">{{ $item->signa }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
@endsection
