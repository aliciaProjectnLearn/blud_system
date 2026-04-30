@php
    $COLOR_TERSIDIA = '#1cc88a';
    $COLOR_DISEWA   = '#e74a3b';
    $COLOR_LAINNYA  = '#858796';
@endphp

<div class="denah-wrapper bg-light rounded shadow-inner" style="overflow: auto; max-height: 500px;">
    <svg id="denah-kantin-svg" viewBox="0 0 1000 600" width="1000" height="600" preserveAspectRatio="xMidYMid meet">
        <!-- Background Grid/Pattern (Optional for aesthetics) -->
        <defs>
            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#e3e6f0" stroke-width="0.5"/>
            </pattern>
        </defs>
        <rect width="1000" height="600" fill="url(#grid)" />

        <!-- Area Landmarks -->
        <rect x="0" y="250" width="1000" height="100" fill="#eaecf4" rx="5" />
        <text x="500" y="310" text-anchor="middle" font-weight="bold" fill="#858796" font-size="20" opacity="0.4">JALAN UTAMA / AKSES PARKIR</text>

        <!-- Dynamic Units -->
        @foreach($rukoList as $ruko)
            @php
                $status = $ruko->status;
                $color = $COLOR_LAINNYA;
                if ($status === 'tersedia') $color = $COLOR_TERSIDIA;
                if ($status === 'disewa') $color = $COLOR_DISEWA;

                $x = $ruko->posisi_x ?? 50;
                $y = $ruko->posisi_y ?? 50;
                $w = 70;
                $h = 70;
                
                $isTersedia = ($status === 'tersedia');
            @endphp
            
            <g class="unit-group" 
               cursor="{{ $isTersedia ? 'pointer' : 'default' }}"
               onclick="{{ $isTersedia ? "selectUnit($ruko->id)" : "" }}"
               data-toggle="tooltip" 
               data-html="true"
               title="<strong>{{ $ruko->nama_ruko }}</strong><br>Status: {{ ucfirst($status) }}<br>Ukuran: {{ $ruko->ukuran_ruko ?? '-' }}">
                
                <!-- Unit Box -->
                <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="{{ $h }}" 
                      fill="{{ $color }}" stroke="white" stroke-width="2" rx="4" class="unit-rect" />
                
                <!-- Unit Label (Inside Box) -->
                <text x="{{ $x + $w/2 }}" y="{{ $y + $h/2 + 5 }}" 
                      text-anchor="middle" font-size="10" font-weight="bold" fill="white">
                    {{ $ruko->kode_unit }}
                </text>

                <!-- Icon Decor -->
                <circle cx="{{ $x + 10 }}" cy="{{ $y + 10 }}" r="4" fill="rgba(255,255,255,0.3)" />
            </g>
        @endforeach
    </svg>
</div>

<style>
    .denah-wrapper {
        border: 2px solid #eaecf4;
        background-color: #f8f9fc !important;
    }
    .unit-rect {
        transition: all 0.2s ease;
    }
    .unit-group:hover .unit-rect {
        filter: brightness(1.15);
        stroke-width: 3;
        transform: translateY(-2px);
    }
    /* Shadow inner effect for SVG container */
    .shadow-inner {
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
    }
</style>

<script>
    function selectUnit(id) {
        window.location.href = "{{ url('/kantin/booking') }}/" + id;
    }
</script>
