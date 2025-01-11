<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; 

class GymStaff extends Authenticatable 
{
    use HasFactory;

    protected $table = 'gym_staffs'; 
    protected $primaryKey = 'gymstaff_id';// Specify the table name if it's not the plural form of the model name

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'suffix_name',
        'age',
        'contact_number',
        'gender_id',
        'profile_image',
        'role',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
// Define the relationship with the Gender model
public function gender()
{
    return $this->belongsTo(Gender::class, 'gender_id'); // Assuming you have a Gender model
    }
}