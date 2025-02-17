<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='orders';

    protected $fillable = [
        'ticket_order',
        'title',
        'job_id',
        'sales_id',
        'description',
        'employee_id',
        'status',
        'is_priority',
        'ada_revisi',
        'type_work',
        'desain',
        'acc_desain',
        'file_cetak',
        'link_file',
        'time_taken',
        'time_end',
        'toWorkshop'
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function sales(){
        return $this->belongsTo(Sales::class);
    }

    public function design(){
        return $this->hasOne(Design::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function job(){
        return $this->belongsTo(Job::class);
    }

    public function antrian(){
        return $this->hasOne(Antrian::class);
    }

    public function payments(){
        return $this->hasOne(Payment::class, 'ticket_order');
    }
}
