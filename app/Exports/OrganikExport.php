<?php

namespace App\Exports;

use App\Models\Antrian;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrganikExport implements FromQuery, WithHeadings, WithMapping

{
    public function query()
    {
        $awal = date('Y-m-01') . ' 00:00:00';
        $akhir = date('Y-m-t') . ' 23:59:59';
        $platform = ['Instagram', 'Facebook', 'Tiktok', 'Youtube', 'Shopee', 'Tokopedia', 'Google', 'G-Maps'];
        return Antrian::query()
            ->with('customer', 'job', 'sales', 'customer.kota')
            ->whereHas('customer', function ($query) use ($platform) {
                $query->whereIn('infoPelanggan', $platform)
								->where('frekuensi_order', 1);
            })
            ->whereBetween('created_at', [$awal, $akhir])
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Tanggal Order',
			'Tiket Order',
            'Sales',
            'Kota',
            'Platform',
            'Nama Produk',
            'Omset',
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at,
						$row->ticket_order,
            $row->sales->sales_name,
            $row->customer->kota->name ?? '-',
            $row->customer->infoPelanggan,
            $row->job->job_name,
            $row->omset,
        ];
    }
}
