<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criterion extends Model
{
    /** @use HasFactory<\Database\Factories\CriterionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'type', 'weight'];

    protected function casts(): array
    {
        return [
            'weight' => 'float',
        ];
    }

    public function assessmentDetails(): HasMany
    {
        return $this->hasMany(AssessmentDetail::class);
    }
}
