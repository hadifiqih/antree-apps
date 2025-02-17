@extends('layouts.app')

@section('title', 'Antrian | CV. Kassab Syariah')

@section('username', Auth::user()->name)

@section('page', 'Tambah Antrian')

@section('breadcrumb', 'Tambah Antrian')

@section('style')
<style>
    .select2-container .select2-selection--single {
        height: 40px !important;
    }
</style>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<div class="container-fluid">
    <form action="{{ route('antrian.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Data Pelanggan</h2>
                </div>
                <div class="card-body">
                    {{-- Tambah Pelanggan Baru --}}
                    <button type="button" class="btn btn-sm btn-primary mb-3" data-toggle="modal" data-target="#exampleModal">
                        Tambah Pelanggan Baru
                    </button>

                    <div class="form-group">
                        <label for="nama">Nama Pelanggan <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="nama" name="nama" style="width: 100%">

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sumberPelanggan">Status Pelanggan</label>
                        <input type="text" class="form-control" id="statusPelanggan" placeholder="Status Pelanggan" value="" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Data Pekerjaan</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="namaPekerjaan">Nama Produk <span class="text-danger">*</span></label>
                        {{-- Nama Pekerjaan Select2 --}}
                        <select class="custom-select rounded-0" id="namaPekerjaan" name="namaPekerjaan" style="width: 100%">
                            <option value="{{ $order->job->id }}" selected>{{ $order->job->job_name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jenisPekerjaan">Jenis Produk <span class="text-danger">*</span></label>
                        <select class="custom-select rounded-0" id="jenisPekerjaan" name="jenisPekerjaan">
                            <option value="{{ $order->job->id }}" selected>{{ $order->job->job_type }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        {{-- File Purchase Order --}}
                        <label for="filePO">File Purchase Order <span class="text-sm text-muted font-italic">(Opsional)</span></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="filePO" name="filePO">
                                <label class="custom-file-label" for="filePO">Pilih File</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan Spesifikasi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keterangan" rows="5" placeholder="Keterangan" name="keterangan">
                            {{ old('keterangan') }}
                        </textarea>
                    </div>
                    <div class="form-group">
                        {{-- Platform Iklan with input select2 --}}
                        <label for="platformIklan">Platform Iklan <span class="text-danger">*</span></label>
                        <select class="custom-select rounded-0" id="platformIklan" name="platformIklan" required>
                            <option value="" disabled selected>-- Pilih Platform Iklan --</option>
                            <option value="0">Tidak dari Iklan Berbayar</option>
                            @foreach ($platforms as $platform)
                                <option value="{{ $platform->id }}" {{ $platform->id == old('platformIklan') ? 'selected' : '' }}>{{ $platform->platform_name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted font-italic">*Jika Organik(Postingan Marol), pilih "Tidak dari Iklan Berbayar"</small>
                    </div>
              </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Data Pembayaran</h2>
                </div>
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="hargaProduk">Harga Produk <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" class="form-control rupiah" placeholder="Harga Produk" id="hargaProduk" name="hargaProduk" value="{{ old('hargaProduk') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="qty">Qty <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="qty" placeholder="Masukkan jumlah / qty produk" name="qty" value="{{ old('qty') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="jenisPembayaran">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="custom-select rounded-0" id="jenisPembayaran" name="jenisPembayaran">
                                <option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
                                @foreach([
                                        'Cash' => 'Tunai', 
                                        'Transfer BCA' => 'Transfer BCA', 
                                        'Transfer BNI' => 'Transfer BNI', 
                                        'Transfer BRI' => 'Transfer BRI', 
                                        'Transfer Mandiri' => 'Transfer Mandiri', 
                                        'Transfer BSI' => 'Transfer BSI', 
                                        'Saldo Tokopedia' => 'Marketplace Tokopedia', 
                                        'Saldo Shopee' => 'Marketplace Shopee', 
                                        'Saldo Bukalapak' => 'Marketplace Bukalapak', 
                                        'Bayar Waktu Ambil' => 'Bayar Waktu Ambil'
                                    ] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="formBuktiBayar" class="form-group mb-3">
                        {{-- Bukti Pembayaran / Transfer --}}
                        <label for="buktiPembayaran">Bukti Pembayaran <span class="text-danger">*</span></label>
                        <div class="d-flex flex-row">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="buktiPembayaran" name="buktiPembayaran" disabled>
                                    <label class="custom-file-label" for="buktiPembayaran">Unggah Bukti Pembayaran</label>
                                </div>
                            </div>
                            <!-- Tombol untuk melihat pratinjau file yang diunggah sementara -->
                            <button class="btn btn-info ml-2" id="btnLihatBukti" type="button" disabled>
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <small id="infoVerified" class="text-muted font-italic">Pilih metode pembayaran terlebih dahulu</small>
                    </div>

                    {{-- Jumlah Pembayaran Pelanggan --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jumlahPembayaran">Jumlah Pembayaran Pelanggan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" class="form-control rupiah" placeholder="Jumlah Pembayaran Pelanggan" value="0" id="jumlahPembayaran" name="jumlahPembayaran" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="statusPembayaran">Status Pembayaran <span class="text-danger">*</span></label>
                            <select class="custom-select rounded-0" id="statusPembayaran" name="statusPembayaran">
                                <option value="" disabled selected>-- Pilih Status Pembayaran --</option>
                                <option value="DP">DP</option>
                                <option value="Lunas">Lunas</option>
                                <option value="Belum Bayar">Belum Bayar</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                {{-- Biaya Jasa Pengiriman --}}
                                <label for="biayaPengiriman">Biaya Jasa Pengiriman <span class="text-sm text-muted font-italic">(Opsional)</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control rupiah" id="biayaPengiriman" placeholder="Biaya Jasa Pengiriman" name="biayaPengiriman">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                {{-- Biaya Jasa Pemasangan --}}
                                <label for="biayaPemasangan">Biaya Jasa Pemasangan <span class="text-sm text-muted font-italic">(Opsional)</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control rupiah" id="biayaPemasangan" placeholder="Biaya Jasa Pemasangan" name="biayaPemasangan">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                {{-- Biaya jasa pengemasan --}}
                                <label for="biayaPengemasan">Biaya Jasa Packing <span class="text-sm text-muted font-italic">(Opsional)</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control rupiah" id="biayaPengemasan" placeholder="Biaya Jasa Packing" name="biayaPengemasan">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {{-- Alamat Pengiriman --}}
                        <label for="alamatPengiriman">Alamat Pengiriman / Pemasangan <span class="text-sm text-muted font-italic">(Opsional)</span></label>
                        <textarea rows="3" class="form-control" id="alamatPengiriman" placeholder="Alamat Pengiriman / Pemasangan" name="alamatPengiriman"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            {{-- Jumlah Pembayaran Pelanggan --}}
                            <label for="totalOmset">Subtotal <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" class="form-control rupiah" placeholder="Subtotal" id="subtotal" name="subtotal" required disabled>
                            </div>
                        </div>
                        <div class="col-6">
                            {{-- Tampilkan sisa pembayaran jika status pembayaran = DP, tampilkan Lunas jika status pembayaran Lunas --}}
                            <label for="sisaTagihan">Sisa Tagihan <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" class="form-control rupiah" placeholder="Sisa Tagihan" id="sisaTagihan" name="sisaTagihan" required disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label for="totalOmset">Total Omset <span class="text-danger">*</span></label>
                            <h3 class="h3 text-success font-weight-bold" id="totalOmset">Rp 0</h3>
                            <input type="hidden" id="totalOmsetInput" name="totalOmset">
                        </div>
                    </div>

                </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Data Desain</h2>
                </div>
                <div class="card-body">
                    <input type="hidden" name="idOrder" value="{{ $order->id }}">
                    <div class="form-group">
                        <label for="namaDesain">Nama Desain <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="namaDesain" placeholder="Nama Desain" name="namaDesain" value="{{ $order->title }}" readonly>
                    </div>
                    <div class="form-group">
                        {{-- Desainer --}}
                        <label for="desainer">Desainer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="desainer" placeholder="Nama Desainer" name="desainer" value="{{ $order->employee->name }}" readonly>
                    </div>
                    <div class="form-group">
                        {{-- File Desain --}}
                        <h6><strong>File Cetak</strong></h6>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="fileDesain" name="fileDesain" value="{{ $order->file_cetak }}" disabled>
                                <label class="custom-file-label" for="fileDesain">{{ $order->file_cetak }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {{-- File Desain --}}
                        <h6><strong>Upload Gambar ACC <span class="text-danger">*</span></strong></h6>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="accDesain" name="accDesain" required>
                                <label class="custom-file-label" for="accDesain">Unggah Gambar</label>
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" id="btnPreviewAcc" disabled>
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      {{-- Tombol Submit Antrikan --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-right">
                        <input type="hidden" name="sales_id" value="{{ $order->sales_id }}" required>
                        <input type="hidden" name="ticket_order" value="{{ $order->ticket_order }}" required>
                        {{-- Tombol Submit --}}
                        <div class="d-flex align-items-center">
                            <button id="submitToAntrian" type="submit" class="btn btn-primary">Submit<div id="loader" class="loader" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Pelanggan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <form id="pelanggan-form" method="POST">
                    @csrf
                    <input type="hidden" name="sales_id" value="{{ Auth::user()->sales->id }}">
                    <div class="form-group">
                        <label for="nama">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modalNama" placeholder="Nama Pelanggan" name="modalNama" required>
                    </div>

                    <div class="form-group">
                        <label for="noHp">No. HP / WA <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="modalTelepon" placeholder="Nomor Telepon" name="modalTelepon" required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modalAlamat" placeholder="Alamat Pelanggan" name="modalAlamat" required>
                    </div>
                    <div class="form-group">
                        <label for="instansi">Instansi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modalInstansi" placeholder="Instansi Pelanggan" name="modalInstansi" required>
                        <p class="text-muted mt-2">*Jika tidak tau, beri tanda "-"</p>
                    </div>
                    <div class="form-group">
                        <label for="infoPelanggan">Sumber Pelanggan <span class="text-danger">*</span></label>
                        <select class="custom-select rounded-0" id="infoPelanggan" name="modalInfoPelanggan" required>
                            <option value="default" selected>Pilih Sumber Pelanggan</option>
                            <option value="Facebook">Facebook</option>
                            <option value="Tokopedia">Tokopedia</option>
                            <option value="Shopee">Shopee</option>
                            <option value="Instagram">Instagram</option>
                            <option value="Tiktok">Tiktok</option>
                            <option value="Youtube">Youtube</option>
                            <option value="Teman/Keluarga/Kerabat">Teman/Keluarga/Kerabat</option>
                            <option value="Iklan Facebook">Iklan Facebook</option>
                            <option value="Iklan Meta">Iklan Meta</option>
                            <option value="Iklan Instagram">Iklan Instagram</option>
                            <option value="Iklan Google">Iklan Google (dari Google/Google Maps)</option>
                            <option value="Iklan Tiktok">Iklan Tiktok</option>
                            <option value="Iklan Shopee">Iklan Shopee</option>
                            <option value="Iklan Tokopedia">Iklan Tokopedia</option>
                            <option value="Salescall">Salescall</option>
                            <option value="Visit">Visit / Kunjungan</option>
                            <option value="Follow Up">Follow Up</option>
                            <option value="RO WA">RO WhatsApp</option>
                            <option value="Broadcast">Broadcast</option>
                            <option value="Event">Event</option>
                            <option value="Toko Pasar Kembang">Toko Pasar Kembang</option>
                        </select>
                    </div>
		    <div class="form-group">
                <label for="provinsi">Provinsi<sup class="text-danger">*</sup></label>
                <select name="provinsi_id" id="provinsi" style="width: 100%" required>
                    <option value="" disabled selected>Pilih Provinsi</option>
                </select>
            </div>
            <div class="form-group" id="groupKota" style="display: none">
                <label for="kota">Kabupaten/Kota<sup class="text-danger">*</sup></label>
                <select name="kota_id" id="kota" style="width: 100%" required>
                    <option value="" disabled selected>Pilih Kota</option>
                </select>
            </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="submit" class="btn btn-primary" id="submitPelanggan" value="Tambah"><span id="loader" class="loader" style="display: none;"></span>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Pratinjau Bukti Pembayaran -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filePreviewModalLabel">Pratinjau Bukti Pembayaran</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div id="filePreviewContainer">
            <!-- Konten pratinjau file akan ditampilkan di sini -->
         </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal untuk Pratinjau ACC Desain -->
<div class="modal fade" id="accPreviewModal" tabindex="-1" aria-labelledby="accPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accPreviewModalLabel">Pratinjau Gambar ACC</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div id="accPreviewContainer" class="text-center">
            <!-- Preview gambar akan dimuat di sini -->
         </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4.0.2/dist/tesseract.min.js"></script>
<script src="{{ asset('adminlte/dist/js/maskMoney.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/ocr.js') }}"></script>
<script>
    $(document).ready(function(){
        $('.rupiah').on('keyup', function () {
            // Ambil nilai input dan buang karakter selain angka
            const inputValue = $(this).val();
            const numericString = inputValue.replace(/[^0-9]/g, '');

            // Konversi string menjadi angka; jika gagal, gunakan 0
            const numberValue = parseInt(numericString, 10) || 0;

            // Format angka ke format rupiah (IDR) dengan tanpa desimal
            const formattedValue = numberValue.toLocaleString('id-ID', {
                minimumFractionDigits: 0
            });

            // Set nilai input dengan hasil format rupiah
            $(this).val(formattedValue);
        });

        //function provinsi dengan select2
        $('#provinsi').select2({
            dropdownParent: $('#provinsi').parent(),
            placeholder: 'Pilih Provinsi',
            ajax: {
                url: "{{ route('getProvinsi') }}",
                dataType: 'json',
                delay: 250,
                data: function(params){
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        // function kota
        $('#provinsi').on('change', function(){
            var provinsi = $(this).val();
            $('#groupKota').show();
            $('#kota').val(null).trigger('change').empty().append(`<option value="" selected disabled>Pilih Kota</option>`);
            $('#kota').select2({
                dropdownParent: $('#kota').parent(),
                placeholder: 'Pilih Kota',
                ajax: {
                    url: "{{ route('getKota') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params){
                        return {
                            search: params.term,
                            provinsi_id: provinsi
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });
        });

        $('#nama').select2({
            placeholder: 'Pilih Pelanggan',
            ajax:{
                url:"{{ route('pelanggan.search') }}",
                processResults: function(data){
                    $('#alamat').val('');
                    $('#noHp').val('');
                    return{
                        results: $.map(data, function(item){
                            var status = $('#statusPelanggan').val(item.frekuensi_order);
                            if(status >= '0'){
                                $('#statusPelanggan').val('Pelanggan Baru');
                            }else if(status >= '1'){
                                $('#statusPelanggan').val('Pernah Order');
                            }else if(status >= '2'){
                                $('#statusPelanggan').val('Repeat Order');
                            }
                            return{
                                id: item.id,
                                text: item.nama+ ' ' + '-' + ' ' +item.telepon,
                            }
                        })
                    }
                },
                cache: true
            }
        });

        $('#pelanggan-form').on('submit', function(e){
        e.preventDefault();

        $(this).find('#submitPelanggan').prop('disabled', true);
        $(this).find('#loader').show();

        const formData = $(this).serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:"{{ route('pelanggan.store') }}",
            type:"POST",
            data: formData,
            success:function(response){
                if(response.success){
                    $('#exampleModal').modal('hide');
                    //Mengosongkan Form pada Modal
                    $('#modalTelepon').val('');
                    $('#modalNama').val('');
                    $('#modalAlamat').val('');
                    $('#modalInstansi').val('');
                    $('#infoPelanggan').val('default');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data Pelanggan Berhasil Ditambahkan',
                        timer: 2500
                    });
                    setInterval(() => {
                        location.reload();
                    }, 2500);
                }
                }
            });
        });

        $('#submitToAntrian').on('submit', function(e){
            // MemasTikan agar form tidak di-submit secara langsung
            e.preventDefault();
            // Lakukan validasi di sini
            var error = false;
            // Validasi untuk nama pelanggan
            if ($('#nama').val() == '') {
                error = true;
                alert('Nama pelanggan harus diisi');
            }
            // Validasi untuk status pembayaran
            if ($('#statusPembayaran').val() == '') {
                error = true;
                alert('Status pembayaran harus dipilih');
            }
            // Validasi untuk jenis pembayaran
            if ($('#jenisPembayaran').val() == '') {
                error = true;
                alert('Jenis pembayaran harus dipilih');
            }
            // Validasi untuk jumlah pembayaran pelanggan
            if ($('#jumlahPembayaran').val() == '') {
                error = true;
                alert('Jumlah pembayaran pelanggan harus diisi');
            }
            // Validasi untuk harga produk
            if ($('#hargaProduk').val() == '') {
                error = true;
                alert('Harga produk harus diisi');
            }
            // Validasi untuk qty
            if ($('#qty').val() == '') {
                error = true;
                alert('Qty harus diisi');
            }
            //Validasi untuk keterangan
            if ($('#keterangan').val() == '') {
                error = true;
                alert('Keterangan / Spesifikasi harus diisi');
            }
            // Validasi untuk bukti pembayaran
            if ($('#buktiPembayaran').get(0).files.length === 0) {
                error = true;
                alert('Bukti pembayaran harus diunggah');
            }
            // Validasi untuk upload gambar ACC
            if ($('#accDesain').get(0).files.length === 0) {
                error = true;
                alert('Gambar ACC harus diunggah');
            }
            // Jika tidak ada kesalahan, submit form
            if (!error) {
                $(this).find('#submitToAntrian').prop('disabled', true);
                $('#loader').show();
                $('#formAntrian').submit();
            }
        });

            // Event listener: Cek perubahan file ACC Desain
        $('#accDesain').on('change', function(){
            var file = this.files[0];
            // Jika file dipilih dan tipe file adalah gambar
            if (file && file.type.startsWith('image/')) {
                $('#btnPreviewAcc').prop('disabled', false);
            } else {
                $('#btnPreviewAcc').prop('disabled', true);
            }
        });

        // Event listener: Preview gambar ACC saat tombol preview ditekan
        $('#btnPreviewAcc').on('click', function(){
            var file = $('#accDesain')[0].files[0];
            // Validasi kembali file yang dipilih
            if (file && file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e){
                    // Masukkan konten gambar ke dalam container preview
                    $('#accPreviewContainer').html('<img src="'+e.target.result+'" class="img-fluid" alt="Preview ACC Desain" />');
                    // Tampilkan modal preview
                    $('#accPreviewModal').modal('show');
                };
                reader.readAsDataURL(file);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silahkan pilih file gambar yang valid untuk ACC Desain!'
                });
            }
        });
    });
</script>
@endsection
