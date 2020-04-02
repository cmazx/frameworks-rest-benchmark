<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Todo
 * @method static Builder ordered
 */
class Todo extends Model
{
    protected $fillable = [
        'title',
        'done'
    ];
    protected $attributes = [
        'done' => 0
    ];
    public $timestamps = false;

    /**
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     *
     * @return mixed
     */
    public function scopeOrdered($queryBuilder)
    {
        return $queryBuilder->orderBy('id');
    }
}
