<?php
namespace api\controllers;

use yii;
use yii\rest\ActiveController;

class BasicController extends ActiveController
{
    public $_uid;
    public $req;
    public $code=200;
    public $msg='success';

    public function init()
    {
        parent::init();
        $this->req = array_merge(Yii::$app->request->bodyParams,Yii::$app->request->get());
    }

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * 所有actions重写
     * @return array
     */
    public function actions()
    {
        parent::actions();
        return [];
    }

    /**
     * 自定义格式化输出
     * @param array $data
     * @return array|mixed
     */
    protected function serializeData($data=array())
    {
        $data = parent::serializeData($data);
        if (isset($data['items']) && isset($data['_meta'])) {
            return [
                'code'=>$this->code,
                'msg'=>$this->msg,
                'data'=>$data['items'],
                'total'=>$data['_meta']
            ];
        } else {
            return [
                'code'=>$this->code,
                'msg'=>$this->msg,
                'data'=>$data
            ];
        }

    }

    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('资源请求错误',500);
        }
    }
}
