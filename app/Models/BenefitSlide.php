<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BenefitSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body_text',
        'sort_order',
    ];

    /**
     * Move this slide to the given 1-based position, shifting every other
     * slide so `sort_order` stays a unique, gapless 1..N sequence. Treats
     * $position as "insert here" rather than a literal value to store, so
     * two slides can never end up sharing the same order number.
     */
    public function moveToSortOrder(int $position): void
    {
        DB::transaction(function () use ($position) {
            $siblingIds = static::query()
                ->where('id', '!=', $this->id)
                ->orderBy('sort_order')
                ->lockForUpdate()
                ->pluck('id')
                ->all();

            $position = max(1, min($position, count($siblingIds) + 1));

            array_splice($siblingIds, $position - 1, 0, [$this->id]);

            foreach ($siblingIds as $index => $id) {
                static::whereKey($id)->update(['sort_order' => $index + 1]);
            }

            $this->sort_order = $position;
        });
    }
}
