<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    // Specify the table name if different from the default
    protected $table = 'members';

    // Define the primary key if different from the default 'id'
    protected $primaryKey = 'member_id';

    // Allow mass assignment for these attributes
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'suffix_name', 'date_joined', 'date_expired', 'email', 'contact_number', 'subscription_id', 'amount','qr_code',
    ];

    // Optionally, specify the data types for dates
    protected $dates = [
        'date_joined',
        'date_expired',
    ];

    // Define relationships if needed
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
