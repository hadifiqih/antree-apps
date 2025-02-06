<?php

namespace App\Exports;

use App\Models\Antrian;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WorkshopExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Antrian::query()
        ->with('payment', 'order', 'sales', 'customer', 'job', 'design', 'operator', 'finishing', 'quality')
        ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Dibuat Pada',
            'Ticket Order',
            'Sales',
            'Nama Produk',
            'Qty',
            'Harga',
            'Omset',
            'Mulai',
            'Selesai',
            'Desainer',
            'Operator',
            'Finishing',
            'Pengawas',
        ];
    }

    public function map($workshop): array
    {
        $operatorId = explode(',', $workshop->operator_id);
        $operator = Employee::whereIn('id', $operatorId)->get();
        $operatorName = '';
        foreach ($operator as $key => $value) {
            $operatorName .= $value->name . ', ';
        }

        $finishingId = explode(',', $workshop->finishing_id);
        $finishing = Employee::whereIn('id', $finishingId)->get();
        $finishingName = '';
        foreach ($finishing as $key => $value) {
            $finishingName .= $value->name . ', ';
        }

        return [
            $workshop->created_at->format("Y-m-d"),
            $workshop->ticket_order,
            $workshop->sales->sales_name,
            $workshop->job->job_name,
            $workshop->qty,
            $workshop->harga_produk,
            $workshop->omset,
            $workshop->start_job,
            $workshop->end_job,
            $workshop->order->employee->name,
            $operatorName,
            $finishingName,
            $workshop->quality->name ?? '-',
        ];
    }
}
