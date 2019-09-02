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
           require Yii::getAlias('@addons').'/'.$addon.'/'.AddonHelper::getAppName($config['id']).'/'.'config'.'/'.'bootstrap.php';
           require Yii::getAlias('@addons').'/'.$addon.'/common/'.'config'.'/'.'bootstrap.php',
            $config=ArrayHelper::merge(
            	$config,
            	require Yii::getAlias('@addons').'/'.$addon.'/common/'.'config'.'/'.'main.php',
            	require Yii::getAlias('@addons').'/'.$addon.'/'.AddonHelper::getAppName($config['id']).'/'.'config'.'/'.'main.php'
            );
        }
        parent::__construct($config);
    }

}