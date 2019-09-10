<?php


namespace yiifast\backend\controllers;


use common\helpers\AuthHelper;
use yii\web\UnauthorizedHttpException;
use yiifast\controllers\OnAuthController;
use Yii;
use yiifast\helpers\AddonHelper;

class BaseController extends \yiifast\controllers\BaseController
{


    /** 权限验证
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws UnauthorizedHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        //验证所有的权限
        $appid=AddonHelper::getAppName(Yii::$app->id);
        $route=Yii::$app->controller->route;
        $authRule="";
        $addonName="default";
        if(Yii::$app->params['inAddon']){
            //获取应用的实际路由数据
            $addonName=Yii::$app->params['addonInfo']['name'];
            $route=Yii::$app->params['addonInfo']['oldRoute'];
        }
        $authRule.=$addonName."_".$appid."_".$route;
        if(AuthHelper::verify($authRule)){
            return true;
        }
        throw new UnauthorizedHttpException("没有权限操作数据");
    }
}