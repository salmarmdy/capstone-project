<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'employee_id',
        'license_plate',
        'vehicle_type',
        'brand',
        'model',
        'engine_capacity',
        'license_expiry',
        'license_document_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'license_expiry' => 'date',
        'engine_capacity' => 'integer',
    ];

    /**
     * Get the employee that owns the vehicle.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * Get the assessments for the vehicle.
     */
    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'vehicle_id', 'vehicle_id');
    }
}