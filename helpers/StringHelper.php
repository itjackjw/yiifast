<?php


namespace yiifast\helpers;


use yii\helpers\BaseStringHelper;

class StringHelper extends BaseStringHelper
{


    /** 字符首字母转换大小写
     * @param $str
     * @return mixed
     */
    public static function strUcwords($str)
    {
        return str_replace(' ','',ucwords(str_replace("-",' ',trim($str))));
    }
}