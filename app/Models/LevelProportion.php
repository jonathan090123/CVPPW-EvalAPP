<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelProportion extends Model
{
    protected $fillable = ['position', 'criterion_id', 'proportion'];

    protected $casts = [
        'proportion' => 'float',
    ];

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }

    /**
     * Ambil proporsi efektif untuk (position, criterion_id).
     * Default 1.00 jika tidak ada setting.
     */
    public static function getProportion(string $position, int $criterionId): float
    {
        return (float) self::where('position', $position)
            ->where('criterion_id', $criterionId)
            ->value('proportion') ?? 1.00;
    }

    /**
     * Map [position => [criterion_id => proportion]] untuk semua setting.
     */
    public static function loadAll(): array
    {
        $map = [];
        foreach (self::with('criterion')->get() as $row) {
            $map[$row->position][$row->criterion_id] = (float) $row->proportion;
        }

        return $map;
    }
}