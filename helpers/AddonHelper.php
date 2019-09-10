<?php


namespace yiifast\helpers;



use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use Yii;

class AddonHelper
{


    /** 已经时里化的组件
     * @var array
     */
    public static $addonModels=[];


    /** 初始化模块信息
     * @param $addonName
     * @param $route
     * @throws NotFoundHttpException
     */
    public static function initAddon($addonName,$route)
    {
        if(!$addonName){
            throw new NotFoundHttpException("模块不能为空");
        }
        Yii::$app->params['addonInfo'] = [
            'name' => $addonName,
            'oldRoute' => $route,
        ];
        Yii::$app->params['inAddon'] = true;
        return true;
    }


    /**
     * 获取模块的App路径名称
     */
    public static function getAppName($moduleId='')
    {
        $appId=[
            'app-frontend'=>"frontend",
            'app-backend'=>"backend",
        ];
        if(empty($moduleId)){
            $moduleId=Yii::$app->controller->module->id;
        }
        Yii::$app->params['addonInfo']['moduleId']=$appId[$moduleId];
        return isset($appId[$moduleId])?$appId[$moduleId]:'backend';
    }



}