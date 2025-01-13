<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasFactory;

    protected $table = 'genders'; // Specify the table name if necessary
    protected $primaryKey = 'gender_id';


    protected $fillable = [
        'name',
    ];

    // Define the relationship with the GymStaff model
    public function gymStaff()
    {
        return $this->hasMany(GymStaff::class, 'gender_id');
    }
}