<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function search(Request $request)
    {
        $data = Customer::where('nama', 'LIKE', "%".request('q')."%")->where('sales_id', auth()->user()->sales->id)->limit(10)->get();
        return response()->json($data);
    }

    public function searchById(Request $request)
    {
        $data = Customer::where('id', 'LIKE', "%".request('id')."%")->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        //Menyimpan no.telp dalam format seperti berikut 081234567890, tanpa spasi. strip, titik, dll
        $telp = preg_replace('/\D/', '', $request->modalTelepon);

        $customer = new Customer;

        $customer->telepon = $telp;

        if($request->modalNama){
            $customer->nama = $request->modalNama;
        }

        if($request->modalAlamat){
            $customer->alamat = $request->modalAlamat;
        }

        if($request->modalInstansi){
            $customer->instansi = $request->modalInstansi;
        }

        if($request->modalInfoPelanggan){
            $customer->infoPelanggan = $request->modalInfoPelanggan;
        }

        if($request->provinsi_id){
            $customer->provinsi_id = $request->provinsi_id;
        }

        if($request->kota_id){
            $customer->kota_id = $request->kota_id;
        }

        if($request->sales_id){
            $customer->sales_id = $request->sales_id;
        }

        $customer->save();

        return response()->json(['success' => 'true', 'message' => 'Data berhasil ditambahkan']);
    }

	public function getProvinsi(Request $request)
    {
        $search = $request->search;

        //Mengambil data provinsi dari API
        $provinsi = DB::table('provinsi')->where('name', 'like', "%{$search}%")->get();

        $results = [];
        foreach ($provinsi as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => $item->name
            ];
        }

        //Mengembalikan data provinsi dalam bentuk JSON
        return response()->json(['results' => $results]);
    }

	public function getKota(Request $request)
    {
        $search = $request->search;
        $provinsiId = $request->provinsi_id;

        //Mengambil data kota dari API
        $kota = DB::table('kota')->where('provinsi_id', $provinsiId)->where('name', 'like', "%{$search}%")->get();

        $results = [];
        foreach ($kota as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => $item->name
            ];
        }

        //Mengembalikan data kota dalam bentuk JSON
        return response()->json(['results' => $results]);
    }

}
