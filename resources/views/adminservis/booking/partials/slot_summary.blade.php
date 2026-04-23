<!-- Slot Availability Summary -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-clock mr-1"></i> Status Slot Tanggal: {{ \Carbon\Carbon::parse($tanggalSlot)->translatedFormat('d F Y') }}
        </h6>
        @php
            $currentRoute = request()->route()->getName();
            $resetRoute = $currentRoute;
        @endphp
        @if(request('tanggal'))
            <a href="{{ route($resetRoute) }}" class="btn btn-sm btn-outline-secondary">Reset Tanggal</a>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            @php
                $workHours = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];
            @endphp
            @foreach($workHours as $hour)
                @php
                    // Normalisasi string jam dari DB (bisa 08:00:00) ke 08:00
                    $usage = 0;
                    foreach($slotUsage as $dbHour => $count) {
                        if(strpos($dbHour, $hour) === 0) {
                            $usage = $count;
                            break;
                        }
                    }
                    $isFull = $usage >= 3;
                @endphp
                <div class="col-6 col-md-3 col-xl-1-5 mb-3">
                    <div class="border rounded p-2 text-center {{ $isFull ? 'bg-danger-soft border-danger' : 'bg-light' }}">
                        <div class="small font-weight-bold">{{ $hour }}</div>
                        <div class="mt-1">
                            @if($isFull)
                                <span class="badge badge-danger">Penuh (3/3)</span>
                            @else
                                <span class="badge badge-success">Tersedia ({{ $usage }}/3)</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@once
@push('styles')
<style>
    .bg-danger-soft {
        background-color: #fff5f5;
    }
    .col-xl-1-5 {
        flex: 0 0 12.5%;
        max-width: 12.5%;
    }
    @media (max-width: 1200px) {
        .col-xl-1-5 {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }
    @media (max-width: 576px) {
        .col-xl-1-5 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
</style>
@endpush
@endonce
