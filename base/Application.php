<?php


namespace yiifast\base;


use yii\helpers\ArrayHelper;
use Yii;
use yiifast\helpers\AddonHelper;

class Application extends \yii\web\Application
{

    public function __construct($config = [])
    {
        if(!empty($addon=isset($_GET['addon'])?$_GET['addon']:$_POST['addon']) && !empty($config) && !empty($config['id'])){
            $addonConfig= require Yii::getAlias('@addons').'/'.$addon.'/'.AddonHelper::getAppName($config['id']).'/'.'config'.'/'.'main.php';
            $config=ArrayHelper::merge($config,$addonConfig);
        }
        parent::__construct($config);
    }

}