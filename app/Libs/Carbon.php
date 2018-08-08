<?php

namespace App\Libs;

use Illuminate\Support\Carbon as LaravelCarbon;

/**
 * Class Carbon
 * @package App\Http\Libs
 */
class Carbon extends LaravelCarbon
{
    const ONE_DAY             = 86400;

    const DATE_DAILY     = 'daily';
    const DATE_WEEKLY    = 'weekly';
    const DATE_MONTHLY   = 'monthly';
    const DATE_QUARTERLY = 'quarterly';
    const DATE_YEARLY    = 'yearly';

    const SELF_DATE_MAP = [
        self::DATE_DAILY     => 'Y-m-d',
        self::DATE_WEEKLY    => 'o-W',
        self::DATE_MONTHLY   => 'Y-m',
        self::DATE_QUARTERLY => 'Y-m',
        self::DATE_YEARLY    => 'Y',
    ];

    /**
     * 计算时间段有多少天
     *
     * @param $start
     * @param $end
     * @return int
     * @throws \Exception
     */
    public static function countDays($start, $end) : int
    {
        if (strcmp($start,$end) > 0) {

            Error::programErr('起止日期异常');
        }

        $startStamp = strtotime($start);
        $endStamp   = strtotime($end);

        return  (int)(($endStamp - $startStamp) / self::ONE_DAY);
    }

    /**
     * 分割时间段
     *
     * @param $start
     * @param $end
     * @param int $pieces
     * @param string $format
     * @return array
     * @throws \Exception
     */
    public static function chopPeriod($start, $end, int $pieces = 3, string $format = 'Y-m-d') : array
    {
        $days   = self::countDays($start, $end);

        $pieces = $days >= $pieces
                ? $pieces
                : ($days ? : 1);

        $step   = (int)floor($days / $pieces);

        $dates  = [];
        for($i = 0; $i < $pieces; $i++) {

            $dates[$i] = date($format,(strtotime($start) + $i * $step * self::ONE_DAY));
        }
        $dates[$pieces] = date($format,strtotime($end));

        $result = array_values(array_unique($dates));

        return  $result;
    }

    /**
     * 根据类型获取时间列表
     *
     * @param $start
     * @param $end
     * @param string $format
     * @return array
     * @throws \Exception
     */
    public static function getDateList($start, $end, string $format = self::DATE_DAILY) : array
    {
        $dateList = [];

        switch ($format) {

            case self::DATE_DAILY :
                $dateList = self::getDailyList($start, $end);
                break;

            case self::DATE_WEEKLY :
                $dateList = self::getWeeklyList($start, $end);
                break;

            case self::DATE_MONTHLY :
                $dateList = self::getMonthlyList($start, $end);
                break;

            case self::DATE_QUARTERLY :
                $dateList = self::getQuarterlyList($start, $end);
                break;

            case self::DATE_YEARLY :
                $dateList = self::getYearlyList($start, $end);
                break;

            default :
                Error::programErr('日期类型异常');
        }

        return  $dateList;
    }

    /**
     * 天列表
     *
     * @param $start
     * @param $end
     * @return array
     */
    protected static function getDailyList ($start, $end) : array
    {

        $startTime  = strtotime($start);
        $endTime    = strtotime($end);

        $result     = [];
        while ($startTime <= $endTime) {

            $date       = date('Y-m-d', $startTime);
            $result[]   = [
                'value' => $date,
                'label' => $date,
            ];
            $startTime  = strtotime('+1 days', $startTime);
        }

        return  $result;
    }

    /**
     * 周列表
     *
     * @param $start
     * @param $end
     * @return array
     */
    protected static function getWeeklyList($start, $end) : array
    {
        $startTime  = strtotime($start);
        $endTime    = strtotime($end);

        $result     = [];
        while ($startTime <= $endTime) {

            list($year, $week)  = explode('-', date('o-W', $startTime));
            $result[]   = [
                'value' => "{$year}-{$week}",
                'label' => "{$year}年{$week}周",
            ];
            $startTime  = strtotime('next monday', $startTime);
        }

        return  $result;
    }

    /**
     * 月列表
     *
     * @param $start
     * @param $end
     * @return array
     */
    protected static function getMonthlyList($start, $end) : array
    {
        $startTime  = strtotime(self::parse($start)->startOfMonth()->toDateString());
        $endTime    = strtotime(self::parse($end)->startOfMonth()->toDateString());

        $result     = [];
        while ($startTime <= $endTime) {

            list($year, $month) = explode('-', date('Y-m', $startTime));
            $result[]   = [
                'value' => "{$year}-{$month}",
                'label' => "{$year}年{$month}月",
            ];
            $startTime  = strtotime('+1 month', $startTime);
        }

        return  $result;
    }

    /**
     * 季列表
     *
     * @param $start
     * @param $end
     * @return array
     */
    protected static function getQuarterlyList($start, $end) : array
    {
        $monthInfo  = self::getMonthlyList($start, $end);
        $monthList  = array_column($monthInfo, 'value');

        $result     = [];
        foreach ($monthList as $month) {

            $yearInt    = date('Y', strtotime($month . '-01'));
            $monthInt   = date('n', strtotime($month . '-01'));
            $quarterInt = '0' . ceil($monthInt / 3);
            $unique     = $yearInt . $quarterInt;
            $result[$unique]    = [
                'value' => (string) $unique,
                'label' => "{$yearInt}年{$quarterInt}季",
            ];
        }

        return  array_values($result);
    }

    /**
     * 年列表
     *
     * @param $start
     * @param $end
     * @return array
     */
    protected static function getYearlyList($start, $end) : array
    {

        $yearStart  = self::parse($start)->year;
        $yearEnd    = self::parse($end)->year;

        $result     = [];
        for ($yearInt = $yearStart; $yearInt <= $yearEnd; $yearInt++) {

            $result[]   = [
                'value' => (string) $yearInt,
                'label' => "{$yearInt}年",
            ];
        }

        return  $result;
    }
}