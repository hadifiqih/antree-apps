<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Antrian;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Payment::all();

        return view ('antrian.payment.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $antrian = Antrian::where('ticket_order', $request->order_number)->first();

        $status = $request->payment_amount == $request->omset ? 1 : 0;
        dd($antrian->ticket_order,$request->omset, $status);

        //Membuat $fileName dengan kondisi jika status = 1 maka nama file + ticket_order = fullpayment.jpg Jika tidak maka nama file + ticket_order = downpayment.jpg
        $file = $request->file('payment_proof');
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $fileName = $status == 1 ? 'fullpayment'. $fileName : 'downpayment' . $fileName;
        $path = $file->storeAs('public/payment-proof', $fileName);

        $validated = $request->validate([
            'order_number' => 'required',
            'omset' => 'required|numeric|min:0',
            'payment_amount' => 'required|numeric|min:0|max:' . $request->omset,
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bank' => 'required'
        ]);

        $validated['payment_status'] = $validated['payment_amount'] == $validated['omset'] ? 1 : 0;
        $validated['payment_proof'] = $path; // Menyimpan path ke payment_proof dalam field yang sesuai

        Payment::create($validated);

        return redirect()->route('payments.index')->with('success', 'Pembayaran dikonfirmasi !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payments = Payment::where('ticket_order', $id)->first();

        return view('antrian.payment.edit', compact('payments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePelunasan(Request $request)
    {
        try {
            $payment = Payment::where('ticket_order', $request->ticketAntrian)
                          ->orderBy('created_at', 'desc')
                          ->first();
        } catch (\Throwable $th) {
            return redirect()->back()->json(['error' => 'Pembayaran tidak ditemukan !']);
        }

        //menghilangkan Rp dan titik
        $jumlahPembayaran = str_replace(['Rp ', '.'], '', $request->jumlahPembayaran);
        //convert ke integer
        $jumlahPembayaran = (int) $jumlahPembayaran;
        //total pembayaran
        $totalPembayaran = $jumlahPembayaran + $payment->payment_amount;
        //total sisa pembayaran
        $sisaPembayaran = $payment->total_payment - $totalPembayaran;

        if($sisaPembayaran < 0){
            return redirect()->back()->with('error', 'Jumlah pembayaran melebihi total pembayaran !');
        }elseif($sisaPembayaran == 0){
            $payment->payment_status = "Lunas";
            $payment->payment_amount = $totalPembayaran;
        }else{
            $payment->payment_status = "DP";
            $payment->payment_amount = $totalPembayaran;
        }

        //Menyimpan bukti pembayaran ke dalam folder bukti-pembayaran
        if ($request->hasFile('filePelunasan')) {
            $file = $request->file('filePelunasan');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $path = 'bukti-pembayaran/' . $fileName;
            Storage::disk('public')->put($path, file_get_contents($file));
            $payment->payment_proof = $fileName;
            $payment->save();
        }else{
            $payment->payment_proof = null;
            $payment->save();
        }

        return redirect()->route('antrian.index')->with('success', 'Pembayaran berhasil diperbarui !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
