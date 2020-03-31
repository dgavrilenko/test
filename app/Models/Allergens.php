<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AllergenCategory;
use App\Models\AllergenType;

/**
 * Аллергены
 *
 * @property integer id
 * @property string name
 * @property string header
 * @property integer category_id
 * @property integer type_id
 * @property string code
 * @property string description
 * @property string composition
 * @property string created_at
 * @property string updated_at
 *
 * Class Allergens
 * @package App\Models
 */
class Allergens extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Файл
     *
     * @return BelongsTo
     */
    public function type(): belongsTo
    {
        return $this->belongsTo(AllergenType::class)->select(['id', 'name']);
    }

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
