<?php

namespace App\Console\Commands;

use App\Models\Antrian;
use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixCustomerOrderFrequency extends Command
{
    protected $signature = 'app:fix-customer-order-frequency';

    protected $description = 'Memperbaiki frekuensi order pelanggan berdasarkan data antrian yang ada';

    public function handle()
    {
        $this->info('Mulai memperbaiki frekuensi order pelanggan...');

        Customer::chunk(100, function ($customers) {
            foreach ($customers as $customer) {
                $this->fixCustomerOrderFrequency($customer);
            }
        });

        $this->info('Proses perbaikan frekuensi order pelanggan selesai.');
    }

    private function fixCustomerOrderFrequency(Customer $customer)
    {
        $orders = Antrian::where('customer_id', $customer->id)
        ->orderBy('created_at')
        ->get(['id', 'created_at']);

        $newFrequency = 0;
        $lastOrderDate = null;

        foreach ($orders as $order) {
            $currentOrderDate = $order->created_at->format('Y-m-d');
            if ($lastOrderDate !== $currentOrderDate) {
                $newFrequency++;
                $lastOrderDate = $currentOrderDate;
            }
        }

        if ($customer->frekuensi_order !== $newFrequency) {
            $oldFrequency = $customer->frekuensi_order;
            $customer->frekuensi_order = $newFrequency;
            $customer->save();

            $this->line("Pelanggan ID {$customer->id}: frekuensi order diperbarui dari {$oldFrequency} menjadi {$newFrequency}");
        } else {
            $this->line("Pelanggan ID {$customer->id}: frekuensi order tetap {$newFrequency}");
        }
    }
}