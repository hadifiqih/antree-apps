<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Barang;
use App\Models\Antrian;
use App\Models\AntrianIklan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ManualBarangInput extends Command
{

    protected $signature = 'app:manual-barang-input';

    protected $description = 'Command description';

    public function handle()
    {
        // $awal = Carbon::now()->startOfMonth();
        // $akhir = Carbon::now()->endOfMonth();

        $tiketAwal = '2025020816073';
        $tiketAkhir = '2025021116300';

        // $antrians = Antrian::with(['customer', 'job'])->whereBetween('created_at', [$awal, $akhir])
        //     ->get();

        $antrians = Antrian::whereBetween('ticket_order', [$tiketAwal, $tiketAkhir])
        ->orderBy('ticket_order', 'asc')
        ->get();

        foreach ($antrians as $antrian) {
            $checkBarang = Barang::where('ticket_order', $antrian->ticket_order)->first();
            if($checkBarang){
                continue;
            }
            $barang = new Barang;
            $barang->ticket_order = $antrian->ticket_order;
            $barang->data_kerja_id = 0;
            $barang->customer_id = $antrian->customer_id;
            $barang->kategori_id = 1;
            $barang->job_id = $antrian->job_id;
            $barang->job_name = $antrian->job->job_name;
            $barang->sales_id = $antrian->sales_id;
            $barang->price = $antrian->harga_produk;
            $barang->qty = $antrian->qty ?? 1;
            $barang->note = $antrian->note;
            $barang->kota_id = $antrian->customer->kota_id;
            $barang->timestamps = false;
            $barang->created_at = $antrian->created_at;
            $barang->updated_at = $antrian->updated_at;
            $barang->save();

            $antrianIklan = new AntrianIklan;
            if($antrian->platform_id != 0){
                $antrianIklan->platform_id = $antrian->platform_id;
                $antrianIklan->is_iklan = 1;
            }else{
                $platform = DB::table('platforms')
                    ->where('platform_name', 'like', '%' . $antrian->customer->infoPelanggan . '%')
                    ->first();

                if ($platform) {
                    $antrianIklan->platform_id = $platform->id;
                } else {
                    $antrianIklan->platform_id = 0;
                }
                $antrianIklan->is_iklan = 0;
            }
            $antrianIklan->sales_id = $antrian->sales_id;
            $antrianIklan->job_id = $antrian->job_id;
            $antrianIklan->barang_id = $barang->id;
            //matikan timestsamp
            $antrianIklan->timestamps = false;
            $antrianIklan->created_at = $antrian->created_at;
            $antrianIklan->updated_at = $antrian->updated_at;
            $antrianIklan->save();

            $this->info('Antrian iklan berhasil dibuat untuk tiket ' . $antrian->ticket_order);
        }
    }
}
