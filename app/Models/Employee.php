<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'name',
        'department',
        'address',
        'email',
        'phone_number',
        'sim_number',
        'sim_expiry_date',
        'employment_status'
    ];

    protected $dates = [
        'sim_expiry_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    /**
     * Get employee's login credentials if exists
     */
    public function getLoginCredentials()
    {
        if ($this->user) {
            return [
                'username' => $this->user->username,
                'has_account' => true
            ];
        }
        return ['has_account' => false];
    }
}