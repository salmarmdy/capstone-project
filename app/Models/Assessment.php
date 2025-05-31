<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'employee_id', 
        'vehicle_id',
        'assessment_date',
        'comments',
        'approved',
        'status_name',
        'status_description',
        'status_color_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assessment_date' => 'date',
        'approved' => 'boolean',
    ];

    /**
     * Get the employee who performed this assessment.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * Get the vehicle being assessed.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    /**
     * Get the checklist items for this assessment.
     */
    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class, 'assessment_id', 'id');
    }

    public function checklistResults()
    {
        return $this->hasMany(AssessmentChecklistResult::class, 'assessment_id');
    }

    public function checklistResultsWithItems()
    {
        return $this->hasMany(AssessmentChecklistResult::class, 'assessment_id')
                    ->with('checklistItem');
    }
}