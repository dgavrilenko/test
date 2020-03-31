<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class User
 *
 * @property integer id
 * @property string name
 * @property string email
 * @property string email_verified_at
 * @property string remember_token
 * @property string company
 * @property string password
 * @property string address
 * @property string post
 * @property string phone
 * @property string legal_address
 * @property string ogrn
 * @property string okpo
 * @property string inn
 * @property string kpp
 * @property string rs
 * @property string ks
 * @property string bank
 * @property string bik

 *
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type_id',
        'organization',
        'address',
        'post',
        'phone',
        'legal_address',
        'ogrn',
        'okpo',
        'inn',
        'kpp',
        'rs',
        'ks',
        'bik',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Файл
     *
     * @return BelongsTo
     */
    public function type(): belongsTo
    {
        return $this->belongsTo(UserType::class)->select(['code', 'name']);
    }
}
