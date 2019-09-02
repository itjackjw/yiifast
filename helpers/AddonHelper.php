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
        /*if(isset(self::$addonModels[$addonName])){
            $addonModel=self::$addonModels[$addonName];
        }else{
            if(!($addonModel=Yii::$app->cache->get('yiifast-addon:'.$addonName))){

            }
            self::$addonModels[$addonName]=$addonModel;
        }*/

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
            'app-frontend'=>"frontend"
        ];
        if(empty($moduleId)){
            $moduleId=Yii::$app->controller->module->id;
        }
        Yii::$app->params['addonInfo']['moduleId']=$appId[$moduleId];
        return isset($appId[$moduleId])?$appId[$moduleId]:'backend';
    }


    /**解析路由
     * @param $route
     * @param $module
     */
    public static function analysisRoute($route,$module)
    {
        if(!$route){
            throw  new NotFoundHttpException('模块路由不能为空');
        }
        $route=explode("/",$route);
        $oldroute=$route;
        if(($countRoute=count($route))<2){
            throw new NotFoundHttpException("路由解析错误，请检查路由");
        }
        $oldController=$route[$countRoute-2];
        $oldAction=$route[$countRoute-1];
        $controller=StringHelper::strUcwords($oldController);
        $action=StringHelper::strUcwords($oldAction);

        $controllerName=$controller."Controller";
        $addonRootPath='\\'.'addons'.'\\'.Yii::$app->params['addonInfo']['name'];

        $configpath=Yii::getAlias('@addons').'/'.Yii::$app->params['addonInfo']['name'].'/'.Yii::$app->params['addonInfo']['moduleId'].'/config/main.php';
        $config=require($configpath);
        if(isset($config['modules'][$oldroute[0]])){
            unset($route[$countRoute-1],$route[$countRoute-2]);
            unset($route[0]);
            $controllerPath=!empty($route)?implode('\\',$route):'';
            !empty($controllerPath) && $controllerPath.='\\';
            $controllersPath=$addonRootPath.'\\'.$module.'\\'.'modules'.'\\'.$oldroute[0].'\\'.'controllers'.'\\'.$controllerPath.$controllerName;
        }else{
            unset($route[$countRoute-1],$route[$countRoute-2]);
            $controllerPath=!empty($route)?implode('\\',$route):'';
            !empty($controllerPath) && $controllerPath.='\\';
            $controllersPath=$addonRootPath.'\\'.$module.'\\'.'controllers'.'\\'.$controllerPath.$controllerName;

        }



        $tmpInfo=[
            'oldController'=>$oldController,
            'oldAction'=>$oldAction,
            'controller'=>$controller,
            'action'=>$action,
            'rootPath'=>$addonRootPath,
            'rootAbsolutePath'=>Yii::getAlias('@addons').'/'.Yii::$app->params['addonInfo']['name'],
            'controllersPath'=>$controllersPath

        ];

        /*if(!class_exists($tmpInfo['controllersPath'])){
            throw new NotFoundHttpException('页面未找到。'.$controllersPath);
        }*/
        Yii::$app->params['addonInfo'] = \yii\helpers\ArrayHelper::merge(Yii::$app->params['addonInfo'], $tmpInfo);
        unset($tmpInfo, $addonRootPath, $controllerName, $controller, $action, $controllerPath);
        return true;
    }

}