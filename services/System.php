<?php


namespace yiifast\services;

use Yii;

class System extends OnAuthService
{


    /** 验证是否是超级管理员
     * @return mixed
     */
    public function isSuperAdmin()
    {
        return Yii::$app->params['adminId'];
    }
}