@extends('layout/main')

@section('style')
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .license-info-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px 0 rgba(17, 38, 146, 0.05);
            font-family: 'Inter', sans-serif;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-active {
            background-color: rgba(60, 179, 113, 0.1);
            color: #2e7d32;
        }

        .status-expired {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .license-key-box {
            background: #f8f9fa;
            border: 2px dashed #ced4da;
            padding: 15px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 1.3rem;
            letter-spacing: 2px;
            color: #333;
            text-transform: uppercase;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center shadow-sm">
                    <a href="javascript:history.back()"
                        class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a>
                    <span class="fw-bold text-center flex-grow-1 fs-5 fs-md-4 text-primary">
                        Informasi Lisensi Aplikasi
                    </span>
                    <div style="width:45px;" class="flex-shrink-0"></div>
                </div>
            </div>
        </div>

        <!-- Detail Card -->
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card license-info-card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".2" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-body p-4 p-md-5">

                        @php
                            // Mengecek status aktif lisensi berdasarkan tanggal hari ini
                            $isActive = false;
                            $expiredDate = '-';

                            if (isset($serial) && $serial->valid_until) {
                                $isActive = \Carbon\Carbon::parse($serial->valid_until)->endOfDay()->isFuture();
                                $expiredDate = \Carbon\Carbon::parse($serial->valid_until)->translatedFormat('d F Y');
                            }
                        @endphp

                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <h5 class="fw-bold mb-0">Status Lisensi</h5>
                            @if ($isActive)
                                <span class="status-badge status-active">
                                    <i class="iconoir-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="status-badge status-expired">
                                    <i class="iconoir-warning-triangle"></i> Tidak Aktif / Kedaluwarsa
                                </span>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="text-muted mb-2 fw-semibold" style="font-size: 14px;">License Key
                                Terdaftar</label>
                            <div class="license-key-box text-center">
                                @if (isset($serial) && $serial->serial_code)
                                    {{ $serial->serial_code }}
                                @else
                                    <span class="text-muted" style="letter-spacing: normal;">BELUM ADA LISENSI</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4 bg-light p-3 rounded mx-0">
                            <div class="col-6">
                                <label class="text-muted mb-1 fw-semibold" style="font-size: 13px;">Diperbarui Pada</label>
                                <p class="fw-bold mb-0 text-dark">
                                    {{ isset($serial) && $serial->updated_at ? \Carbon\Carbon::parse($serial->updated_at)->translatedFormat('d M Y, H:i') : '-' }}
                                </p>
                            </div>
                            <div class="col-6 text-end">
                                <label class="text-muted mb-1 fw-semibold" style="font-size: 13px;">Berlaku Hingga</label>
                                <p class="fw-bold mb-0 {{ !$isActive ? 'text-danger' : 'text-success' }}">
                                    {{ $expiredDate }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <a href="{{ url('license') }}"
                                class="btn btn-primary w-100 rounded-pill fw-bold py-3 text-uppercase"
                                style="letter-spacing: 1px;">
                                <i class="iconoir-refresh-double" style="margin-right: 5px;"></i> Perbarui Lisensi
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
