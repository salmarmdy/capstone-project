<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentChecklistResult extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'assessment_checklist_results';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'assessment_id',
        'checklist_items_id',
        'passed',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'passed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the assessment that owns the checklist result.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    /**
     * Get the checklist item that owns the result.
     */
    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_items_id');
    }

    /**
     * Scope untuk hasil yang lulus
     */
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    /**
     * Scope untuk hasil yang tidak lulus
     */
    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    /**
     * Scope untuk assessment tertentu
     */
    public function scopeForAssessment($query, $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    /**
     * Accessor untuk status text
     */
    public function getStatusTextAttribute()
    {
        return $this->passed ? 'Lulus' : 'Gagal';
    }

    /**
     * Accessor untuk status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return $this->passed ? 'badge-success' : 'badge-danger';
    }

    /**
     * Accessor untuk status icon
     */
    public function getStatusIconAttribute()
    {
        return $this->passed ? 'fas fa-check-circle' : 'fas fa-times-circle';
    }
}