<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanTrashUpFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-trash';

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
        // Ambil daftar file dari tabel Order
        $orderFiles = Payment::pluck('payment_proof')->toArray();

        // Ambil daftar semua file di folder 'file-cetak' di storage disk 'public'
        $files = Storage::disk('public')->files('bukti-pembayaran');

        foreach ($files as $file) {
            // Ekstrak nama file dari path yang disimpan di Storage
            $fileName = basename($file);

            // Jika nama file tidak ada dalam daftar file di Order, hapus file tersebut
            if (!in_array($fileName, $orderFiles)) {
                Storage::disk('public')->delete($file);
                $this->info('File ' . $fileName . ' has been deleted!');
            }
        }

        $this->info('Trash up files have been cleaned up!');
    }
}
