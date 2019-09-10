<?php


namespace yiifast\controllers;


use yii\rest\ActiveController;
use yiifast\helpers\AddonHelper;
use yiifast\helpers\StringHelper;
use Yii;

class AddonsController extends ActiveController
{


    public $modelClass='';

    /** 访问路由
     * @var
     */
    public $route;

    /** 插件名称
     * @var
     */
    public $addonName;


    public function init()
    {
        parent::init();
        $this->route=Yii::$app->request->get('route',Yii::$app->request->post('route'));
        $this->addonName=Yii::$app->request->get('addon',Yii::$app->request->post('addon'));
        $this->addonName=StringHelper::strUcwords($this->addonName);
    }

    /**
     * 执行应用的基本数据
     */
    public function actionExecute()
    {
        //TODO初始化应用的配置数据
        AddonHelper::initAddon($this->addonName, $this->route);
        $params = Yii::$app->request->get();
        return $this->run($this->route,$params);
    }
}