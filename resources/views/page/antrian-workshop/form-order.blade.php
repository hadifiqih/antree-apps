<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Bootstrap 5 CDN--}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        /* Col-6 Bootstrap */
        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        /* row bootstrap */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        /* Container Fluid Bootstrap */
        .container-fluid {
            width: 100%;
            padding: 0;
            margin : 0;
        }

        .p-4 {
            padding: 1.5rem!important;
        }

        .wadah {
            display: flex;
        }

        .kolom {
            flex: 1;
            padding: 10px
        }
    </style>

    <title>Form Order - PDF</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; background-color:bisque; height: 150px;">
            <div class="col-6">
                <h5 class="mt-3" style="padding-left: 30px"><strong>No. Tiket : </strong>{{ $antrian->ticket_order }} <span style="padding-left: 400px; padding-top: 10px; font-size:30pt;">FORM ORDER</span></h5>
            </div>
            <div class="col-6">
                <p class="text-end" style="padding-right: 80px; padding-top:3px;"><strong>Tanggal : </strong>{{ $antrian->created_at->format('d-m-Y') }}</p>
            </div>
        </div>
        <div></div>
        <div class="row mt-3 mb-1">
            <div class="col-md-6">
                <p><strong>Nama Pelanggan : </strong>{{ $antrian->customer->nama }}
                    @if($antrian->customer->frekuensi_order == '0')
                        <span class="badge bg-success">New Leads</span>
                    @elseif($antrian->customer->frekuensi_order == '1')
                        <span class="badge bg-warning">Pelanggan Baru</span>
                    @elseif($antrian->customer->frekuensi_order >= '2')
                        <span class="badge bg-danger">Repeat Order</span>
                    @endif
                </p>
                <p><strong>No. Telepon : </strong>{{ $antrian->customer->telepon }}</p>
                <p><strong>Sumber Pelanggan : </strong>{{ $antrian->customer->infoPelanggan }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Instansi : </strong>{{ $antrian->customer->instansi }}</p>
                <p><strong>Alamat : </strong>{{ $antrian->customer->alamat }}</p>
            </div>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Nama Project : </strong>{{ $antrian->order->title }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Pekerjaan : </strong>{{ $antrian->job->job_name }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Spesifikasi : </strong></p><textarea rows="5" style="border: 0px">{{ $antrian->order->description }}</textarea>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Qty : </strong>{{ $antrian->qty }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Omset : </strong>Rp {{ number_format($antrian->omset, 0, ',', '.') }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Jumlah Pembayaran : </strong>Rp {{ number_format($antrian->payment->payment_amount, 0, ',', '.') }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Sisa Pembayaran : </strong>Rp {{ number_format($antrian->payment->remaining_payment, 0, ',', '.') }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Biaya Pengiriman : </strong>Rp {{ number_format($antrian->payment->shipping_cost, 0, ',', '.') }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Biaya Pengemasan : </strong>Rp {{ number_format($antrian->payment->installation_cost, 0, ',', '.') }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Alamat Pengiriman / Pemasangan : </strong></p><textarea rows="2" style="border: 0px">{{ $antrian->alamat_pengiriman }}</textarea>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Metode Pembayaran : </strong>{{ $antrian->payment->payment_method }}</p>
        </div>
        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Status Pembayaran : </strong>{{ $antrian->payment->payment_status }}</p>
        </div>

        <div class="row border border-2 border-dark p-2 mb-2 rounded-3">
            <p class="m-0"><strong>Bukti Pembayaran : </strong></p>

            <img class="" src="{{ asset('storage/bukti-pembayaran/'.$antrian->payment->payment_proof) }}" alt="Bukti Pembayaran" style="width: 30%">
        </div>
        <div class="row p-3">
            <p class="text-muted text-center fst-italic">*Form ini dibuat otomatis oleh sistem. Untuk pertanyaan dapat menghubungi Sales/Admin Workshop</p>
        </div>
    </div>
</body>
</html>
