<?php

namespace App\Models;

use App\Models\Job;
use App\Models\Order;
use App\Models\Sales;
use App\Models\Design;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Platform;
use App\Models\Dokumproses;
use App\Models\Documentation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrians';

    protected $fillable = [
        'ticket_order',
        'customer_id',
        'sales_id',
        'job_id',
        'note',
        'admin_note',
        'start_job',
        'end_job',
        'design_id',
        'operator_id',
        'finisher_id',
        'qc_id',
        'omset',
        'qty',
        'harga_produk',
        'packing_cost',
        'working_at',
        'machine_code',
        'alamat_pengiriman',
        'file_po',
        'order_id',
        'platform_id',
        'timer_stop',
        'status',
        'is_aman',
        'deadline_status',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'ticket_order', 'ticket_order');
    }

    public function operator()
    {
        return $this->belongsTo(Employee::class, 'operator_id');
    }

    public function finishing()
    {
        return $this->belongsTo(Employee::class, 'finisher_id');
    }

    public function quality()
    {
        return $this->belongsTo(Employee::class, 'qc_id');
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function documentation()
    {
        return $this->hasMany(Documentation::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_code', 'machine_code');
    }

    public function dokumproses()
    {
        return $this->hasMany(Dokumproses::class);
    }

    //ambil job_type dari tabel job
    public function getJobTypeAttribute()
    {
        return $this->job->job_type;
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

}
