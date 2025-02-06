<?php

namespace App\Exports;

use App\Models\Antrian;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenjualanBulanan implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        $awalJanuari = date('Y') . '-01-01 00:00:00';
        $akhirJanuari = date('Y') . '-01-31 23:59:59';

        return Antrian::with(['sales', 'customer', 'job', 'platform'])
            ->whereBetween('created_at', [$awalJanuari, $akhirJanuari]);
    }

    public function headings(): array
    {
        return [
            'Tiket Order',
            'Tanggal Order',
            'Sales',
            'Kota',
            'Nama Produk',
            'Platform',
            'Omset',
        ];
    }

    public function map($antrian): array
    {
        return [
            $antrian->ticket_order,
            $antrian->created_at->format('d-m-Y'), 
            $antrian->sales->sales_name,
            $antrian->customer->kota->name ?? '-', // Kota bisa kosong
            $antrian->job->job_name,
            $antrian->platform->platform_name ?? '-', // Platform bisa kosong
            $antrian->harga_produk * $antrian->qty,
        ];
    }
}
