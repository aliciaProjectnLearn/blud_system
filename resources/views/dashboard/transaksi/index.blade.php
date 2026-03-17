@extends('layouts.app')

@section('content')

<div class="container-fluid">

<h1 class="h3 mb-2 text-gray-800">Monitoring Transaksi</h1>

<p class="mb-4">
Super Admin dapat melihat semua transaksi dari sistem AC, Futsal, dan Ruko.
</p>

<div class="card shadow mb-4">

<div class="card-header py-3">
<h6 class="m-0 font-weight-bold text-primary">
Daftar Transaksi
</h6>
</div>

<div class="card-body">

    <form method="GET" class="mb-3">

<div class="row">

<div class="col-md-4">
<input 
type="text" 
name="search" 
class="form-control"
placeholder="Cari ID transaksi..."
value="{{ request('search') }}">
</div>

<div class="col-md-3">
<select name="sistem" class="form-control">

<option value="">Semua Sistem</option>

<option value="AC" {{ request('sistem') == 'AC' ? 'selected' : '' }}>
AC
</option>

<option value="Futsal" {{ request('sistem') == 'Futsal' ? 'selected' : '' }}>
Futsal
</option>

<option value="Ruko" {{ request('sistem') == 'Ruko' ? 'selected' : '' }}>
Ruko
</option>

</select>
</div>

<div class="col-md-3">
<select name="status" class="form-control">

<option value="">Semua Status</option>

<option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>
Menunggu
</option>

<option value="verifikasi" {{ request('status') == 'verifikasi' ? 'selected' : '' }}>
Verifikasi
</option>

<option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>
Dibatalkan
</option>

</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary btn-block">
Filter
</button>
</div>

</div>

</form>

<div class="table-responsive">

<table class="table table-bordered" id="dataTable">

<thead>
<tr>
<th>No</th>
<th>Sistem</th>
<th>ID Transaksi</th>
<th>Total</th>
<th>Status</th>
<th>Tanggal Bayar</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

@foreach($transaksi as $t)

<tr>

<td>{{ $loop->iteration }}</td>

<td>

@if($t->sistem == 'AC')
<span class="badge badge-primary">AC</span>
@elseif($t->sistem == 'Futsal')
<span class="badge badge-success">Futsal</span>
@else
<span class="badge badge-warning">Ruko</span>
@endif

</td>

<td>{{ $t->id }}</td>

<td>
Rp {{ number_format($t->total,0,',','.') }}
</td>

<td>

@if($t->status == 'menunggu')
<span class="badge badge-secondary">Menunggu</span>
@elseif($t->status == 'verifikasi')
<span class="badge badge-success">Verifikasi</span>
@else
<span class="badge badge-danger">Dibatalkan</span>
@endif

</td>

<td>
{{ $t->tgl_bayar ?? '-' }}
</td>

<td>

<a href="#" class="btn btn-info btn-sm">
Detail
</a>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>
</div>
</div>
</div>

@endsection