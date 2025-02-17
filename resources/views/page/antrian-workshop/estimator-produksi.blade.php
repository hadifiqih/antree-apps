@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')
@section('username', Auth::user()->name)
@section('page', 'Antrian')
@section('breadcrumb', 'Detail Antrian')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <a href="javascript:window.history.back()" class="btn btn-sm btn-secondary mb-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">Detail Antrian {{ $antrian->ticket_order }}</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Sales</th>
                            <td>{{ $antrian->sales->sales_name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tanggal Order</th>
                            <td>{{ $antrian->created_at }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Nama Project</th>
                            <td>{{ $antrian->order->title }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Nama Pelanggan</th>
                            <td>{{ $antrian->customer->nama }} - 
                                @if($antrian->customer->frekuensi_order == '1')
                                <span class="badge badge-success">Pelanggan Baru</span>
                                @elseif($antrian->customer->frekuensi_order >= '2')
                                <span class="badge badge-danger">Repeat Order</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Telepon Pelanggan</th>
                            <td>{{ $antrian->customer->telepon }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Alamat</th>
                            <td>{{ $antrian->customer->alamat }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Sumber Pelanggan</th>
                            <td>{{ $antrian->customer->infoPelanggan }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Referensi Desain</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/ref-desain/'. $antrian->order->desain) }}" class="img-fluid rounded" style="max-width: 200px" alt="Referensi Desain">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">ACC Desain</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/acc-desain/'. $antrian->order->acc_desain) }}" class="img-fluid rounded" style="max-width: 200px" alt="ACC Desain">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">Harga</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Nama Produk</th>
                                    <td>{{$antrian->job->job_name}}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah</th>
                                    <td>{{$antrian->qty}}</td>
                                </tr>
                                <tr>
                                    <th>Harga</th>
                                    <td>Rp {{number_format($antrian->harga_produk, 0,',','.')}}</td>
                                </tr>
                                <tr>
                                    <th>Biaya Pasang</th>
                                    <td>Rp {{number_format($antrian->payment->installation_cost ?? 0, 0,',','.')}}</td>
                                </tr>
                                <tr>
                                    <th>Biaya Pengiriman</th>
                                    <td>Rp {{number_format($antrian->payment->shipping_cost ?? 0, 0,',','.')}}</td>
                                </tr>
                                <tr>
                                    <th>Biaya Packing</th>
                                    <td>Rp {{number_format($antrian->packing_cost ?? 0, 0,',','.')}}</td>
                                </tr>
                            </table>
                            <h6 class="mt-3">Spesifikasi:</h6>
                            <p>{!! nl2br(e($antrian->note)) !!}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Omset</th>
                            <td>{{ number_format($antrian->omset, 0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Mulai</th>
                            <td>{{ $antrian->start_job }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Deadline</th>
                            <td>{{ $antrian->end_job }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Desainer</th>
                            <td>{{ $antrian->order->employee->name }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Tempat Workshop</th>
                            <td>
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
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Operator</th>
                            <td>
                                @if($antrian->operator_id != null)
                                    @php
                                    $operatorId = explode(',', $antrian->operator_id);
                                    foreach ($operatorId as $item) {
                                        if($item == 'rekanan'){
                                            echo '- Rekanan<br>';
                                        }
                                        else{
                                            $antriann = App\Models\Employee::find($item);
                                            echo '- ' . $antriann->name . "<br>";
                                        }
                                    }
                                    @endphp
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Finishing</th>
                            <td>
                                @if($antrian->finisher_id != null)
                                    @php
                                    $finisherId = explode(',', $antrian->finisher_id);
                                    foreach ($finisherId as $item) {
                                        if($item == 'rekanan'){
                                            echo '- Rekanan<br>';
                                        }
                                        else{
                                            $antriann = App\Models\Employee::find($item);
                                            echo '- ' . $antriann->name . "<br>";
                                        }
                                    }
                                    @endphp
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">QC</th>
                            <td>
                                @if($antrian->qc_id)
                                    @php
                                    $qcId = explode(',', $antrian->qc_id);
                                    foreach ($qcId as $item) {
                                        $antriann = App\Models\Employee::find($item);
                                        echo '- ' . $antriann->name . "<br>";
                                    }
                                    @endphp
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Keterangan Pembayaran</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal Pembayaran</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Bukti Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($antrian->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>Rp {{ number_format($payment->payment_amount, 0,',','.') }}</td>
                                        <td>
                                            @if($payment->checked_status == '0' || $payment->checked_status == null)
                                            <span class="badge badge-warning p-2 text-sm">Menunggu Pengecekan</span>
                                            @elseif($payment->checked_status == '1')
                                            <span class="badge badge-success">Diterima</span>
                                            @elseif($payment->checked_status == '2')
                                            <span class="badge badge-danger">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->payment_proof != null)
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paymentModal{{ $payment->id }}">
                                                Lihat Bukti
                                            </button>
                                            @else
                                            Tidak Ditemukan
                                            @endif
                                    </tr>
                                    @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel">Bukti Pembayaran</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/bukti-pembayaran/'. $payment->payment_proof) }}" class="img-fluid" alt="Bukti Pembayaran">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold">Keterangan Admin</h6>
                            <p class="card-text">{{ $antrian->admin_note != null ? $antrian->admin_note : "-" }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection