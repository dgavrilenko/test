<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Каталог продуктов
 * Class Products
 * @package App\Models
 * @property integer id
 * @property string number
 * @property boolean active
 * @property string name
 * @property string header
 * @property integer type_id
 * @property integer category_id
 * @property string description
 * @property double price
 * @property string units
 * @property integer multiplicity
 * @property integer definitions_number
 * @property string created_at
 * @property string updated_at
 */
class Product extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Файл
     *
     * @return BelongsTo
     */
    public function category(): belongsTo
    {
        return $this->belongsTo(AllergenCategory::class)->select(['id', 'name']);
    }
}
