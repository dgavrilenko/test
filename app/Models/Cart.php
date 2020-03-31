<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Заказы
 *
 * Class Orders
 *
 * @property integer id
 * @property string identifier
 * @property string instance
 * @property string content

 *
 * @package App\Models
 */
class Cart extends Model
{
    protected $table = 'shoppingcart';

    protected $fillable = [
        'identifier', 'content'
    ];
}

