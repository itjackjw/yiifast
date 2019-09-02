<?php



$dir= __DIR__.'/../../../yiisoft/yii2';
require $dir."/BaseYii.php";




class Yii extends \yii\BaseYii
{
    public static $service;
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require $dir.'/classes.php';
Yii::$container = new yii\di\Container();