<?php


namespace yiifast\services;


use yii\base\BaseObject;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

class Service extends BaseObject
{

    use BaseAction;



    /** 数据模型
     * @var string
     */
    public $modelClass='';


    /** 所有的服务
     * @var
     */
    public $childService;

    /** 是否启用此服务
     * @var bool
     */
    public $enableService = true;


    /** 已经实例化的服务
     * @var
     */
    protected $_childService;


    /** 已经回调过的方法
     * @var
     */
    protected $_callFunLog;


    /** 魔术方法，当调用一个属性，对象不存在的时候会调用此方法
     * @param string $serviceName
     * @return mixed|void
     * @throws InvalidConfigException
     */
    public function __get($serviceName)
    {
        return $this->getChildService($serviceName);
    }


    /** 通过服务的名字
     * @param $serviceName
     */
    public function getChildService($serviceName)
    {
        if(!isset($this->_childService[$serviceName]) && !$this->_childService[$serviceName]){
            $childService=$this->childService;
            if(isset($childService[$serviceName])){
                $service=$childService[$serviceName];
                if(!isset($service['enableService']) || $service['enableService']){
                    $this->_childService[$serviceName]=\Yii::createObject($service);
                }else{
                    throw new InvalidConfigException('Child Service ['.$childServiceName.'] does not exist in '.get_called_class().', you must config it! ');
                }

            }else{
                throw new InvalidConfigException('Child Service ['.$childServiceName.'] does not exist in '.get_called_class().', you must config it! ');
            }

        }

        return isset($this->_childService[$serviceName])?$this->_childService[$serviceName]:null;
    }


    /** 通过call 来调用方法
     * @param string $originMethod
     * @param array $arguments
     * @return mixed|void
     */
    public function __call($originMethod,$arguments)
    {
        if(isset($this->_callFunLog[$originMethod])){
            $method=$this->_callFunLog[$originMethod];
        }else{
            //$method="action".ucfirst($originMethod);
            $method=$originMethod;
            $this->_callFunLog[$originMethod]=$method;
        }
        if(method_exists($this,$method)){
            $return=call_user_func_array([$this,$method],$arguments);
            return $return;
        }else{
            throw new InvalidCallException('yiifast service method is not exit.  '.get_class($this)."::$method");
        }
    }


    /**
     * 更新树结构
     */
    public function updateTree(ActiveRecord $activeRecord)
    {
        $activeRecord->tree="".$activeRecord->id;
        if($activeRecord->pid!=0 && $parent = $activeRecord->parent){
            $activeRecord->tree=$parent->tree.",".$activeRecord->id;
        }
        $activeRecord->save();
    }

    /**获取模型
     * @param $id
     * @return mixed
     */
    public function findByModel($id="")
    {
        if(empty($id) || empty(($model=$this->modelClass::findOne($id)))){
            $model=new $this->modelClass;
            return $model->loadDefaultValues();
        }
        return $model;
    }


    /** 增加和修改数据
     * @param $data
     * @return mixed
     */
    public function save($data,$extraData=array())
    {
        $model=$this->findByModel($data['id']);
        if(is_array($extraData) && !empty($extraData)){
            foreach ($extraData as $key=>$val){
                $model->$key=$val;
            }
        }
        $model->attributes=$data;
        if($model->validate() && $model->save()){
            return $model->attributes;
        }
        Yii::$service->helper->errors->addModelErrors($model);
        return false;
    }


}