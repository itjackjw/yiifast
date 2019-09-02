<?php


namespace yiifast\behaviors;


use Yii;
use yii\base\Behavior;
use yii\web\Response;

class BeforeSend extends Behavior
{
    public function events()
    {
        return [
            'beforeSend'=>'beforeSend'
        ];
    }



    public function beforeSend($event)
    {
        $response=$event->sender;
        $response->data=[
            'code'=>$response->statusCode,
            'message'=>$response->statusText,
            'data'=>$response->data
        ];

        $errData=Yii::$service->errorLog->record($response, true);
        // 格式化报错输入格式
        if ($response->statusCode >= 500)
        {
            $response->data['data'] = YII_DEBUG ? $errData : '内部服务器错误,请联系管理员';
        }

        // 提取系统的报错信息
        if ($response->statusCode >= 300 && isset($response->data['data']['message']) && isset($response->data['data']['status']))
        {
            $response->data['message'] = $response->data['data']['message'];
        }
        
        $response->format = Response::FORMAT_JSON;
        $response->statusCode = 200; // 考虑到了某些前端必须返回成功操作，所以这里可以设置为都返回200的状态码
    }
}