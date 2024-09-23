<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    // Explicitly specify the database table name
    protected $table = 'equipments'; // This is the name of your equipment table

    // Specify which fields can be mass-assigned
    protected $fillable = [
        'equipment_name', // The name of the equipment (e.g., Treadmill, Dumbbells)
        'equipment_picture',        // The path to the equipment picture (stored in your public folder)
        'total_number',   // Total quantity of this equipment
        'status',         // Equipment status, could be 'active' or 'inactive'
    ];

    // Define the primary key if it doesn't follow Laravel's default convention
    protected $primaryKey = 'equipment_id'; // Primary key for this table is 'equipment_id'

    // Enable timestamps to automatically handle created_at and updated_at fields
    public $timestamps = true;

    /**
     * Example relationship methods:
     * 
     * - If you want to link this equipment to a gym or a location, 
     * you can define relationships here.
     * 
     * Example:
     * public function gym() {
     *     return $this->belongsTo(Gym::class); // If your equipment belongs to a specific gym
     * }
     * 
     * You can add more based on how your system is structured.
     */
}
