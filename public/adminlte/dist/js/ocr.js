/**
 * Modul OCR untuk memproses bukti pembayaran.
 * Menggunakan Tesseract.js untuk mengenali teks dari gambar.
 * Pastikan file yang diunggah merupakan file gambar yang valid.
 */
$(document).ready(function () {
    const validAccounts = [
      'tri martha herawati',
      'tri martha',
      'tri',
      'martha',
      'herawati',
      '2580489209',
      '2582724769',
      '313701004062501',
      '1638585329',
      '1420016842584',
      '7294205211'
    ];

    $('#hargaProduk, #jumlahPembayaran, #biayaPemasangan, #biayaPengemasan, #biayaPengiriman').on('keyup', function(){
        calculateTotalHarga();
    });

    $('#qty').on('change', function(){
        calculateTotalHarga();
    });

    function removeDot(value){
        return parseInt(value.replace(/\./g, ''));
    }

    function formatRupiah(value){
        return value.toLocaleString('id-ID', { minimumFractionDigits: 0 });
    }

    function calculateTotalHarga(){
        var hargaProduk = removeDot($('#hargaProduk').val());
        var qty = removeDot($('#qty').val());
        var jumlahPembayaran = removeDot($('#jumlahPembayaran').val());
        var biayaPemasangan = removeDot($('#biayaPemasangan').val());
        var biayaPengemasan = removeDot($('#biayaPengemasan').val());
        var biayaPengiriman = removeDot($('#biayaPengiriman').val());

        if(isNaN(hargaProduk)){
            hargaProduk = 0;
        }
        if(isNaN(qty)){
            qty = 0;
        }
        if(isNaN(jumlahPembayaran)){
            jumlahPembayaran = 0;
        }
        if(isNaN(biayaPemasangan)){
            biayaPemasangan = 0;
        }
        if(isNaN(biayaPengemasan)){
            biayaPengemasan = 0;
        }
        if(isNaN(biayaPengiriman)){
            biayaPengiriman = 0;
        }

        var total = hargaProduk * qty;
        var subtotal = total + biayaPemasangan + biayaPengemasan + biayaPengiriman;
        var omset = total + biayaPemasangan + biayaPengemasan;
        var sisaTagihan = subtotal - jumlahPembayaran;

        if(sisaTagihan == 0){
            $('#statusPembayaran').val('Lunas');
        }else if(sisaTagihan == subtotal){
            $('#statusPembayaran').val('Belum Bayar');
        }else{
            $('#statusPembayaran').val('DP');
        }

        $('#totalOmset').html(formatRupiah(omset));
        $('#totalOmsetInput').val(omset);

        $('#sisaTagihan').val(sisaTagihan < 0 ? 0 : formatRupiah(sisaTagihan));

        $('#subtotal').val(formatRupiah(subtotal));
    }

    // Mendefinisikan fungsi untuk validasi tipe file yang diperbolehkan
    function isValidImageFile(file) {
        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/jpg',
            'image/webp',
            'image/heic',
            'image/heif',
            'application/pdf'
        ];
        return allowedTypes.includes(file.type);
    }

    // Event handler untuk perubahan pada dropdown metode pembayaran
    $("#jenisPembayaran").on('change', function () {
        // Ambil nilai terbaru dari elemen #jenisPembayaran
        var metodePembayaran = $(this).val();

        // Cek jika nilai metodePembayaran kosong atau tidak ada
        if (!metodePembayaran || metodePembayaran === "") {
            $("#buktiPembayaran").prop('disabled', true);
        }
        // Cek jika metode pembayaran mengandung kata "transfer"
        else if (metodePembayaran.toLowerCase().includes("transfer")) {
            $("#buktiPembayaran").prop('disabled', false);
        }
        else {
            $("#buktiPembayaran").prop('disabled', false);
        }
        // Set nilai jumlah pembayaran menjadi 0 setiap kali terjadi perubahan
        $("#jumlahPembayaran").val(0);
    });

    // Event ketika file bukti pembayaran diunggah
    $("#buktiPembayaran").change(function (event) {
        const files = event.target.files;

        if (files.length > 0) {
            const file = files[0];
            // Validasi tipe file
            if (!isValidImageFile(file)) {
                alert("Format file tidak valid. Harap unggah file gambar (jpg, png, gif, webp, heic, heif, pdf).");
                $(this).val(''); // Reset file input jika tidak valid
                return;
            }

            Swal.fire({
                title: 'Memproses Bukti Pembayaran...',
                text: 'Mohon tunggu sampai proses selesai.',
                allowOutsideClick: false,
                showConfirmButton: false,
                showCancelButton: false,
            });

            // Proses OCR
            Tesseract.recognize(
                file,
                'eng+ind',
            ).then(({ data: { text } }) => {
                const result = processOCR(text);
                Swal.close();
            }).catch(error => {
                console.error("OCR Error:", error);
                document.getElementById("result").innerText = "Gagal memproses gambar!";
            });
        }
    });

    /**
     * Event handler untuk mengubah pratinjau file bukti pembayaran.
     * Menggunakan FileReader untuk menampilkan pratinjau file (gambar atau PDF) di dalam modal.
     */
    $('#buktiPembayaran').on('change', function(event) {
      var file = event.target.files[0];
      if (file) {
          // Aktifkan tombol "Lihat File" jika file terpilih
          $('#btnLihatBukti').prop('disabled', false);
          var reader = new FileReader();
          reader.onload = function(e) {
              var fileUrl = e.target.result;
              // Periksa tipe file dan tampilkan pratinjau sesuai dengan tipe file
              if (file.type === 'application/pdf') {
                  $('#filePreviewContainer').html('<object data="'+ fileUrl +'" type="application/pdf" width="100%" height="500px">Pratinjau PDF tidak tersedia.</object>');
              } else if (file.type.startsWith('image/')) {
                  $('#filePreviewContainer').html('<img src="'+ fileUrl +'" alt="Pratinjau Bukti Pembayaran" class="img-fluid"/>');
              } else {
                  $('#filePreviewContainer').html('<p>Pratinjau tidak tersedia untuk jenis file ini.</p>');
              }
          };
          reader.readAsDataURL(file);
      } else {
          $('#btnLihatBukti').prop('disabled', true);
          $('#filePreviewContainer').empty();
      }
    });

  // Event handler untuk tombol "Lihat File" agar menampilkan modal pratinjau
  $('#btnLihatBukti').on('click', function() {
      $('#filePreviewModal').modal('show');
  });

    /**
     * Fungsi untuk memproses teks OCR dengan ekstraksi nominal dan validasi transfer.
     * @param {string} text - Teks hasil OCR.
     * @returns {Object} Objek yang berisi properti 'nominal' (string) dan 'valid' (boolean).
     */
    function processOCR(text) {
        // Ekstrak nominal menggunakan regex untuk menangkap angka dengan atau tanpa "Rp"
        const nominalMatch = text.match(/(?:Rp\s?)?(\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{2})?)/g);
        let nominalText = "Nominal tidak ditemukan!";

        if (nominalMatch) {
            // Bersihkan dan validasi setiap nominal yang ditemukan
            const validNominal = nominalMatch.map(num => {
                // Hapus "Rp" jika ada
                let cleanedNum = num.replace(/Rp\s?/g, '');
                // Hapus tanda pemisah ribuan (titik/koma) jika ada
                cleanedNum = cleanedNum.replace(/[.,](?=\d{3})/g, '');
                // Hapus desimal (contoh: 216900.00 → 216900)
                cleanedNum = cleanedNum.replace(/(\.\d{2}|\.\d{1}|,\d{2}|,\d{1})$/, '');
                return parseInt(cleanedNum) > 999 ? cleanedNum : null;
            }).filter(Boolean);

            if (validNominal.length > 0) {
                nominalText = "Nominal: Rp " + validNominal[0];
                $("#jumlahPembayaran").val(parseInt(validNominal[0]).toLocaleString('id-ID', { minimumFractionDigits: 0 }));
            }
        }

        // Validasi transfer berdasarkan kehadiran salah satu kata kunci dari validAccounts
        const isValidTransfer = validAccounts.some(keyword => text.toLowerCase().includes(keyword));

        // Perbarui tampilan tombol verifikasi berdasarkan hasil validasi
        if(isValidTransfer){
            $("#infoVerified").removeClass("text-muted text-danger").addClass("text-success").html("<i class='fa fa-check'></i> Bukti Pembayaran Valid");
        }else{
            $("#infoVerified").removeClass("text-muted text-success").addClass("text-danger").html("<i class='fa fa-times'></i> Bukti Pembayaran Tidak Valid, akan dicek oleh Tim Keuangan terlebih dahulu");
        }

        calculateTotalHarga();

        return { nominal: nominalText, valid: isValidTransfer };
    }
});
