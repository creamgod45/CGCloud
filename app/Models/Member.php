<?php

namespace App\Models;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin Builder
 *
 * @method static Builder|static query()
 * @method static static make(array $attributes = [])
 * @method static static create(array $attributes = [])
 * @method static static forceCreate(array $attributes)
 * @method Member firstOrNew(array $attributes = [], array $values = [])
 * @method Member firstOrFail($columns = ['*'])
 * @method Member firstOrCreate(array $attributes, array $values = [])
 * @method Member firstOr($columns = ['*'], Closure $callback = null)
 * @method Member firstWhere($column, $operator = null, $value = null, $boolean = 'and')
 * @method Member updateOrCreate(array $attributes, array $values = [])
 * @method null|static first($columns = ['*'])
 * @method static static findOrFail($id, $columns = ['*'])
 * @method static static findOrNew($id, $columns = ['*'])
 * @method static null|static find($id, $columns = ['*'])
 *
 * @property-read int    $id
 *
 * @property string      $username
 * @property string      $email
 * @property string      $password
 * @property string      $phone
 * @property string      $enable
 * @property string      $administrator
 * @property string      $remember_token
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Member extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = true; // 指定模型对应的表
    protected $table = 'members'; // 主键，Laravel 默认也是 id，此行可省略
    protected $primaryKey = 'id'; // 因为 id 是自增的
    protected $keyType = 'int'; // 主键类型

    protected $fillable = [
        'username',
        'email',
        'password',
        'phone',
        'enable',
        'administrator',
        'remember_token',
    ]; // 允许批量赋值的字段

    protected $hidden = [
        'password',
        'remember_token', // 在模型数组或 JSON 显示时隐藏这些字段
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
