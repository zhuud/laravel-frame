<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The column name of the "remember me" token to auth
     */
    protected $rememberTokenName = 'remember_token';

    /**
     * The column name of the "password" password to auth
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * The connection of db
     */
    protected $connection   = 'mysql';

    /**
     * The name of the table
     *
     * @var string
     */
    protected $table        = 'users';

    /**
     * The primaryKey of the table
     */
    protected $primaryKey   = 'user_id';

    /**
     * The attributes that are not assignable
     */
    protected $guarded      = ['user_id'];

    /**
     * The attributes that are mass assignable
     */
    //protected $fillable     = [];

    /**
     * The attributes that should be hidden for arrays
     */
    protected $hidden       = ['password', 'remember_token',];

    /**
     * The attributes that should be casted
     */
    protected $casts        = [];

    /**
     * The attributes that should be casted date
     */
    protected $dates        = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    //const CREATED_AT    = null;
    //const UPDATED_AT    = 'update_time';

    /**
     * Determine if the model uses timestamps.
     */
    public $timestamps      = true;

    /**
     * Mosaic model query parameters
     *
     * @param array $params
     * @param null|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Model $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function setCondition(array $params, $query = null)
    {
        // TODO: Implement setCondition() method.
    }
}
