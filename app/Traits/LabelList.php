<?php

namespace App\Http\Traits;

trait LabelList
{
    /**
     * select组件下拉数据
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $id
     * @param string $text
     * @param bool|array $where
     * @return array
     */
    protected function labelList($model, string $id, string $text, $where = false)
    {
        return
            $model->when($where, function ($query, $where) {
                return $query->where($where);
            })
            ->orderBy("$id", 'dsc')
            ->get(["$id","$text"])
            ->map(function ($item) use ($id, $text) {
                return  [
                    'label' => $item->$text,
                    'value' => $item->$id,
                ];
            })
            ->all();
    }
}