<?php


namespace yiifast\controllers;


use yii\base\InvalidConfigException;
use yii\rest\ActiveController;
use Yii;
use yii\web\HttpException;

class BaseController extends ActiveController
{

    /** 获取块应用的处理数据
     * @return mixed
     */
    public function block()
    {
        $controllerNamespace=Yii::$app->controllerNamespace;
        $blockNamespace=str_replace("controllers","blocks",$controllerNamespace);
        $controllerName=Yii::$app->controller->id;
        $action=Yii::$app->controller->action->id;
        if(class_exists($blockNamespace.'\\'.$controllerName.'\\'.$action)){
            return  Yii::createObject($blockNamespace.'\\'.$controllerName.'\\'.$action);
        }else{
            throw new HttpException(406,'block 为空');
        }
    }
}