<?php

namespace api\models;

use yii\db\ActiveRecord;

class Basic extends ActiveRecord
{
//    public $db;
    public $limit=20;
    public $offset=1;
    public $error_code;
    public $error_msg;

    public function init()
    {
        parent::init();
    }

    /**
     * 聚合统计参数与数据数组
     * @param $query
     * @return array
     */
    protected function getCountParam($query)
    {
        $countQuery = clone $query;
        $totalCount = $countQuery->count();
        $pageCount = ceil($totalCount / $this->limit);
        return [
            'item'=>$query->asArray()->all(),
            '_meta'=>[
                'totalCount'=>$totalCount,
                'pageCount'=>$pageCount,
                'currentPage'=>$this->offset,
                'perPage'=>$this->limit,
            ],
        ];
    }
}
