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
        <div class="row mb-3">
            <form id="filterByCategory" action="{{ route('antrian.index') }}" method="GET" enctype="multipart/form-data">
                <div class="d-flex flex-row">
                    <div class="col-md-4">
                        <label for="kategori">Kategori Pekerjaan</label>
                        <select id="kategori" name="kategori" class="custom-select rounded-1">
                            <option value="">Semua</option>
                            <option value="Stempel" {{ request('kategori') == "Stempel" ? "selected" : "" }}>Stempel</option>
                            <option value="Advertising" {{ request('kategori') == "Advertising" ? "selected" : "" }}>Advertising</option>
                            <option value="Non Stempel" {{ request('kategori') == "Non Stempel" ? "selected" : "" }}>Non Stempel</option>
                            <option value="Digital Printing" {{ request('kategori') == "Digital Printing" ? "selected" : "" }}>Digital Printing</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="sales">Sales</label>
                            <select id="sales" name="sales" class="custom-select rounded-1">
                                <option value="">Semua</option>
                                @foreach($salesAll as $item)
                                    <option value="{{ $item->id }}" {{ request('sales') == $item->id ? "selected" : "" }}>{{ $item->sales_name }}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" class="btn btn-primary mt-1">Filter</button>
                    </div>
                </div>
            </form>
        </div>
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
                                            <th scope="col">Nama Customer</th>
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
                                            @elseif(auth()->user()->role == 'stempel' || auth()->user()->role == 'advertising' || auth()->user()->id == '28' || auth()->user()->role == 'estimator' || auth()->user()->role == 'sales' )
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
                                                    @if($antrian->order && $antrian->order->is_priority == 1)
                                                        <span><i class="fas fa-star text-warning"></i></span>
                                                    @endif
                                                </td>
                                                <td>{{ $antrian->customer->nama }}</td>
                                                <td>{{ $antrian->job->job_name }} <a href="{{ route('antrian.estimator-produksi', $antrian->ticket_order) }}" type="button" class="btn btn-sm btn-primary" ><i class="fas fa-info-circle"></i></a></td>
                                                <td>{{ $antrian->qty }}</td>

                                                <td class="text-center">
                                                    <span class="countdown" data-countdown="{{ $antrian->end_job }}">Loading..</span>
                                                </td>

                                                {{-- File dari Desainer --}}
                                                <td class="text-center">
                                                    @php
                                                        $isRevisi = $antrian->order->ada_revisi ?? null;
                                                        $linkFile = $antrian->order->link_file ?? null;
                                                        $href = $linkFile ? $linkFile : route('design.download', $antrian->id);
                                                        $target = $linkFile ? ' target="_blank"' : '';
                                                        $btnText = $linkFile ? 'Akses Link' : 'Download';
                                                        $btnClass = ($isRevisi == 2) ? 'btn-success' : 'btn-dark';
                                                    @endphp

                                                    @if($isRevisi == 1)
                                                        <span class="text-danger text-sm">(Sedang Direvisi)</span>
                                                    @else
                                                        <a class="btn {{ $btnClass }} btn-sm" href="{{ $href }}"{!! $target !!}>{{ $btnText }}</a>
                                                        @if($isRevisi == 2)
                                                            <span class="text-danger text-sm">(Sudah Direvisi)</span>
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
                                                    @if($antrian->operator_id != null)
                                                        @php
                                                            $operatorIdsForRow = explode(',', $antrian->operator_id);
                                                            $totalOperators = count($operatorIdsForRow);
                                                        @endphp
                                                        @foreach($operatorIdsForRow as $index => $item)
                                                            @if($item == 'rekanan')
                                                                - Rekanan
                                                            @else
                                                                @php
                                                                    // Ambil data karyawan dari variabel $employees yang sudah di-eager load
                                                                    $operator = $operators[$item] ?? null;
                                                                @endphp
                                                                @if($operator)
                                                                    - {{ $operator->name }}{!! $index < $totalOperators - 1 ? '<br>' : '' !!}
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($antrian->finisher_id != null)
                                                        @php
                                                            $finisherIdsForRow = explode(',', $antrian->finisher_id);
                                                            $totalFinishers = count($finisherIdsForRow);
                                                        @endphp
                                                        @foreach($finisherIdsForRow as $index => $item)
                                                            @if($item == 'rekanan')
                                                                - Rekanan
                                                            @else
                                                                @php
                                                                    // Ambil data karyawan dari variabel $finishing yang sudah di-eager load
                                                                    $finisher = $finishers[$item] ?? null;
                                                                @endphp
                                                                @if($finisher)
                                                                    - {{ $finisher->name }}{!! $index < $totalFinishers - 1 ? '<br>' : '' !!}
                                                                @endif
                                                            @endif
                                                            @if(!$loop->last)
                                                                <br>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($antrian->qc_id)
                                                        @php
                                                            $qcIdsForRow = explode(',', $antrian->qc_id);
                                                            $totalQCs = count($qcIdsForRow);
                                                        @endphp
                                                        @foreach($qcIdsForRow as $index => $item)
                                                            @if($item == 'rekanan')
                                                                - Rekanan
                                                            @else
                                                                @php
                                                                    // Ambil data karyawan dari variabel $qc yang sudah di-eager load
                                                                    $qc = $qcs[$item] ?? null;
                                                                @endphp
                                                                @if($qc)
                                                                    - {{ $qc->name }}{!! $index < $totalQCs - 1 ? '<br>' : '' !!}
                                                                @endif
                                                            @endif
                                                            @if(!$loop->last)
                                                                <br>
                                                            @endif
                                                        @endforeach
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
                                                                <a class="dropdown-item {{ $antrian->end_job ? 'text-warning' : 'disabled' }}" href="{{ route('cetak-espk', $antrian->ticket_order) }}" target="_blank"><i class="fas fa-xs fa-print"></i> Unduh e-SPK</a>
                                                                <a class="dropdown-item {{ $antrian->end_job ? 'text-success' : 'text-muted disabled' }}" href="{{ route('antrian.markSelesai', $antrian->id) }}"><i class="fas fa-xs fa-check"></i> Tandai Selesai</a>
                                                                {{-- <a class="dropdown-item text-danger disabled" href="{{ route('cetak-espk', $antrian->ticket_order) }}" target="_blank"><i class="fas fa-xs fa-print"></i> Cetak e-SPK</a> --}}
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

                                                @if(auth()->user()->role == 'stempel' || auth()->user()->role == 'advertising' || auth()->user()->role == 'sales' || auth()->user()->id == '28' || auth()->user()->role == 'estimator')
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
                                                            <a type="button" class="btn btn-outline-danger btn-sm disabled" href="#">Belum Aktif</a>
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
                                            <th scope="col">Nama Customer</th>
                                            <th scope="col">Sales</th>
                                            <th scope="col">Jenis Produk</th>
                                            <th scope="col">Desain</th>
                                            <th scope="col">Dokumentasi</th>
                                            @if(auth()->user()->role == 'sales' || auth()->user()->role == 'staffAdmin' || auth()->user()->role == 'adminKeuangan')
                                            <th scope="col">Pelunasan</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($antrianSelesai as $antrian)
                                            <tr>
                                                <td>{{ $antrian->ticket_order }}</td>
                                                <td>{{ $antrian->order->title ?? '-' }}</td>
                                                <td>{{ $antrian->customer->nama }}</td>
                                                <td>{{ $antrian->sales->sales_name }}
                                                    @if($antrian->order && $antrian->order->is_priority == 1)
                                                        <span><i class="fas fa-star text-warning"></i></span>
                                                    @endif
                                                </td>
                                                <td>{{ $antrian->job->job_name }} <a href="{{ route('antrian.estimator-produksi', $antrian->ticket_order) }}" type="button" class="btn btn-primary btn-sm"><i class="fas fa-info-circle"></i></a></td>

                                                <td class="text-center">
                                                    @if($antrian->order->ada_revisi == 0 && $antrian->order)
                                                    <a class="btn btn-dark btn-sm" href="{{ route('design.download', $antrian->id) }}">Download</a>
                                                    @elseif($antrian->order->ada_revisi == 1 && $antrian->order)
                                                    <a class="btn btn-warning btn-sm disabled" href="#">Download</a><span class="text-danger">(Sedang Direvisi)</span>
                                                    @elseif($antrian->order->ada_revisi == 2 && $antrian->order)
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
                                                                        @if($antrian->timer_stop != null && $antrian->documentation->count())
                                                                            @foreach ($antrian->documentation->sortByDesc('created_at') as $gambar)
                                                                                <img loading="lazy" src="{{ asset('storage/dokumentasi/'.$gambar->filename) }}" alt="" class="img-fluid p-3">
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
                                                @if(auth()->user()->role == 'sales' || auth()->user()->role == 'staffAdmin' || auth()->user()->role == 'adminKeuangan')
                                                <td>
                                                        @php
                                                            $latestPayment = App\Models\Payment::where('ticket_order', $antrian->ticket_order)->latest()->first();
                                                        @endphp
                                                        @if($latestPayment->payment_status == 'Belum Bayar' || $latestPayment->payment_status == 'DP')
                                                            @if(auth()->user()->role == 'sales')
                                                                <button id="btnModalPelunasan{{ $antrian->id }}" class="btn btn-sm btn-danger btnModalPelunasan" data-toggle="modal" data-target="#modalPelunasan{{ $antrian->id }}"><i class="fas fa-upload"></i> Pelunasan</button>
                                                                @includeIf('page.antrian-workshop.modal-pelunasan')
                                                            @elseif(auth()->user()->role == 'staffAdmin' || auth()->user()->role == 'adminKeuangan')
                                                                <button class="btn btn-sm btn-secondary disabled"> Belum Pelunasan</button>
                                                            @endif
                                                        @elseif($latestPayment->payment_status == 'Lunas')
                                                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalTampilBP{{ $antrian->id }}"><i class="fas fa-check-circle"></i> Lihat</button>
                                                            <!-- Modal -->
                                                            <div class="modal fade" id="modalTampilBP{{ $antrian->id }}" tabindex="-1" aria-labelledby="TampilBPLabel{{ $antrian->id }}" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                    <h5 class="modal-title" id="TampilBPLabel{{ $antrian->id }}">Data Pembayaran</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <h5 class="bg-danger p-2 rounded"><strong>Total Omset : </strong>Rp {{ number_format($antrian->omset, 0, ',', '.') }}</h5>
                                                                        <hr>
                                                                        @php
                                                                            $buktiPembayaran = App\Models\Payment::where('ticket_order', $antrian->ticket_order)->get();
                                                                        @endphp
                                                                        @foreach ($buktiPembayaran as $bukti)
                                                                            <table class="table table-borderless table-responsive">
                                                                                <tr>
                                                                                    <th>Tanggal Pembayaran</th>
                                                                                    <td>{{ $bukti->created_at }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Status Pembayaran</th>
                                                                                    <td>{{ $bukti->payment_status }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Metode Pembayaran</th>
                                                                                    <td>{{ $bukti->payment_method }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Jumlah Pembayaran</th>
                                                                                    <td>Rp {{ number_format($bukti->payment_amount, 0, ',', '.') }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Biaya Pengiriman</th>
                                                                                    <td>Rp {{ number_format($bukti->shipping_cost, 0, ',', '.') }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Biaya Pemasangan</th>
                                                                                    <td>Rp {{ number_format($bukti->installation_cost, 0, ',', '.') }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Sisa Pembayaran</th>
                                                                                    <td>Rp {{ number_format($bukti->remaining_payment, 0, ',', '.') }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Bukti Pembayaran</th>
                                                                                    <td>
                                                                                        <a href="{{ $bukti->payment_proof != null ? asset('storage/bukti-pembayaran/'.$bukti->payment_proof) : "#" }}" target="_blank">
                                                                                            {{ $bukti->payment_proof == null ? "-" : $bukti->payment_proof }}
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- /.card -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <!-- /.container-fluid -->
@endsection

@section('script')
<script>

</script>

<script src="{{ asset('adminlte/dist/js/maskMoney.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('.maskRupiah').maskMoney({prefix:'Rp ', thousands:'.', decimal:',', precision:0});

            $("#dataAntrian").DataTable({
                "responsive": true,
                "autoWidth": false,
                "order": [[ 0, "desc" ]],
                "pageLength": 25,
            });
            $("#dataAntrianSelesai").DataTable({
                "responsive": true,
                "autoWidth": false,
                "order": [[ 0, "desc" ]],
                "pageLength": 25,
            });

            //Menutup modal saat modal lainnya dibuka
            $('.modal').on('show.bs.modal', function (e) {
                $('.modal').not($(this)).each(function () {
                    $(this).modal('hide');
                });
            });

            // Kumpulkan semua elemen countdown dan data target-nya
            var countdownElements = [];
            $('.countdown').each(function() {
                var $this = $(this);
                var targetTime = new Date($this.data('countdown')).getTime();

                if (isNaN(targetTime)) {
                    $this.html("<span class='text-danger'>BELUM DIANTRIKAN</span>");
                } else {
                    // Simpan objek berisi element dan target time
                    countdownElements.push({
                        element: $this,
                        target: targetTime
                    });
                }
            });

            // Jika ada elemen yang memiliki countdown, gunakan satu interval untuk meng-update semuanya
            if (countdownElements.length > 0) {
                var timer = setInterval(function() {
                    var now = new Date().getTime();

                    // Iterasi untuk setiap elemen countdown
                    countdownElements.forEach(function(item, index) {
                        var distance = item.target - now;

                        if (distance < 0) {
                            item.element.html("<span class='text-danger'>TERLAMBAT</span>");
                            // Jika countdown sudah selesai, opsional: hapus elemen dari array agar tidak terus melakukan perhitungan.
                            // countdownElements.splice(index, 1);
                        } else {
                            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            item.element.html("<span class='text-success'>" + days + "d " + hours + "h " + minutes + "m " + seconds + "s</span>");
                        }
                    });
                }, 1000);
            }

            $('.metodePembayaran').on('change', function(){
                var id = $(this).attr('id').split('metodePembayaran')[1];
                var metode = $(this).val();
                if(metode == 'Tunai' || metode == 'Cash'){
                    $('.filePelunasan').hide();
                    $('#filePelunasan'+id).removeAttr('required');
                }
                else{
                    $('.filePelunasan').show();
                    $('#filePelunasan'+id).attr('required', true);
                }
            });

            $('.btnModalPelunasan').on('click', function(){
                //ambil id dari tombol submitUnggahBayar
                var id = $(this).attr('id').split('btnModalPelunasan')[1];

                $('#jumlahPembayaran'+id).on('keyup', function(){
                //ambil value dari jumlahPembayaran
                var jumlah = $('#jumlahPembayaran'+id).val().replace(/Rp\s|\.+/g, '');
                //ambil value dari sisaPembayaran
                var sisa = $('#sisaPembayaran'+id).val().replace(/Rp\s|\.+/g, '');
                //inisialisasi variabel keterangan
                var keterangan = $('#keterangan'+id);
                //inisialisasi variabel submit
                var submit = $('#submitUnggahBayar'+id);
                //jika jumlah pembayaran melebihi sisa pembayaran
                if(parseInt(jumlah) > parseInt(sisa)){
                    //tampilkan keterangan
                    keterangan.html('<span class="text-danger">Jumlah pembayaran melebihi sisa pembayaran</span>');
                    //tombol submit disabled
                    submit.attr('disabled', true);
                }
                else{
                    //sembunyikan keterangan
                    keterangan.html('');
                    //tombol submit enabled
                    submit.attr('disabled', false);
                }
                });
            });
        });
    </script>
@endsection
