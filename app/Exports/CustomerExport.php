<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Customer::with('kota', 'provinsi');
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Telepon',
            'Alamat',
            'Sumber Pelanggan',
            'Instansi',
            'Kota',
            'Provinsi',
            'Frekuensi Pembelian',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->nama,
            $customer->telepon,
            $customer->alamat,
            $customer->infoPelanggan,
            $customer->instansi,
            $customer->kota->name ?? '-',
            $customer->provinsi->name ?? '-',
            $customer->frekuensi_order,
        ];
    }
}
