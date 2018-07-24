<?php

namespace App\Models;

/**
 * Class City
 * @package App\Models
 */
class City extends Model
{
    /**
     * The name of the table
     *
     * @var string
     */
    protected $table        = 'cities';

    /**
     * The primaryKey of the table
     */
    protected $primaryKey   = 'adcode';

    /**
     * The attributes that are not assignable
     */
    protected $guarded      = [];

    /**
     * Determine if the model uses timestamps.
     */
    public $timestamps      = false;

    /**
     * enum
     */
    // 城市分级
    const PROVINCE      = 1;
    const CITY          = 2;
    const DISTRICT      = 3;
    const CLASS_MAP     = [
        self::PROVINCE  => '省',
        self::CITY      => '市',
        self::DISTRICT  => '区',
    ];

    // 城市等级
    const YIXIAN        = 1;
    const XINYIXIAN     = 2;
    const ERXIAN        = 3;
    const SANXIAN       = 4;
    const SIXIAN        = 5;
    const WUXIAN        = 6;
    const LEVEL_MAP     = [
        self::YIXIAN    => '一线城市',
        self::XINYIXIAN => '新一线城市',
        self::ERXIAN    => '二线城市',
        self::SANXIAN   => '三线城市',
        self::SIXIAN    => '四线城市',
        self::WUXIAN    => '五线城市',
    ];
}
