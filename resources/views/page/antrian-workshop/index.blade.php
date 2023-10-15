@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Antrian')

@section('breadcrumb', 'Antrian Stempel')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
</div>
@endif

{{-- Alert success-update --}}
@if(session('success-update'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success-update') }}
</div>
@endif

{{-- Alert successToAntrian --}}
@if(session('successToAntrian'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('successToAntrian') }}
</div>
@endif

{{-- Alert success-dokumentasi --}}
@if(session('success-dokumentasi'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success-dokumentasi') }}
</div>
@endif

@if(session('success-progress'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success-progress') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  {{ session('error') }}
</div>
@endif

{{-- Alert error --}}

{{-- Content Table --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs mb-2" id="custom-content-below-tab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="custom-content-below-home-tab" data-toggle="pill" href="#custom-content-below-home" role="tab" aria-controls="custom-content-below-home" aria-selected="true">Dikerjakan</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="custom-content-below-profile-tab" data-toggle="pill" href="#custom-content-below-profile" role="tab" aria-controls="custom-content-below-profile" aria-selected="false">Selesai</a>
                    </li>
                  </ul>
                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade show active" id="custom-content-below-home" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Antrian Stempel</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="dataAntrian" class="table table-responsive table-bordered table-hover" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Ticket Order</th>
                                            <th scope="col">Sales</th>
                                            <th scope="col">Jenis Produk</th>
                                            <th scope="col">Qty</th>
                                            <th scope="col">Deadline</th>
                                            <th scope="col">File Desain</th>
                                            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'stempel' || auth()->user()->role == 'advertising')
                                            <th scope="col">File Produksi</th>
                                            @endif
                                            <th scope="col">Desainer</th>
                                            <th scope="col">Operator</th>
                                            <th scope="col">Finishing</th>
                                            <th scope="col">QC</th>
                                            <th scope="col">Tempat</th>
                                            <th scope="col">Catatan Admin</th>
                                            @if(auth()->user()->role == 'admin')
                                                <th scope="col">Aksi</th>
                                            @elseif(auth()->user()->role == 'stempel' || auth()->user()->role == 'advertising')
                                            <th scope="col">Progress</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($antrians as $antrian)
                                            <tr>
                                                <td>
                                                @if($antrian->end_job == null)
                                                    <p class="text-danger">{{ $antrian->ticket_order }}<i class="fas fa-circle"></i></p>
                                                @else
                                                    <p class="text-success">{{ $antrian->ticket_order }}</p>
                                                @endif
                                                </td>
                                                <td>{{ $antrian->sales->sales_name }}
                                                @if($antrian->order->is_priority == 1)
                                                    <span><i class="fas fa-star text-warning"></i></span>
                                                @endif
                                                </td>
                                                <td>{{ $antrian->job->job_name }} <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#detailAntrian{{ $antrian->id }}"><i class="fas fa-info-circle"></i></button></td>
                                                <td>{{ $antrian->qty }}</td>

                                                <td id="waktu{{ $antrian->id }}" class="text-center"></td>

                                                {{-- File dari Desainer --}}
                                                <td class="text-center">
                                                    @if($antrian->order->link_file == null)
                                                        @if($antrian->order->ada_revisi == 0)
                                                        <a class="btn btn-dark btn-sm" href="{{ route('design.download', $antrian->id) }}">Download</a>
                                                        @elseif($antrian->order->ada_revisi == 1)
                                                        <span class="text-danger text-sm">(Sedang Direvisi)</span>
                                                        @elseif($antrian->order->ada_revisi == 2)
                                                        <a class="btn btn-success btn-sm" href="{{ route('design.download', $antrian->id) }}">Download</a><span class="text-danger text-sm">(Sudah Direvisi)</span>
                                                        @endif
                                                    @else
                                                        @if($antrian->order->ada_revisi == 0)
                                                        <a class="btn btn-dark btn-sm" href="{{ $antrian->order->link_file }}" target="_blank">Akses Link</a>
                                                        @elseif($antrian->order->ada_revisi == 1)
                                                        <span class="text-danger text-sm">(Sedang Direvisi)</span>
                                                        @elseif($antrian->order->ada_revisi == 2)
                                                        <a class="btn btn-success btn-sm" href="{{ $antrian->order->link_file }}" target="_blank">Akses Link</a><span class="text-danger text-sm">(Sudah Direvisi)</span>
                                                        @endif
                                                    @endif
                                                </td>

                                                {{-- File dari Produksi --}}
                                                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'stempel' || auth()->user()->role == 'advertising')
                                                    @if($antrian->design_id != null && $antrian->is_aman == 1)
                                                        <td>
                                                            <a class="btn bg-indigo btn-sm" href="{{ route('antrian.downloadProduksi', $antrian->id) }}" target="_blank">Download</a>
                                                        </td>
                                                    @elseif($antrian->design_id == null && $antrian->is_aman == 1)
                                                        <td>
                                                            <p class="text-success"><i class="fas fa-check-circle"></i> File Desain Aman</p>
                                                        </td>
                                                    @elseif($antrian->design_id == null && $antrian->is_aman == 0)
                                                        <td>
                                                            <a class="text-danger" href="#">File Desain Dalam Pengecekan</a>
                                                        </td>
                                                    @endif
                                                @endif

                                                <td>
                                                    {{-- Nama Desainer --}}
                                                    @if($antrian->order->employee_id)
                                                        {{ $antrian->order->employee->name }}
                                                    @else
                                                    -
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($antrian->operator_id)
                                                    @php
                                                        $operatorId = explode(',', $antrian->operator_id);
                                                        foreach ($operatorId as $item) {
                                                            if($item == 'rekanan'){
                                                                echo '- Rekanan';
                                                            }
                                                            else{
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($operatorId)){
                                                                    echo '- ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '- ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @else
                                                    -
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($antrian->finisher_id)
                                                    @php
                                                        $finisherId = explode(',', $antrian->finisher_id);
                                                        foreach ($finisherId as $item) {
                                                            if($item == 'rekanan'){
                                                                echo '- Rekanan';
                                                            }
                                                            else{
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($finisherId)){
                                                                    echo '- ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '- ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @else
                                                    -
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($antrian->qc_id)
                                                    @php
                                                        $qcId = explode(',', $antrian->qc_id);
                                                        foreach ($qcId as $item) {
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($qcId)){
                                                                    echo '- ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '- ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                    @endphp
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $tempat = explode(',', $antrian->working_at);
                                                        foreach ($tempat as $item) {
                                                                if($item == 'Surabaya'){
                                                                    if($item == end($tempat)){
                                                                        echo '- Surabaya';
                                                                    }
                                                                    else{
                                                                        echo '- Surabaya' . "<br>";
                                                                    }
                                                                }elseif ($item == 'Kediri') {
                                                                    if($item == end($tempat)){
                                                                        echo '- Kediri';
                                                                    }
                                                                    else{
                                                                        echo '- Kediri' . "<br>";
                                                                    }
                                                                }elseif ($item == 'Malang') {
                                                                    if($item == end($tempat)){
                                                                        echo '- Malang';
                                                                    }
                                                                    else{
                                                                        echo '- Malang' . "<br>";
                                                                    }
                                                                }
                                                            }
                                                    @endphp
                                                </td>
                                                <td>{{ $antrian->admin_note != null ? $antrian->admin_note : "-" }}</td>

                                                @if(auth()->user()->role == 'admin')
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-warning">Ubah</button>
                                                        <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                            <div class="dropdown-menu" role="menu">
                                                                <a class="dropdown-item" href="{{ url('antrian/'.$antrian->id. '/edit') }}"><i class="fas fa-xs fa-pen"></i> Edit</a>
                                                                <a class="dropdown-item {{ $antrian->end_job ? 'text-warning' : 'disabled' }}" href="{{ route('cetak-espk', $antrian->id) }}" target="_blank"><i class="fas fa-xs fa-print"></i> Unduh e-SPK</a>
                                                                <a class="dropdown-item {{ $antrian->end_job ? 'text-success' : 'text-muted disabled' }}" href="{{ route('antrian.markSelesai', $antrian->id) }}"><i class="fas fa-xs fa-check"></i> Tandai Selesai</a>
                                                                {{-- <a class="dropdown-item text-danger disabled" href="{{ route('cetak-espk', $antrian->id) }}" target="_blank"><i class="fas fa-xs fa-print"></i> Cetak e-SPK</a> --}}
                                                                <form
                                                                    action="{{ route('antrian.destroy', $antrian->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data antrian ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item"
                                                                        data-id="{{ $antrian->id }}">
                                                                        <i class="fas fa-xs fa-trash"></i> Hapus
                                                                    </button>
                                                                </form>
                                                            </div>
                                                    </div>
                                                </td>
                                                @endif

                                                @if(auth()->user()->role == 'stempel' || auth()->user()->role == 'advertising')
                                                <td>
                                                    @php
                                                        $waktuSekarang = date('H:i');
                                                        $waktuAktif = '15:00';
                                                    @endphp
                                                    <div class="btn-group">
                                                        @if( $waktuSekarang > $waktuAktif )
                                                            @if($antrian->timer_stop != null && $antrian->end_job != null)
                                                                <a href="" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Sip</a>
                                                            @else
                                                                <a type="button" class="btn btn-outline-danger btn-sm" href="{{ route('antrian.showProgress', $antrian->id) }}">Upload</a>
                                                            @endif
                                                        @elseif( $waktuSekarang < $waktuAktif )
                                                            <a type="button" class="btn btn-outline-danger btn-sm disabled"
                                                            href="#">Belum Aktif</a>
                                                        @endif
                                                        @if($antrian->end_job != null)
                                                            <a href="{{ route('antrian.showDokumentasi', $antrian->id) }}" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Tandai Selesai</a>
                                                        @else
                                                            <a href="" class="btn btn-outline-success btn-sm disabled"><i class="fas fa-check"></i> Tandai Selesai</a>
                                                        @endif
                                                    </div>
                                                </td>
                                                @endif

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @foreach($antrians as $antrian)
                                <div class="modal fade" id="detailAntrian{{ $antrian->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title">Detail #{{ $antrian->ticket_order }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="form-label">Nama Project</label>
                                                <input type="text" class="form-control" value="{{ $antrian->order->title }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Sales</label>
                                                <input type="text" class="form-control" value="{{ $antrian->sales->sales_name }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Nama Pelanggan
                                                    @if($antrian->customer->frekuensi_order == 0)
                                                    <span class="badge badge-danger">New Leads</span>
                                                    @elseif($antrian->customer->frekuensi_order == 1)
                                                    <span class="badge badge-warning">New Customer</span>
                                                    @elseif($antrian->customer->frekuensi_order >= 2)
                                                    <span class="badge badge-success">Repeat Order</span>
                                                    @endif
                                                </label>
                                                <input type="text" class="form-control" value="{{ $antrian->customer->nama }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Telepon/WA</label>
                                                <input type="text" class="form-control" value="{{ $antrian->customer->telepon }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Alamat Pelanggan</label>
                                                <textarea class="form-control" rows="3" readonly>{{ $antrian->customer->alamat }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Sumber Pelanggan</label>
                                                <input type="text" class="form-control" value="{{ $antrian->customer->infoPelanggan }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Nama Produk</label>
                                                <input type="text" class="form-control" value="{{ $antrian->job->job_name }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Jumlah Produk (Qty)</label>
                                                <input type="text" class="form-control" value="{{ $antrian->qty }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Keterangan / Spesifikasi Produk</label>
                                                <textarea class="form-control" rows="6" readonly>{{ $antrian->note }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Deadline</label>
                                                <input type="text" class="form-control" value="{{ $antrian->end_job }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Desainer</label>
                                                <input type="text" class="form-control" value="{{ $antrian->order->employee->name }}" readonly>
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <label for="form-label">Tempat : </label>
                                                    @php
                                                        $tempat = explode(',', $antrian->working_at);
                                                        foreach ($tempat as $item) {
                                                                if($item == 'Surabaya'){
                                                                    if($item == end($tempat)){
                                                                        echo '<a class="btn btn-sm btn-danger ml-2 mr-2">Surabaya</a>';
                                                                    }
                                                                    else{
                                                                        echo '<a class="btn btn-sm btn-danger ml-2 mr-2">Surabaya</a>';
                                                                    }
                                                                }elseif ($item == 'Kediri') {
                                                                    if($item == end($tempat)){
                                                                        echo '<a class="btn btn-sm btn-warning ml-2 mr-2">Kediri</a>';
                                                                    }
                                                                    else{
                                                                        echo '<a class="btn btn-sm btn-warning ml-2 mr-2">Kediri</a>';
                                                                    }
                                                                }elseif ($item == 'Malang') {
                                                                    if($item == end($tempat)){
                                                                        echo '<a class="btn btn-sm btn-success ml-2 mr-2">Malang</a>';
                                                                    }
                                                                    else{
                                                                        echo '<a class="btn btn-sm btn-success ml-2 mr-2">Malang</a>';
                                                                    }
                                                                }
                                                            }
                                                    @endphp
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4 rounded-2">
                                                <label>Operator</label>
                                                <p>
                                                @if($antrian->operator_id)
                                                    @php
                                                        $operatorId = explode(',', $antrian->operator_id);
                                                        foreach ($operatorId as $item) {
                                                            if($item == 'rekanan'){
                                                                echo '<i class="fas fa-user-circle"></i> Rekanan';
                                                            }
                                                            else{
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($operatorId)){
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @else
                                                    -
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-4 rounded-2">
                                                <label>Finishing</label>
                                                <p>
                                                @if($antrian->finisher_id)
                                                    @php
                                                        $finisherId = explode(',', $antrian->finisher_id);
                                                        foreach ($finisherId as $item) {
                                                            if($item == 'rekanan'){
                                                                echo '<i class="fas fa-user-circle"></i> Rekanan';
                                                            }
                                                            else{
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($finisherId)){
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @else
                                                    -
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-4 rounded-2">
                                                <label>Pengawas / QC</label>
                                                <p>
                                                @if($antrian->qc_id)
                                                    @php
                                                        $qcId = explode(',', $antrian->qc_id);
                                                        foreach ($qcId as $item) {
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($qcId)){
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                    @endphp
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>
                                            </div>
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <label for="">Mesin : </label>
                                                @if($antrian->machine_code)
                                                        @php
                                                            $machineCode = explode(',', $antrian->machine_code);

                                                            foreach ($machineCode as $item) {
                                                                    $antriann = App\Models\Machine::where('machine_code', $item)->first();
                                                                    //tampilkan name dari tabel machines, jika nama terakhir tidak perlu koma
                                                                    if($antriann->machine_code == end($machineCode)){
                                                                        echo '<a class="btn btn-sm btn-dark ml-2 mr-2">' . $antriann->machine_name . '</a>';
                                                                    }
                                                                    else{
                                                                        echo '<a class="btn btn-sm btn-dark ml-2 mr-2">' . $antriann->machine_name . '</a>';
                                                                    }
                                                                }
                                                        @endphp
                                                    @else
                                                        -
                                                    @endif
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <label>Nominal Omset</label>
                                                <input type="text" class="form-control" value="Rp {{ number_format($antrian->omset, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Harga Produk</label>
                                                <input type="text" class="form-control" value="Rp {{ number_format($antrian->harga_produk, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Status Pembayaran</label>
                                                <input type="text" class="form-control" value="{{ $antrian->payment->payment_status }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Metode Pembayaran</label>
                                                <input type="text" class="form-control" value="{{ $antrian->payment->payment_method }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Sisa Pembayaran</label>
                                                <input type="text" class="form-control" value="Rp {{ number_format($antrian->payment->remaining_payment, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Biaya Jasa Pasang</label>
                                                <input type="text" class="form-control" value="Rp {{ $antrian->payment->installation_cost == null ? '-' : number_format($antrian->payment->installation_cost, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Biaya Jasa Pengiriman</label>
                                                <input type="text" class="form-control" value="Rp {{ $antrian->payment->shipping_cost == null ? '-' : number_format($antrian->payment->shipping_cost, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat Pengiriman</label>
                                                <textarea class="form-control" rows="6" readonly>{{ $antrian->alamat_pengiriman == null ? '-' : $antrian->alamat_pengiriman }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Preview ACC Desain</label>
                                                        <div class="text-muted font-italic">{{ strlen($antrian->order->acc_desain) > 25 ? substr($antrian->order->acc_desain, 0, 25) . '...' : $antrian->order->acc_desain }}<button type="button" class="btn btn-sm btn-primary ml-3" data-toggle="modal" data-target="#modal-accdesain{{ $antrian->id }}">Lihat</button></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Preview Bukti Pembayaran</label>
                                                        <div class="text-muted font-italic">{{ strlen($antrian->payment->payment_proof) > 25 ? substr($antrian->payment->payment_proof, 0, 25) . '...' : $antrian->payment->payment_proof }}<button type="button" class="btn btn-sm btn-primary ml-3" data-toggle="modal" data-target="#modal-buktiPembayaran{{ $antrian->id }}">Lihat</button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                @endforeach
                                <!-- /.card -->
                                @if(auth()->user()->role == 'stempel' || auth()->user()->role == 'advertising')
                                    <p class="text-muted font-italic mt-2 text-sm">*Tombol <span class="text-danger">"Upload Progress"</span> akan aktif diatas jam 15.00</p>
                                @endif
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-content-below-profile" role="tabpanel" aria-labelledby="custom-content-below-profile-tab">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Antrian Stempel</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="dataAntrianSelesai" class="table table-responsive table-bordered table-hover" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Ticket Order</th>
                                            <th scope="col">Keyword Project</th>
                                            <th scope="col">Sales</th>
                                            <th scope="col">Jenis Produk</th>
                                            <th scope="col">Desain</th>
                                            <th scope="col">Dokumentasi</th>
                                            @if(auth()->user()->role == 'sales')
                                            <th scope="col">Pelunasan</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($antrianSelesai as $antrian)
                                            <tr>
                                                <td>{{ $antrian->ticket_order }}</td>
                                                <td>{{ $antrian->order->title }}</td>
                                                <td>{{ $antrian->sales->sales_name }}
                                                    @if($antrian->order->is_priority == 1)
                                                    <span><i class="fas fa-star text-warning"></i></span>
                                                    @endif
                                                </td>
                                                <td>{{ $antrian->job->job_name }} <button class="btn btn-primary btn-sm" data-target="#detailAntrianSelesai{{ $antrian->id }}" data-toggle="modal"><i class="fas fa-info-circle"></i></button></td>

                                                <td class="text-center">
                                                    @if($antrian->order->ada_revisi == 0)
                                                    <a class="btn btn-dark btn-sm" href="{{ route('design.download', $antrian->id) }}">Download</a>
                                                    @elseif($antrian->order->ada_revisi == 1)
                                                    <a class="btn btn-warning btn-sm disabled" href="#">Download</a><span class="text-danger">(Sedang Direvisi)</span>
                                                    @elseif($antrian->order->ada_revisi == 2)
                                                    <a class="btn btn-success btn-sm" href="{{ route('design.download', $antrian->id) }}">Download</a><div class="text-danger text-sm">(Terdapat Revisi)</div>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if($antrian->timer_stop != null)
                                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#selesaiProgress{{ $antrian->id }}">Lihat</button>
                                                        <div class="modal fade" id="selesaiProgress{{ $antrian->id }}">
                                                            <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                <h4 class="modal-title">Dokumentasi Hasil Produksi</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                    <p><strong>Gambar</strong></p>
                                                                        @if($antrian->timer_stop != null)
                                                                        @php
                                                                            $dokumentasi = App\Models\Documentation::where('antrian_id', $antrian->id)->orderBy('created_at', 'desc')->get();
                                                                        @endphp
                                                                        @foreach ($dokumentasi as $gambar)
                                                                            <img src="{{ asset('storage/dokumentasi/'.$gambar->filename) }}" alt="" class="img-fluid p-3">
                                                                        @endforeach
                                                                        @else
                                                                        <p class="text-danger">Tidak ada gambar</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                                                </div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                            </div>
                                                            <!-- /.modal-dialog -->
                                                        </div>
                                                        <!-- /.modal -->
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                @if(auth()->user()->role == 'sales')
                                                <td>
                                                        @if($antrian->payment->payment_status == 'Belum Bayar' || $antrian->payment->payment_status == 'DP')
                                                        <button class="btn btn-sm btn-danger" data-target="#modalPelunasan{{ $antrian->id }}" data-toggle="modal" ><i class="fas fa-upload"></i> Pelunasan</button>
                                                        {{-- Modal Pelunasan  --}}
                                                        <div class="modal fade" id="modalPelunasan{{ $antrian->id }}">
                                                            <div class="modal-dialog modal-sm">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                <h4 class="modal-title">Upload Pelunasan</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{ route('payment.pelunasan') }}" method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        {{-- Jumlah Pembayaran --}}
                                                                        <div class="form-group mb-1">
                                                                            <label for="form-label">Jumlah Pembayaran</label>
                                                                            <input id="jumlahPembayaran" type="text" class="form-control maskRupiah" name="jumlahPembayaran" placeholder="Contoh : Rp 10.000" required>
                                                                        </div>
                                                                            <p id="keterangan" class="my-1"></p>
                                                                        {{-- File Bukti Pembayaran --}}
                                                                        <p class="mb-2"><strong>Bukti Pembayaran</strong></p>
                                                                        <div class="input-group">
                                                                            <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" id="exampleInputFile" name="filePelunasan" required>
                                                                            <label class="custom-file-label" for="exampleInputFile">Pilih File</label>
                                                                            </div>
                                                                        </div>
                                                                        <p class="text-danger mt-2 mb-0">Sisa Tagihan : <strong>Rp {{ number_format($antrian->payment->remaining_payment, 0, ',', '.') }}</strong></p>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <input type="hidden" name="ticketAntrian" value="{{ $antrian->ticket_order }}">
                                                                    <input id="submitUnggahBayar" type="submit" class="btn btn-primary" value="Unggah">
                                                                </form>
                                                                </div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                            </div>
                                                            <!-- /.modal-dialog -->
                                                        </div>
                                                        <!-- /.modal -->
                                                        @elseif($antrian->payment->payment_status == 'Lunas')
                                                            <p class="text-success text-center"><i class="fas fa-check-circle"></i> Lunas</p>
                                                        @endif
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- /.card -->
                            </div>
                            @foreach($antrianSelesai as $antrian)
                            <div class="modal fade" id="detailAntrianSelesai{{ $antrian->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title">Detail #{{ $antrian->ticket_order }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="form-label">Nama Project</label>
                                                <input type="text" class="form-control" value="{{ $antrian->order->title }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Sales</label>
                                                <input type="text" class="form-control" value="{{ $antrian->sales->sales_name }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Nama Pelanggan
                                                    @if($antrian->customer->frekuensi_order == 0)
                                                    <span class="badge badge-danger">New Leads</span>
                                                    @elseif($antrian->customer->frekuensi_order == 1)
                                                    <span class="badge badge-warning">New Customer</span>
                                                    @elseif($antrian->customer->frekuensi_order >= 2)
                                                    <span class="badge badge-success">Repeat Order</span>
                                                    @endif
                                                </label>
                                                <input type="text" class="form-control" value="{{ $antrian->customer->nama }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Nama Produk</label>
                                                <input type="text" class="form-control" value="{{ $antrian->job->job_name }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Jumlah Produk (Qty)</label>
                                                <input type="text" class="form-control" value="{{ $antrian->qty }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Keterangan / Spesifikasi Produk</label>
                                                <textarea class="form-control" rows="6" readonly>{{ $antrian->note }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="form-label">Deadline</label>
                                                <input type="text" class="form-control" value="{{ $antrian->end_job }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Desainer</label>
                                                <input type="text" class="form-control" value="{{ $antrian->order->employee->name }}" readonly>
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <label for="form-label">Tempat : </label>
                                                    @php
                                                        $tempat = explode(',', $antrian->working_at);
                                                        foreach ($tempat as $item) {
                                                                if($item == 'Surabaya'){
                                                                    if($item == end($tempat)){
                                                                        echo '<a class="btn btn-sm btn-danger ml-2 mr-2">Surabaya</a>';
                                                                    }
                                                                    else{
                                                                        echo '<a class="btn btn-sm btn-danger ml-2 mr-2">Surabaya</a>';
                                                                    }
                                                                }elseif ($item == 'Kediri') {
                                                                    if($item == end($tempat)){
                                                                        echo '<a class="btn btn-sm btn-warning ml-2 mr-2">Kediri</a>';
                                                                    }
                                                                    else{
                                                                        echo '<a class="btn btn-sm btn-warning ml-2 mr-2">Kediri</a>';
                                                                    }
                                                                }elseif ($item == 'Malang') {
                                                                    if($item == end($tempat)){
                                                                        echo '<a class="btn btn-sm btn-success ml-2 mr-2">Malang</a>';
                                                                    }
                                                                    else{
                                                                        echo '<a class="btn btn-sm btn-success ml-2 mr-2">Malang</a>';
                                                                    }
                                                                }
                                                            }
                                                    @endphp
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4 rounded-2">
                                                <label>Operator</label>
                                                <p>
                                                @if($antrian->operator_id)
                                                    @php
                                                        $operatorId = explode(',', $antrian->operator_id);
                                                        foreach ($operatorId as $item) {
                                                            if($item == 'rekanan'){
                                                                echo '<i class="fas fa-user-circle"></i> Rekanan';
                                                            }
                                                            else{
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($operatorId)){
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @else
                                                    -
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-4 rounded-2">
                                                <label>Finishing</label>
                                                <p>
                                                @if($antrian->finisher_id)
                                                    @php
                                                        $finisherId = explode(',', $antrian->finisher_id);
                                                        foreach ($finisherId as $item) {
                                                            if($item == 'rekanan'){
                                                                echo '<i class="fas fa-user-circle"></i> Rekanan';
                                                            }
                                                            else{
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($finisherId)){
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @else
                                                    -
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-4 rounded-2">
                                                <label>Pengawas / QC</label>
                                                <p>
                                                @if($antrian->qc_id)
                                                    @php
                                                        $qcId = explode(',', $antrian->qc_id);
                                                        foreach ($qcId as $item) {
                                                                $antriann = App\Models\Employee::find($item);
                                                                //tampilkan name dari tabel employees, jika nama terakhir tidak perlu koma
                                                                if($antriann->id == end($qcId)){
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name;
                                                                }
                                                                else{
                                                                    echo '<i class="fas fa-user-circle"></i>  ' . $antriann->name . "<br>";
                                                                }
                                                            }
                                                    @endphp
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>
                                            </div>
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <label for="">Mesin : </label>
                                                @if($antrian->machine_code)
                                                        @php
                                                            $machineCode = explode(',', $antrian->machine_code);

                                                            foreach ($machineCode as $item) {
                                                                    $antriann = App\Models\Machine::where('machine_code', $item)->first();
                                                                    //tampilkan name dari tabel machines, jika nama terakhir tidak perlu koma
                                                                    if($antriann->machine_code == end($machineCode)){
                                                                        echo '<a class="btn btn-sm btn-dark ml-2 mr-2">' . $antriann->machine_name . '</a>';
                                                                    }
                                                                    else{
                                                                        echo '<a class="btn btn-sm btn-dark ml-2 mr-2">' . $antriann->machine_name . '</a>';
                                                                    }
                                                                }
                                                        @endphp
                                                    @else
                                                        -
                                                    @endif
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <label>Nominal Omset</label>
                                                <input type="text" class="form-control" value="Rp {{ number_format($antrian->omset, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Harga Produk</label>
                                                <input type="text" class="form-control" value="Rp {{ number_format($antrian->harga_produk, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Biaya Jasa Pasang</label>
                                                <input type="text" class="form-control" value="Rp {{ $antrian->payment->installation_cost == null ? '-' : number_format($antrian->payment->installation_cost, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Biaya Jasa Pengiriman</label>
                                                <input type="text" class="form-control" value="Rp {{ $antrian->payment->shipping_cost == null ? '-' : number_format($antrian->payment->shipping_cost, 0, ',', '.') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat Pengiriman</label>
                                                <textarea class="form-control" rows="6" readonly>{{ $antrian->alamat_pengiriman == null ? '-' : $antrian->alamat_pengiriman }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Preview ACC Desain</label>
                                                        <div class="text-muted font-italic">{{ strlen($antrian->order->acc_desain) > 25 ? substr($antrian->order->acc_desain, 0, 25) . '...' : $antrian->order->acc_desain }}<button type="button" class="btn btn-sm btn-primary ml-3" data-toggle="modal" data-target="#modal-accdesain{{ $antrian->id }}">Lihat</button></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Preview Bukti Pembayaran</label>
                                                        <div class="text-muted font-italic">{{ strlen($antrian->payment->payment_proof) > 25 ? substr($antrian->payment->payment_proof, 0, 25) . '...' : $antrian->payment->payment_proof }}<button type="button" class="btn btn-sm btn-primary ml-3" data-toggle="modal" data-target="#modal-buktiPembayaran{{ $antrian->id }}">Lihat</button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                @endforeach
                            <!-- /.col -->
                        </div>
                    </div>
                </div>
            </div>
    <!-- /.container-fluid -->
    @foreach ($antrians as $antrian)
    <div class="modal fade" id="modal-accdesain{{ $antrian->id }}">
        <div class="modal-dialog modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Preview File Acc Desain</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <img class="img-fluid" src="storage/acc-desain/{{ $antrian->order->acc_desain }}">
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
      @endforeach

      @foreach ($antrianSelesai as $antrian)
        <div class="modal fade" id="modal-accdesain{{ $antrian->id }}">
            <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Preview File Acc Desain</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <img class="img-fluid" src="storage/acc-desain/{{ $antrian->order->acc_desain }}">
                </div>
                <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
            <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
      <!-- /.modal -->
      @endforeach

      @foreach ($antrians as $antrian)
        <div class="modal fade" id="modal-buktiPembayaran{{ $antrian->id }}">
            <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">File Bukti Pembayaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    {{-- Menampilkan payment_proof dari tabel payments --}}
                    @php
                        $paymentProof = \App\Models\Payment::where('ticket_order', $antrian->ticket_order)->get();
                    @endphp
                    @foreach ($paymentProof as $item)
                        @if($item->payment_proof == null)
                            <p class="text-danger">Tidak ada file</p>
                        @else
                        <img class="img-fluid" src="storage/bukti-pembayaran/{{ $item->payment_proof }}">
                        @endif
                    @endforeach
                </div>
                <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
            <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    @endforeach

    @foreach ($antrianSelesai as $antrian)
        <div class="modal fade" id="modal-buktiPembayaran{{ $antrian->id }}">
            <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">File Bukti Pembayaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    {{-- Menampilkan payment_proof dari tabel payments --}}
                    @php
                        $paymentProof = \App\Models\Payment::where('ticket_order', $antrian->ticket_order)->get();
                    @endphp
                    @foreach ($paymentProof as $item)
                    <img class="img-fluid" src="{{ asset('storage/bukti-pembayaran/'.$item->payment_proof) }}">
                    @endforeach
                </div>
                <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
            <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    @endforeach

@endsection

@section('script')
<script src="{{ asset('adminlte/dist/js/maskMoney.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.maskRupiah').maskMoney({prefix:'Rp ', thousands:'.', decimal:',', precision:0});

            $("#dataAntrian").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
            $("#dataAntrianSelesai").DataTable({
                "responsive": true,
                "autoWidth": false,
            });

            //Menutup modal saat modal lainnya dibuka
            $('.modal').on('show.bs.modal', function (e) {
                $('.modal').not($(this)).each(function () {
                    $(this).modal('hide');
                });
            });

            //saat tombol submitUnggahBayar di submit, maka tombol tersebut akan disabled
            $('#formUnggahBayar').on('submit', function () {
                $('#submitUnggahBayar').attr('disabled', true);
            });

            $('#jumlahPembayaran').on('keyup', function(){
                var sisatagihan = {{ $antrian->payment->remaining_payment }};
                var jumlahpembayaran = $(this).val().replace(/Rp\s|\.+/g, '');
                //convert ke integer
                sisatagihan = parseInt(sisatagihan);
                jumlahpembayaran = parseInt(jumlahpembayaran);

                //jika jumlah pembayaran lebih besar dari sisa tagihan, maka akan muncul keterangan dibawah input jumlah pembayaran
                if(jumlahpembayaran > sisatagihan){
                    $('#keterangan').html('<span class="text-danger">Jumlah pembayaran melebihi sisa tagihan.</span>');
                    $('#submitUnggahBayar').attr('disabled', true);
                }
                else{
                    $('#keterangan').html('');
                    $('#submitUnggahBayar').attr('disabled', false);
                }

            });
        });
    </script>

        {{-- Script untuk countdown timer --}}
    @foreach($antrians as $antrian)
    <script>
    @if($antrian->end_job != null && $antrian->deadline_status == 0)
    // Set the date we're counting down to
    var countDownDate{{ $antrian->id }} = new Date("{{ $antrian->end_job }}").getTime();
    var endJob{{ $antrian->id }} = new Date("{{ $antrian->end_job }}");
    var deadline{{ $antrian->id }} = "{{ $antrian->deadline_status }}";
    var timerStop{{ $antrian->id }} = new Date("{{ $antrian->timer_stop }}");

    // Update the count down every 1 second
    var x{{ $antrian->id }} = setInterval(function() {
        // Get today's date and time
        var now{{ $antrian->id }} = new Date().getTime();
        // Find the distance between now and the count down date
        var distance{{ $antrian->id }} = countDownDate{{ $antrian->id }} - now{{ $antrian->id }};
        // Time calculations for days, hours, minutes and seconds
        var days{{ $antrian->id }} = Math.floor(distance{{ $antrian->id }} / (1000 * 60 * 60 * 24));
        var hours{{ $antrian->id }} = Math.floor((distance{{ $antrian->id }} % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes{{ $antrian->id }} = Math.floor((distance{{ $antrian->id }} % (1000 * 60 * 60)) / (1000 * 60));
        var seconds{{ $antrian->id }} = Math.floor((distance{{ $antrian->id }} % (1000 * 60)) / 1000);
        // Output the result in an element with id="demo"
        document.getElementById("waktu{{ $antrian->id }}").innerHTML = "<span class='badge bg-dark'>" + days{{ $antrian->id }} + "h " + hours{{ $antrian->id }} + "j " +
            minutes{{ $antrian->id }} + "m " + seconds{{ $antrian->id }} + "d " + "</span>";
        // If the count down is over, write some text
        if (distance{{ $antrian->id }} < 0) {
                clearInterval(x{{ $antrian->id }});
                //memperbarui deadline_status menjadi terlambat
                $.ajax({
                    url: "{{ route('antrian.updateDeadline') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: "{{ $antrian->id }}"
                    },
                    success: function(response) {
                        console.log(response);
                    }
                });
            }
        }, 1000);
    @elseif($antrian->end_job != null && $antrian->deadline_status == 1)
        document.getElementById("waktu{{ $antrian->id }}").innerHTML = "<span class='badge bg-success'>Tepat Waktu</span>";
    @elseif($antrian->end_job != null && $antrian->deadline_status == 2)
        document.getElementById("waktu{{ $antrian->id }}").innerHTML = "<span class='badge bg-danger'>Terlambat</span>";
    @elseif($antrian->end_job == null)
        document.getElementById("waktu{{ $antrian->id }}").innerHTML = "<span class='badge bg-warning'>-</span>";
    @endif
    </script>
    @endforeach

@endsection
