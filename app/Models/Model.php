<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model AS LaravelModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends LaravelModel
{
    //use SoftDeletes;

    /**
     * The connection of db
     */
    protected $connection   = 'mysql';

    /**
     * The name of the table
     *
     * @var string
     */
    //protected $table        = 'users';

    /**
     * The primaryKey of the table
     */
    //protected $primaryKey   = 'user_id';

    /**
     * The attributes that are not assignable
     */
    protected $guarded      = [];

    /**
     * The attributes that are mass assignable
     */
    //protected $fillable     = [];

    /**
     * The attributes that should be hidden for arrays
     */
    //protected $hidden       = ['password',];

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

}
