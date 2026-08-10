<?php

namespace App\Models;

use Database\Factories\AssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    /** @use HasFactory<AssessmentFactory> */
    use HasFactory;

    protected $fillable = ['name', 'period', 'description', 'created_by'];

    public function details(): HasMany
    {
        return $this->hasMany(AssessmentDetail::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'assessment_details')
            ->withPivot('criterion_id', 'value')
            ->withTimestamps();
    }
}
