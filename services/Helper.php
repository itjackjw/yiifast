<?php


namespace yiifast\services;


class Helper extends Service
{
    /** 获取错误信息
     * @return mixed
     */
    public function getErrors()
    {
        return Yii::$service->helper->errors->getErrors();
    }
}