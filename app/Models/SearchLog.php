<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;
    protected $table = 'search_logs';
    protected $fillable = ['user_id', 'ticket_order', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function antrian()
    {
        return $this->belongsTo(Antrian::class, 'ticket', 'ticket_order');
    }
}

