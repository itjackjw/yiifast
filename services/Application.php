<?php


namespace yiifast\services;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;


class Application
{

    /** 所有服务
     * @var array
     */
    public $childService;


    /** 已经实例化的服务
     * @var
     */
    public $_childService;


    /** 初始化所有的服务
     * Application constructor.
     * @param array $config
     */
    public function __construct($config=[])
    {
        Yii::$service=$this;

        if(!empty($addon=isset($_GET['addon'])?$_GET['addon']:$_POST['addon']) && !empty($config) && !empty($config['id'])){
            $addonConfig= require Yii::getAlias('@addons').'/'.$addon.'/'.AddonHelper::getAppName($config['id']).'/'.'config'.'/'.'services.php';
            $config=ArrayHelper::merge($config,$addonConfig);
        }


        $this->childService=$config;
    }


    /** 获取服务的实例
     * @param $serviceName
     */
    public function getChildService($serviceName)
    {
        if(!isset($this->_childService[$serviceName]) && !$this->_childService[$serviceName]){
            $childService=$this->childService;
            if(isset($childService[$serviceName])){
                $service=$childService[$serviceName];
                if(!isset($service['enableService']) || $service['enableService']){
                    $this->_childService[$serviceName]=Yii::createObject($service);
                }else{
                    throw new InvalidConfigException('Child Service ['.$childServiceName.'] does not exist in '.get_called_class().', you must config it! ');
                }
            }else{
                throw new InvalidConfigException('Child Service ['.$childServiceName.'] does not exist in '.get_called_class().', you must config it! ');
            }
        }
        return isset($this->_childService[$serviceName])?$this->_childService[$serviceName]:null;
    }


    /**当调用一个属性，对象不存在的时候会调用此方法
     * @param $serviceName
     */
    public function __get($serviceName)
    {
        return $this->getChildService($serviceName);
    }
}