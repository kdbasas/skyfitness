<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments'; // Explicitly defining the table name
    protected $primaryKey = 'payment_id'; // Custom primary key
    protected $fillable = ['member_id', 'subscription_id', 'amount', 'date_paid']; // Mass assignable attributes

    /**
     * Get the member associated with the payment.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    /**
     * Get the subscription associated with the payment.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
