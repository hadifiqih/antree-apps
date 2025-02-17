<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillSalesIdCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fill-sales-id-customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai update sales_id pada tabel customers...');

        $updatedRows = DB::table('customers')
            ->join('antrians', 'customers.id', '=', 'antrians.customer_id')
            ->whereNull('customers.sales_id') // Hanya update jika sales_id masih kosong
            ->update([
                'customers.sales_id' => DB::raw('antrians.sales_id')
            ]);

        $this->info("Selesai! Total data yang diperbarui: $updatedRows");
    }
}
