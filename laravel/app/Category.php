<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * @method static Builder ordered
 */
class Category extends Model
{
    protected $fillable = [
        'title'
    ];
    public $timestamps = false;

}
