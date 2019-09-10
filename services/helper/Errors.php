<?php


namespace yiifast\services\helper;


use BaconQrCode\Common\Mode;
use yii\base\Model;
use yiifast\services\Service;

class Errors extends Service
{


    protected $_errors = false;

    public function addModelErrors(Model $model)
    {
       if(!empty(($errors=$model->getFirstErrors()))){
           $this->_errors=array_values($errors)[0];
       }
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}