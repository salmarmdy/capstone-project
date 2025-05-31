<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get employees in this department
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'department', 'name');
    }

    /**
     * Get active departments only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get employee count for this department
     */
    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }
}