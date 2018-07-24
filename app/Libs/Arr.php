<?php

namespace App\Libs;

use Illuminate\Support\Arr as LaravelArr;

/**
 * Class Arr
 * @package App\Http\Libs
 */
class Arr extends LaravelArr
{

    /**
     * 过滤数组含有null的值
     *
     * @param array $arr
     * @return array
     */
    public static function filterNull(array $arr) : array
    {
        return  array_filter($arr, function ($item){

            if (is_array($item))    return self::filterNull($item);

            return  is_null($item)  ? false : true;
        });
    }

    /**
     * 转换数组含有null的为指定格式值
     *
     * @param array $arr
     * @param string $format
     * @return array
     */
    public static function fmtNullAs(array $arr, $format = '') : array
    {
        return  array_map(function (&$item) use ($format) {

            if (is_array($item))    return self::fmtNullAs($item,$format);

            return  is_null($item)  ? $format   : $item;
        },$arr);
    }

    /**
     * 根据多个字段排序多维数组 类似SQL SORT BY
     *
     * @param array $listData   [['id' => 1, 'name' => 'apple', 'age' => 21], ['id' => 2, 'name' => 'orange', 'age' => 16]]
     * @param array $sortRules  ['id' => SORT_DESC, 'name' => SORT_ASC, 'age' => SORT_DESC]
     * @return mixed
     */
    public static function multiSort(array $listData, array $sortRules) : array
    {
        $dataList = [];

        foreach ($sortRules as $field => $sequence) {

            $temp = [];
            foreach ($listData as $offset => $data) {

                $temp[] = $data[$field];
            }
            $dataList[] = $temp;
            $dataList[] = $sequence;
        }

        $dataList[] = &$listData;
        call_user_func_array('array_multisort', $dataList);

        return  array_pop($dataList);
    }

    /**
     * 将关联数组键值格式化
     *
     * @param array $arr
     * @param string $format "camel","snake"
     * @return array
     */
    public static function fmtCase($arr, string $format = 'camel')
    {
        if (!is_array($arr))         return $arr;
        if (!self::isAssoc($arr))   return $arr;

        $result = [];
        foreach ($arr as $key => $item) {

            $fmtKey          = Str::$format($key);
            $result[$fmtKey] = self::fmtCase($item);
        }

        return $result;
    }

    /**
     * 重置在多维数组中的索引键
     *
     * @param array $arr
     * @return array
     */
    public static function fixKeys(array $arr) : array
    {
        $numberCheck = false;
        foreach ($arr as $key => $item) {

            if (is_array($item))    $array[$key] = self::fixKeys($item);
            if (is_numeric($key))   $numberCheck = true;
        }

        if (true === $numberCheck)  return array_values($arr);

        return  $arr;
    }

    /**
     * 按键做递归排序
     *
     * @param $arr
     * @param int $flags
     * @return array
     */
    public static function kSort($arr, $flags = SORT_NATURAL)
    {
        if (!is_array($arr)) return $arr;

        foreach ($arr as &$item) {

            self::sort($item);
        }

        ksort($arr, $flags);

        return  $arr;
    }

    /**
     * 数据格式化成树形结构
     *
     * @param $items    [['id' => 1, 'pid' => 0, 'name' => '江西省'],['id' => 3, 'pid' => 1, 'name' => '南昌市']]
     * @return array
     * [[0] => [
     *      'id'    => 1,
     *      'pid'   => 0,
     *      'name'  => '江西省',
     *      'son'   => [
     *          'id'    => 3,
     *          'pid'   => 1,
     *          'name'  => '南昌市'
     *      ]
     * ]]
     */
    public function formatTree(array $items) : array
    {
        $tree = [];
        foreach ($items as $k =>$item) {

            if (isset($items[$item['pid']])) {

                $items[$item['pid']]['son'][] = &$items[$item['id']];
            } else {

                $tree[] = &$items[$item['id']];
            }
        }

        return $tree;
    }
}