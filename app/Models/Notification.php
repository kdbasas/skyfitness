<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'message',
        'type',
        'is_read',
    ];
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    // You can also add mutators or accessors for formatted date or messages, if needed.
}