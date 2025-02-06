<?php

namespace App\Models;

use App\Models\Kota;
use App\Models\Provinsi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'infoPelanggan',
        'instansi',
        'frekuensi_order',
        'count_followUp',
        'sales_id',
        'provinsi_id',
        'kota_id',
        'last_follow_up',
        'status_follow_up',
        'priority',
        'next_follow_up',
        'reason_for_follow_up',
        'last_order_date',
    ];

    public function antrian()
    {
        return $this->hasMany(Antrian::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function documentation()
    {
        return $this->hasMany(Documentation::class);
    }
		
		public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }
}
