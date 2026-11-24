<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public $timestamps = false; 

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        $pattern = '%'.$search.'%';

        return $query->where(fn (Builder $query): Builder => $query
            ->where('key', 'like', $pattern)
            ->orWhere('value', 'like', $pattern));
    }
}
