<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anservice extends Model
{
    use HasFactory;

    protected $table = 'anservices';

    public function payment()
    {
        return $this->hasOne(Payment::class, 'ticket_order', 'ticket_order');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

}
