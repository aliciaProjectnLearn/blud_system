@extends('layouts.app')

@section('title', 'Pembagian Pendapatan')

@push('styles')
<style>
    :root {
        --ac-color:      #4e73df;
        --kantin-color:  #f6c23e;
        --futsal-color:  #1cc88a;
        --servis-color:  #e74a3b;
        --jurusan-color: #6f42c1;
        --aplikasi-color:#e83e8c;
        --blud-color:    #17a2b8;
    }

    .sistem-card { border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: transform .2s, box-shadow .2s; overflow: hidden; }
    .sistem-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.13); }
    .sistem-card.card-ac     { border-top: 4px solid var(--ac-color); }
    .sistem-card.card-kantin { border-top: 4px solid var(--kantin-color); }
    .sistem-card.card-futsal { border-top: 4px solid var(--futsal-color); }
    .sistem-card.card-servis { border-top: 4px solid var(--servis-color); }

    .badge-jurusan  { background: var(--jurusan-color);  color:#fff; }
    .badge-aplikasi { background: var(--aplikasi-color); color:#fff; }
    .badge-blud     { background: var(--blud-color);     color:#fff; }

    .penerima-bar { height: 10px; border-radius: 5px; }
    .bar-jurusan  { background: var(--jurusan-color); }
    .bar-aplikasi { background: var(--aplikasi-color); }
    .bar-blud     { background: var(--blud-color); }

    .rekap-card { border-radius: 10px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    .config-panel { background: #f8f9fc; border-radius: 10px; border: 1px solid #e3e6f0; padding: 1.2rem; }
    .persen-input { width: 80px; text-align: center; font-weight: 700; border-radius: 8px; border: 2px solid #e3e6f0; transition: border-color .2s; }
    .persen-input:focus { border-color: #4e73df; outline: none; }
    .total-badge { font-size: 0.85rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
    .total-ok     { background: #d4edda; color: #155724; }
    .total-not-ok { background: #f8d7da; color: #721c24; }
    .saldo-highlight { background: linear-gradient(135deg, #f8f9fc 0%, #e8ecf8 100%); border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-1">
        <h1 class="h3 mb-0 text-gray-800 mr-3">Pembagian Pendapatan</h1>
        <span class="badge badge-primary">Super Admin</span>
    </div>
    <p class="mb-4 text-muted">Kalkulasi dan konfigurasi distribusi pendapatan bersih per sistem.</p>

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>{{ $errors->first() }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    {{-- Filter Tanggal --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" class="form-inline">
                <label class="mr-2 font-weight-bold"><i class="fas fa-calendar-alt mr-1 text-primary"></i> Periode:</label>
                <input type="date" name="start_date" class="form-control form-control-sm mr-2" value="{{ $startDate }}">
                <span class="mr-2">s/d</span>
                <input type="date" name="end_date" class="form-control form-control-sm mr-2" value="{{ $endDate }}">
                <button class="btn btn-primary btn-sm mr-2"><i class="fas fa-filter mr-1"></i>Filter</button>
                <a href="{{ route('dashboard.pembagian-pendapatan') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-undo mr-1"></i>Reset
                </a>
            </form>
        </div>
    </div>

    {{-- Rekap Global per Penerima --}}
    @php
        $ikonPenerima = [
            'jurusan'  => ['icon' => 'fa-graduation-cap', 'color' => 'jurusan',  'label' => 'Jurusan'],
            'aplikasi' => ['icon' => 'fa-laptop-code',    'color' => 'aplikasi', 'label' => 'Aplikasi'],
            'blud'     => ['icon' => 'fa-hospital',       'color' => 'blud',     'label' => 'BLUD'],
        ];
    @endphp

    <div class="row mb-4">
        @foreach($rekapPenerima as $penerima => $totalNominal)
        @php $meta = $ikonPenerima[$penerima] ?? ['icon'=>'fa-circle','color'=>'secondary','label'=>ucfirst($penerima)]; @endphp
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="rekap-card card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: var(--{{ $meta['color'] }}-color)">
                                {{ $meta['label'] }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalNominal, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">Total dari semua sistem</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas {{ $meta['icon'] }} fa-2x" style="color:var(--{{ $meta['color'] }}-color); opacity:.6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Breakdown Per Sistem --}}
    @php
        $sistemMeta = [
            'ac'     => ['label'=>'Sistem AC',            'color'=>'ac',     'icon'=>'fa-snowflake', 'saldo' => $saldoAc],
            'kantin' => ['label'=>'Sistem Kantin',         'color'=>'kantin', 'icon'=>'fa-store',     'saldo' => $saldoKantin],
            'futsal' => ['label'=>'Sistem Futsal',         'color'=>'futsal', 'icon'=>'fa-futbol',    'saldo' => $saldoFutsal],
            'servis' => ['label'=>'Servis Kendaraan',      'color'=>'servis', 'icon'=>'fa-motorcycle','saldo' => $saldoServis],
        ];
        $urutan = ['ac','kantin','futsal','servis'];
    @endphp

    <div class="row mb-4">
    @foreach($urutan as $sistem)
    @php
        $meta = $sistemMeta[$sistem];
        $data = $pembagian[$sistem];
        $cfg  = $konfigurasi->get($sistem, collect());
    @endphp
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card sistem-card card-{{ $sistem }} h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas {{ $meta['icon'] }} mr-2" style="color:var(--{{ $meta['color'] }}-color)"></i>
                    <h6 class="m-0 font-weight-bold" style="color:var(--{{ $meta['color'] }}-color)">
                        {{ $meta['label'] }}
                    </h6>
                </div>
                <button class="btn btn-sm btn-outline-secondary" data-toggle="collapse"
                        data-target="#config-{{ $sistem }}">
                    <i class="fas fa-cog"></i>
                </button>
            </div>

            <div class="card-body">
                <div class="saldo-highlight d-flex justify-content-between align-items-center">
                    <span class="text-muted small font-weight-bold text-uppercase">Saldo Bersih</span>
                    <span class="font-weight-bold" style="font-size:1.1rem; color:var(--{{ $meta['color'] }}-color)">
                        Rp {{ number_format($data['saldo_bersih'], 0, ',', '.') }}
                    </span>
                </div>

                <div class="mb-3">
                    @foreach($data['detail'] as $penerima => $info)
                    @php $pm = $ikonPenerima[$penerima] ?? ['icon'=>'fa-circle','color'=>'secondary','label'=>ucfirst($penerima)]; @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-{{ $pm['color'] }} mr-2">{{ $pm['label'] }}</span>
                                <span class="text-muted small">{{ number_format($info['persentase'], 1) }}%</span>
                            </div>
                            <span class="font-weight-bold text-gray-800 small">
                                Rp {{ number_format($info['nominal'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="progress" style="height:8px;border-radius:4px;">
                            <div class="progress-bar bar-{{ $pm['color'] }} penerima-bar"
                                 style="width: {{ $info['persentase'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Panel Konfigurasi --}}
                <div class="collapse" id="config-{{ $sistem }}">
                    <div class="config-panel mt-2">
                        <p class="text-xs text-muted font-weight-bold text-uppercase mb-2">
                            <i class="fas fa-sliders-h mr-1"></i>Atur Persentase
                        </p>
                        <form method="POST" action="{{ route('dashboard.pembagian-pendapatan.update') }}"
                              id="form-{{ $sistem }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="sistem" value="{{ $sistem }}">

                            @foreach($data['detail'] as $penerima => $info)
                            @php $pm2 = $ikonPenerima[$penerima] ?? ['icon'=>'fa-circle','color'=>'secondary','label'=>ucfirst($penerima)]; @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="mb-0 small font-weight-bold" style="color:var(--{{ $pm2['color'] }}-color)">
                                    <i class="fas {{ $pm2['icon'] }} mr-1"></i>{{ $pm2['label'] }}
                                </label>
                                <div class="input-group input-group-sm" style="width:100px">
                                    <input type="number"
                                           name="persentase[{{ $penerima }}]"
                                           class="form-control persen-input persen-field"
                                           data-sistem="{{ $sistem }}"
                                           value="{{ number_format($info['persentase'], 2, '.', '') }}"
                                           min="0" max="100" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="total-badge total-indicator-{{ $sistem }}">
                                    Total: <span class="total-val-{{ $sistem }}">0</span>%
                                </span>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-save mr-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    </div>

    {{-- Tabel Ringkasan Pembagian --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Ringkasan Pembagian Pendapatan
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Sistem</th>
                            <th class="text-right">Saldo Bersih</th>
                            @foreach(array_keys($rekapPenerima) as $p)
                            @php $pm3 = $ikonPenerima[$p] ?? ['label'=>ucfirst($p)]; @endphp
                            <th class="text-right">{{ $pm3['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($urutan as $sistem)
                        @php
                            $meta2 = $sistemMeta[$sistem];
                            $data2 = $pembagian[$sistem];
                        @endphp
                        <tr>
                            <td>
                                <i class="fas {{ $meta2['icon'] }} mr-1" style="color:var(--{{ $meta2['color'] }}-color)"></i>
                                <strong>{{ $meta2['label'] }}</strong>
                            </td>
                            <td class="text-right font-weight-bold" style="color:var(--{{ $meta2['color'] }}-color)">
                                Rp {{ number_format($data2['saldo_bersih'], 0, ',', '.') }}
                            </td>
                            @foreach(array_keys($rekapPenerima) as $p)
                            <td class="text-right">
                                @if(isset($data2['detail'][$p]))
                                    <span class="text-success font-weight-bold">
                                        Rp {{ number_format($data2['detail'][$p]['nominal'], 0, ',', '.') }}
                                    </span>
                                    <br><small class="text-muted">{{ number_format($data2['detail'][$p]['persentase'], 1) }}%</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="font-weight-bold" style="background:#f8f9fc">
                        <tr>
                            <td>TOTAL</td>
                            <td class="text-right">
                                Rp {{ number_format($saldoAc + $saldoKantin + $saldoFutsal + $saldoServis, 0, ',', '.') }}
                            </td>
                            @foreach(array_keys($rekapPenerima) as $p)
                            <td class="text-right text-success">
                                Rp {{ number_format($rekapPenerima[$p], 0, ',', '.') }}
                            </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.persen-field').forEach(input => {
    input.addEventListener('input', function() {
        updateTotal(this.dataset.sistem);
    });
});

function updateTotal(sistem) {
    const fields = document.querySelectorAll(`.persen-field[data-sistem="${sistem}"]`);
    let total = 0;
    fields.forEach(f => total += parseFloat(f.value) || 0);
    total = Math.round(total * 100) / 100;

    const badge = document.querySelector(`.total-indicator-${sistem}`);
    const valEl = document.querySelector(`.total-val-${sistem}`);
    if (valEl) valEl.textContent = total.toFixed(2);
    if (badge) {
        badge.className = `total-badge ${Math.abs(total - 100) < 0.01 ? 'total-ok' : 'total-not-ok'} total-indicator-${sistem}`;
    }
}

['ac','kantin','futsal','servis'].forEach(s => updateTotal(s));
</script>
@endpush
