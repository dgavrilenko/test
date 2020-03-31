<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
 * 1. Оформлен, ожидается оплата
 * 2. Оплачен
 * 3. Доставлен
 * 4. Отменен    (из письма только клиент)   администратор из админки.  Производителю письма отправляется после оплаты...
 * */


/**
 * Заказы
 *
 * Class Orders
 *
 * @property integer id
 * @property string name
 * @property double price
 * @property double basket_id
 * @property string email
 * @property string phone
 * @property string post
 * @property string fax
 * @property string organization
 * @property string inn
 * @property string kpp
 * @property string note
 * @property string status
 * @property string token
 * @property string address
 * @property string rs
 * @property string legal_address
 * @property string ogrn
 * @property string okpo
 * @property string ks
 * @property string bank
 * @property string bik
 * @property string discount
 * @property string total_price
 * @property string created_at
 * @property string updated_at
 *
 * @package App\Models
 */
class Orders extends Model
{
    protected $fillable = [
        'name', 'price', 'products', 'description', 'email',
        'phone', 'post', 'fax', 'organization', 'inn',
        'kpp', 'note', 'status', 'token',
        'address',
        'rs',
        'legal_address',
        'ogrn',
        'okpo',
        'ks',
        'bank',
        'bik',
        'discount',
        'total',
        'total_price'
    ];

    /**
     * Файл
     *
     * @return BelongsTo
     */
    public function cart(): belongsTo
    {
        return $this->belongsTo(Cart::class, 'basket_id', 'identifier')->select(['content', 'identifier']);
    }
}
