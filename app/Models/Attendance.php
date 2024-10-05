<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance'; 
    protected $fillable = [
        'member_id',
        'date',
        'check_in_time',
        'check_out_time',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}