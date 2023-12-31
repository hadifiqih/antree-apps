<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function search(Request $request)
    {
        $data = Customer::where('nama', 'LIKE', "%".request('q')."%")->get();
        return response()->json($data);
    }

    public function searchById(Request $request)
    {
        $data = Customer::where('id', 'LIKE', "%".request('id')."%")->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $customer = new Customer;
        $customer->telepon = $request->modalTelepon;

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

        $customer->save();

        return response()->json(['success' => 'true', 'message' => 'Data berhasil ditambahkan']);
    }

}
