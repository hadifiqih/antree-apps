<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Order;
use App\Models\Sales;
use App\Models\Design;
use App\Models\Antrian;
use App\Models\Machine;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Anservice;
use App\Models\Dokumproses;
use App\Models\SearchLog;
use Illuminate\Http\Request;
use App\Models\Documentation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AntrianWorkshop;
use App\Http\Resources\AntrianResource;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AntrianDiantrikan;
use Illuminate\Support\Facades\Notification;


class AntrianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function estimatorProduksi(string $id)
    {
        $antrian = Antrian::with(['payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing'])->where('ticket_order', $id)->first();
        return view('page.antrian-workshop.estimator-produksi', compact('antrian'));
    }

    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $salesAll = Sales::all();
        $filtered = [];

        // Mengecek apakah terdapat parameter filter (kategori dan/atau sales) pada request
        $kategori   = $request->input('kategori');
        $salesValue = $request->input('sales');

        if ($kategori || $salesValue) {
            // Jika terdapat filter, gunakan kedua query (aktif dan selesai) dengan filter yang diterapkan

            // Query untuk antrian aktif (status = 1)
            $activeQuery = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
                ->where('status', '1')
                ->whereBetween('created_at', [now()->subMonth(3), now()]);

            // Query untuk antrian selesai (status = 2)
            $finishedQuery = Antrian::with('sales', 'customer', 'job', 'design', 'operator', 'finishing', 'order', 'documentation')
                ->where('status', '2')
                ->whereBetween('created_at', [now()->subMonth(3), now()]);

            // Terapkan filter berdasarkan kategori (job_type) jika tidak "Semua" dan tidak kosong
            if ($kategori && $kategori !== '') {
                $activeQuery->whereHas('job', function($query) use ($kategori) {
                    $query->where('job_type', $kategori);
                });
                $finishedQuery->whereHas('job', function($query) use ($kategori) {
                    $query->where('job_type', $kategori);
                });
            }

            // Terapkan filter berdasarkan sales_id jika tidak "Semua" dan tidak kosong
            if ($salesValue && $salesValue !== '') {
                $activeQuery->where('sales_id', $salesValue);
                $finishedQuery->where('sales_id', $salesValue);
            }

            // Eksekusi query untuk mendapatkan data antrian aktif dan selesai
            $antrians = $activeQuery->orderByDesc('created_at')->get();
            $antrianSelesai = $finishedQuery->orderByDesc('created_at')->get();

            // Menyimpan data filter agar form tetap menampilkan pilihan yang telah diseleksi
            $filtered = [
                'kategori' => $kategori,
                'sales'    => $salesValue,
            ];
        } else {
            // Jika tidak ada filter, gunakan logika default berdasarkan role
            switch ($role) {
                case 'sales':
                    $sales    = Sales::where('user_id', auth()->user()->id)->first();
                    $salesId  = $sales->id;
                    $antrians = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
                        ->where('status', '1')
                        ->where('sales_id', $salesId)
                        ->orderByDesc('created_at')
                        ->get();
                    $antrianSelesai = Antrian::with('sales', 'customer', 'job', 'design', 'operator', 'finishing', 'order', 'documentation')
                        ->where('status', '2')
                        ->where('sales_id', $salesId)
                        ->orderByDesc('created_at')
                        ->whereBetween('created_at', [now()->subMonth(1), now()])
                        ->get();
                    break;

                case 'admin':
                case 'dokumentasi':
                case 'stempel':
                case 'advertising':
                    $antrians = Antrian::with('payments', 'order', 'order.employee', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
                        ->where('status', '1')
                        ->orderByDesc('created_at')
                        ->limit(100)
                        ->get();
                    $antrianSelesai = Antrian::with('sales', 'customer', 'job', 'design', 'operator', 'finishing', 'order', 'documentation')
                        ->where('status', '2')
                        ->orderByDesc('created_at')
                        ->whereBetween('created_at', [now()->subMonth(1), now()])
                        ->limit(100)
                        ->get();
                    break;

                case 'estimator':
                case 'staffAdmin':
                    $antrians = Antrian::with('payments', 'sales', 'order', 'order.employee' , 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses', 'documentation')
                        ->where('status', '1')
                        ->orderByDesc('created_at')
                        ->whereBetween('created_at', [now()->subMonth(1), now()])
                        ->get();
                    $antrianSelesai = Antrian::with('payments', 'sales', 'order', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses', 'documentation')
                        ->where('status', '2')
                        ->orderByDesc('created_at')
                        ->whereBetween('created_at', [now()->subMonth(1), now()])
                        ->get();
                    break;

                default:
                    $antrians = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
                        ->where('status', '1')
                        ->orderByDesc('created_at')
                        ->whereBetween('created_at', [now()->subMonth(1), now()])
                        ->get();
                    $antrianSelesai = Antrian::with('sales', 'customer', 'job', 'design', 'operator', 'finishing', 'order', 'documentation')
                        ->where('status', '2')
                        ->orderByDesc('created_at')
                        ->whereBetween('created_at', [now()->subMonth(3), now()])
                        ->get();
                    break;
            }
        }

        // Kumpulkan semua operator_id, finisher_id, dan qc_id dari setiap antrian untuk menghindari query berulang
        $operatorIds = [];
        $finisherIds = [];
        $qcIds = [];

        // Gabungkan antrian aktif dan selesai agar seluruh data ter-cover
        $allAntrians = $antrians;
        if (isset($antrianSelesai)) {
            $allAntrians = $allAntrians->merge($antrianSelesai);
        }

        foreach ($allAntrians as $antrian) {
            if (!empty($antrian->operator_id)) {
                $ids = explode(',', $antrian->operator_id);
                foreach ($ids as $id) {
                    $id = trim($id);
                    if ($id !== 'rekanan' && is_numeric($id)) {
                        $operatorIds[] = (int)$id;
                    }
                }
            }
            if (!empty($antrian->finisher_id)) {
                $ids = explode(',', $antrian->finisher_id);
                foreach ($ids as $id) {
                    $id = trim($id);
                    if ($id !== 'rekanan' && is_numeric($id)) {
                        $finisherIds[] = (int)$id;
                    }
                }
            }
            if (!empty($antrian->qc_id)) {
                $ids = explode(',', $antrian->qc_id);
                foreach ($ids as $id) {
                    $id = trim($id);
                    if ($id !== 'rekanan' && is_numeric($id)) {
                        $qcIds[] = (int)$id;
                    }
                }
            }
        }

        $operatorIds = array_unique($operatorIds);
        $finisherIds = array_unique($finisherIds);
        $qcIds = array_unique($qcIds);

        $operators = Employee::whereIn('id', $operatorIds)->get()->keyBy('id');
        $finishers = Employee::whereIn('id', $finisherIds)->get()->keyBy('id');
        $qcs = Employee::whereIn('id', $qcIds)->get()->keyBy('id');

        return view('page.antrian-workshop.index', compact('antrians', 'antrianSelesai', 'operators', 'finishers', 'qcs', 'salesAll', 'filtered'));
    }

    public function searchByTicket()
    {
        return view('page.antrian-workshop.search-by-ticket');
    }

    public function resultSearchByTicket(Request $request)
    {
        $ticket = $request->input('ticket');
        // Simpan data pencarian
        SearchLog::create([
            'user_id' => auth()->user()->id,
            'ticket_order' => $ticket,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $antrian = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')->where('ticket_order', $ticket)->first();
        return view('page.antrian-workshop.estimator-produksi', compact('antrian'));
    }

    //--------------------------------------------------------------------------
    //Estimator
    //--------------------------------------------------------------------------

    public function estimatorIndex()
    {
        $fileBaruMasuk = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
        ->where('status', '1')
        ->where('is_aman', '0')
        ->orderByDesc('created_at')
        ->get();

        $progressProduksi = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses')
        ->where('status', '1')
        ->orderByDesc('created_at')
        ->get();

        $selesaiProduksi = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses')
        ->where('status', '2')
        ->orderByDesc('created_at')
        ->get();

        return view('page.antrian-workshop.estimator-index', compact('fileBaruMasuk', 'progressProduksi', 'selesaiProduksi'));
    }

    public function estimatorFilter(Request $request)
    {
        $jobType = $request->input('kategori');
        $filtered = $jobType;

        $fileBaruMasuk = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing')
        ->whereHas('job', function ($query) use ($jobType) {
            $query->where('job_type', $jobType);
        })
        ->where('status', '1')
        ->orderByDesc('created_at')
        ->get();

        $progressProduksi = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses')
        ->whereHas('job', function ($query) use ($jobType) {
            $query->where('job_type', $jobType);
        })
        ->where('status', '1')
        ->orderByDesc('created_at')
        ->get();

        $selesaiProduksi = Antrian::with('payments', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'dokumproses')
        ->whereHas('job', function ($query) use ($jobType) {
            $query->where('job_type', $jobType);
        })
        ->where('status', '2')
        ->orderByDesc('created_at')
        ->get();

        return view('page.antrian-workshop.estimator-index', compact('fileBaruMasuk', 'progressProduksi', 'selesaiProduksi', 'filtered'));
    }

    public function downloadPrintFile($id){
        $antrian = Antrian::where('id', $id)->first();
        $file = $antrian->order->file_cetak;
        $path = storage_path('app/public/file-cetak/' . $file);
        return response()->download($path);
    }

    public function downloadProduksiFile($id){
        $antrian = Antrian::where('id', $id)->first();
        $file = $antrian->design->filename;
        $path = storage_path('app/public/file-jadi/' . $file);
        return response()->download($path);
    }

    public function store(Request $request)
    {        
        try {
            DB::beginTransaction();
            
            // Validate all inputs first
            $validated = $request->validate([
                'nama' => 'required|exists:customers,id',
                'namaPekerjaan' => 'required|exists:jobs,id', 
                'jenisPekerjaan' => 'required',
                'keterangan' => 'required|string',
                'platformIklan' => 'required|exists:platforms,id',
                'hargaProduk' => 'required|string',
                'qty' => 'required|integer|min:1',
                'jenisPembayaran' => 'required|string',
                'jumlahPembayaran' => 'required|string',
                'statusPembayaran' => 'required|in:DP,Lunas,Belum Bayar',
                'biayaPengiriman' => 'nullable|string',
                'biayaPemasangan' => 'nullable|string',
                'biayaPengemasan' => 'nullable|string', 
                'alamatPengiriman' => 'nullable|string',
                'totalOmset' => 'required',
                'idOrder' => 'required|exists:orders,id',
                'namaDesain' => 'required|string',
                'desainer' => 'required|string',
                'sales_id' => 'required|exists:sales,id',
                'ticket_order' => 'required|unique:antrians,ticket_order',
                'accDesain' => 'required|file|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf|max:10240',
                'buktiPembayaran' => 'required_if:jenisPembayaran,Transfer BCA,Transfer BNI,Transfer BRI,Transfer Mandiri,Transfer BSI|file|mimes:jpeg,png,jpg,gif,webp,heic,heif,pdf|max:10240',
                'filePO' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
            ]);

            // Check if order exists and is not already in queue
            $order = Order::findOrFail($validated['idOrder']);
            $ticketOrder = $order->ticket_order;
            
            if(Antrian::where('ticket_order', $ticketOrder)->exists()) {
                throw new \Exception('Data antrian sudah ada!');
            }

            // Get customer
            $customer = Customer::findOrFail($validated['nama']);

            // Handle file uploads with proper error handling
            $uploadedFiles = [];
            
            // Handle bukti pembayaran
            $namaBuktiPembayaran = null;
            if($request->hasFile('buktiPembayaran')) {
                $file = $request->file('buktiPembayaran');
                $namaBuktiPembayaran = time() . '_' . $file->getClientOriginalName();
                $uploadedFiles[] = [
                    'path' => 'bukti-pembayaran/' . $namaBuktiPembayaran,
                    'content' => file_get_contents($file)
                ];
            }

            // Handle PO file
            $namaPurchaseOrder = null; 
            if($request->hasFile('filePO')) {
                $file = $request->file('filePO');
                $namaPurchaseOrder = time() . '_' . $file->getClientOriginalName();
                $uploadedFiles[] = [
                    'path' => 'purchase-order/' . $namaPurchaseOrder,
                    'content' => file_get_contents($file)
                ];
            }

            // Handle ACC design
            $namaAccDesain = null;
            if($request->hasFile('accDesain')) {
                $file = $request->file('accDesain');
                $namaAccDesain = time() . '_' . $file->getClientOriginalName();
                $uploadedFiles[] = [
                    'path' => 'acc-desain/' . $namaAccDesain,
                    'content' => file_get_contents($file)
                ];
            }

            // Format currency inputs
            $formatCurrency = function($value) {
                return (int) str_replace(['Rp ', '.'], '', $value ?: '0');
            };

            $payment = new Payment([
                'ticket_order' => $ticketOrder,
                'total_payment' => $formatCurrency($request->subtotal),
                'payment_amount' => $formatCurrency($request->jumlahPembayaran),
                'shipping_cost' => $formatCurrency($request->biayaPengiriman),
                'installation_cost' => $formatCurrency($request->biayaPemasangan),
                'packing_cost' => $formatCurrency($request->biayaPengemasan),
                'remaining_payment' => $formatCurrency($request->sisaTagihan),
                'payment_method' => $validated['jenisPembayaran'],
                'payment_status' => $validated['statusPembayaran'],
                'payment_proof' => $namaBuktiPembayaran,
                'checked_status' => '0',
                'checked_by' => null
            ]);

            // Save files only after validation passes
            foreach($uploadedFiles as $file) {
                Storage::disk('public')->put($file['path'], $file['content']);
            }

            // Update order
            $order->update([
                'acc_desain' => $namaAccDesain,
                'toWorkshop' => 1
            ]);

            // Calculate omset
            $hargaProduk = $formatCurrency($request->hargaProduk);
            $qty = (int) $validated['qty'];
            $biayaPemasangan = $formatCurrency($request->biayaPemasangan);
            $biayaPengemasan = $formatCurrency($request->biayaPengemasan);
            
            $omset = ($hargaProduk * $qty) + $biayaPemasangan + $biayaPengemasan;

            // Create antrian
            $antrian = Antrian::create([
                'ticket_order' => $ticketOrder,
                'sales_id' => $validated['sales_id'],
                'customer_id' => $customer->id,
                'job_id' => $validated['namaPekerjaan'],
                'note' => $validated['keterangan'],
                'omset' => $omset,
                'qty' => $qty,
                'order_id' => $validated['idOrder'],
                'platform_id' => $validated['platformIklan'],
                'alamat_pengiriman' => $validated['alamatPengiriman'],
                'file_po' => $namaPurchaseOrder,
                'harga_produk' => $hargaProduk,
                'packing_cost' => $biayaPengemasan,
            ]);

            // Save payment
            $payment->save();

            // Update customer order frequency
            $latestAntrian = Antrian::where('customer_id', $antrian->customer_id)
                ->where('id', '!=', $antrian->id)
                ->latest()
                ->first();

            if (!$latestAntrian ||
                $antrian->created_at->format('d-m-Y') != $latestAntrian->created_at->format('d-m-Y') ||
                $customer->frekuensi_order == 0) {
                $customer->increment('frekuensi_order');
            }

            // Notify admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $admin->notify(new AntrianWorkshop($antrian, $order, $payment));
            }

            DB::commit();

            return redirect()->route('antrian.index')
                ->with('success', 'Data antrian berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded files if any error occurs
            if(isset($uploadedFiles)) {
                foreach($uploadedFiles as $file) {
                    Storage::disk('public')->delete($file['path']);
                }
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $antrian = Antrian::where('id', $id)->first();

        $jenis = strtolower($antrian->job->job_type);

        if($jenis == 'non stempel'){
            $operators = User::with('employee')->where('role', 'stempel')->orWhere('role', 'advertising')->orWhere('id', 79)->orWhere('id', 55)->orWhere('id', 44)->get();
        }elseif($jenis == 'digital printing'){
            $operators = 'rekanan';
        }else{
            $operators = User::with('employee')->where('role', 'stempel')->orWhere('role', 'advertising')->orWhere('id', 79)->orWhere('id', 55)->orWhere('id', 44)->get();
        }

        //Melakukan explode pada operator_id, finisher_id, dan qc_id
        $operatorId = explode(',', $antrian->operator_id);
        $finisherId = explode(',', $antrian->finisher_id);
        $qualityId = explode(',', $antrian->qc_id);

        $machines = Machine::get();

        $qualitys = Employee::where('can_qc', 1)->get();

        $tempat = explode(',', $antrian->working_at);

        if($antrian->end_job == null){
            $isEdited = 0;
        }else{
            $isEdited = 1;
        }

        return view('page.antrian-workshop.edit', compact('antrian', 'operatorId', 'finisherId', 'qualityId', 'operators', 'qualitys', 'machines', 'tempat', 'isEdited'));
    }

    public function update(Request $request, $id)
    {
        $antrian = Antrian::find($id);

        //Jika input operator adalah array, lakukan implode lalu simpan ke database
        $operator = implode(',', $request->input('operator'));
        $antrian->operator_id = $operator;

        //Jika input finisher adalah array, lakukan implode lalu simpan ke database
        $finisher = implode(',', $request->input('finisher'));
        $antrian->finisher_id = $finisher;

        //Jika input quality adalah array, lakukan implode lalu simpan ke database
        $quality = implode(',', $request->input('quality'));
        $antrian->qc_id = $quality;

        //Jika input tempat adalah array, lakukan implode lalu simpan ke database
        $tempat = implode(',', $request->input('tempat'));
        $antrian->working_at = $tempat;

        //start_job diisi dengan waktu sekarang
        $antrian->start_job = $request->input('start_job');
        $antrian->end_job = $request->input('deadline');

        //Jika input mesin adalah array, lakukan implode lalu simpan ke database
        if($request->input('jenisMesin')){
        $mesin = implode(',', $request->input('jenisMesin'));
        $antrian->machine_code = $mesin;
        }
        $antrian->admin_note = $request->input('catatan');
        $antrian->save();

        return redirect()->route('antrian.index')->with('success-update', 'Data antrian berhasil diupdate!');
    }

    public function updateDeadline(Request $request)
    {
        $antrian = Antrian::find($request->id);
        if (now() > $antrian->end_job) {
            $status = 2;
        }
        $antrian->deadline_status = $status;
        $antrian->save();

        return response()->json(['message' => 'Success'], 200);
    }
    public function destroy($id)
    {
        // Melakukan pengecekan otorisasi sebelum menghapus antrian
        $this->authorize('delete', Antrian::class);

        $antrian = Antrian::find($id);

        $order = Order::where('id', $antrian->order_id)->first();
        $order->toWorkshop = 0;
        $order->save();

        if ($antrian) {

            $antrian->delete();
            return redirect()->route('antrian.index')->with('success-delete', 'Data antrian berhasil dihapus!');
        } else {
            return redirect()->route('antrian.index')->with('error-delete', 'Data antrian gagal dihapus!');
        }
    }
//--------------------------------------------------------------------------

//fungsi untuk menggunggah & menyimpan file gambar dokumentasi
    public function showDokumentasi($id){
        $antrian = Antrian::find($id);
        return view ('page.antrian-workshop.dokumentasi' , compact('antrian'));
    }

    public function storeDokumentasi(Request $request){
        $files = $request->file('files');
        $id = $request->input('idAntrian');

        foreach($files as $file){
            $filename = time()."_".$file->getClientOriginalName();
            $path = 'dokumentasi/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file));

            $dokumentasi = new Documentation();
            $dokumentasi->antrian_id = $id;
            $dokumentasi->filename = $filename;
            $dokumentasi->type_file = $file->getClientOriginalExtension();
            $dokumentasi->path_file = $path;
            $dokumentasi->job_id = $request->input('jobType');
            $dokumentasi->save();
        }

        return response()->json(['success'=>'You have successfully upload file.']);
    }

    public function getMachine(Request $request){
        //Menampilkan data mesin pada tabel Machines
        $search = $request->search;

        if($search == ''){
            $machines = Machine::get();
        }else{
            $machines = Machine::orderby('machine_code','asc')->select('machine_code', 'machine_name')->where('machine_name', 'like', '%' .$search . '%')->get();
        }

        $response = array();
        foreach($machines as $machine){
            $response[] = array(
                "id" => $machine->machine_code,
                "text" => $machine->machine_name
            );
        }
        return response()->json($response);
    }

    public function showProgress($id){
        $antrian = Antrian::where('id', $id)->with('job', 'sales', 'order')
        ->first();

        return view('page.antrian-workshop.progress', compact('antrian'));
    }

    public function storeProgressProduksi(Request $request){
        $antrian = Antrian::where('id', $request->input('idAntrian'))->first();

        if($request->file('fileGambar')){
        $gambar = $request->file('fileGambar');
        $namaGambar = time()."_".$gambar->getClientOriginalName();
        $pathGambar = 'dokum-proses/'.$namaGambar;
        Storage::disk('public')->put($pathGambar, file_get_contents($gambar));
        }else{
            $namaGambar = null;
        }

        if($request->file('fileVideo')){
        $video = $request->file('fileVideo');
        $namaVideo = time()."_".$video->getClientOriginalName();
        $pathVideo = 'dokum-proses/'.$namaVideo;
        Storage::disk('public')->put($pathVideo, file_get_contents($video));
        }else{
            $namaVideo = null;
        }

        $dokumProses = new Dokumproses();
        $dokumProses->note = $request->input('note');
        $dokumProses->file_gambar = $namaGambar;
        $dokumProses->file_video = $namaVideo;
        $dokumProses->antrian_id = $request->input('idAntrian');
        $dokumProses->save();

        return redirect()->route('antrian.index');
    }

    public function markAman($id)
    {
        $design = Antrian::find($id);
        $design->is_aman = 1;
        $design->save();

        return redirect()->back()->with('success', 'File berhasil di tandai aman');
    }

    public function markSelesai($id){
        //cek apakah waktu sekarang sudah melebihi waktu deadline
        $antrian = Antrian::where('id', $id)->with('job', 'sales', 'order')->first();
        $antrian->timer_stop = Carbon::now();

        if($antrian->deadline_status = 1){
            $antrian->deadline_status = 1;
        }
        elseif($antrian->deadline_status = 0){
            $antrian->deadline_status = 2;
        }
        $antrian->status = 2;
        $antrian->save();

        return redirect()->route('antrian.index')->with('success-dokumentasi', 'Berhasil ditandai selesai !');
    }

    public function reminderProgress(){
        return response()->json('success', 200);
    }
}
