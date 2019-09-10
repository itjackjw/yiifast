<?php


namespace yiifast\controllers;


use yii\base\InvalidConfigException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yiifast\helpers\StringHelper;
use yiifast\services\BaseAction;

class BaseController extends ActiveController
{
    use BaseAction;



    public $controllerNamespace="";

    /** 活跃记录的模型
     * @var string
     */
    public $modelClass='';

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $optional = [];


    /** 不需要进行权限验证的方法
     *  例如：['index', 'update', 'create', 'view', 'delete']
     *  默认全部不需要进行权限验证
     * @var array
     */
    protected $noCheckAuth=[];

    /**
     * 默认每页数量
     *
     * @var int
     */
    protected $pageSize = 10;

    /**
     * 启始位移
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * 实际每页数量
     *
     * @var
     */
    protected $limit;

    /**
     * 行为验证
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 跨域支持
        $behaviors['class'] = Cors::class;
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                /**
                 * 下面是四种验证access_token方式
                 *
                 * 1.HTTP 基本认证: access token 当作用户名发送，应用在access token可安全存在API使用端的场景，例如，API使用端是运行在一台服务器上的程序。
                 * \yii\filters\auth\HttpBasicAuth::class,
                 *
                 * 2.OAuth : 使用者从认证服务器上获取基于OAuth2协议的access token，然后通过 HTTP Bearer Tokens 发送到API 服务器。
                 * header格式：Authorization:Bearer+空格+access-token
                 * yii\filters\auth\HttpBearerAuth::class,
                 *
                 * 3.请求参数 access token 当作API URL请求参数发送，这种方式应主要用于JSONP请求，因为它不能使用HTTP头来发送access token
                 * http://rageframe.com/user/index/index?access-token=123
                 *
                 * 4.请求参数 access token 当作API header请求参数发送
                 * header格式: X-Api-Key: access-token
                 * yii\filters\auth\HttpHeaderAuth::class,
                 */
                HttpBasicAuth::class,
                HttpBearerAuth::class,
                HttpHeaderAuth::class,
                [
                    'class' => QueryParamAuth::class,
                    'tokenParam' => 'access-token'
                ],
            ],
            // 不进行认证判断方法
            'optional' => $this->optional,
        ];

        // 进行签名验证，前提开启了签名验证
        $behaviors['signTokenValidate'] = [
            'class' => \common\behaviors\HttpSignAuth::class,
        ];

        /**
         * 请求速率控制
         *
         * limit部分，速度的设置是在common\models\common\RateLimit::getRateLimit($request, $action)
         * 当速率限制被激活，默认情况下每个响应将包含以下HTTP头发送 目前的速率限制信息：
         * X-Rate-Limit-Limit: 同一个时间段所允许的请求的最大数目;
         * X-Rate-Limit-Remaining: 在当前时间段内剩余的请求的数量;
         * X-Rate-Limit-Reset: 为了得到最大请求数所等待的秒数。
         * enableRateLimitHeaders：false: 不开启限制 true：开启限制
         */
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::class,
            'enableRateLimitHeaders' => true,
        ];

        return $behaviors;
    }


    /**
     * 前置操作验证token有效期和记录日志和检查curd权限
     *
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        // 判断验证token有效性是否开启
        if (Yii::$app->params['user.accessTokenValidity'] == true)
        {
            $token = Yii::$app->request->get('access-token');
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['user.accessTokenExpire'];

            // 验证有效期
            if ($timestamp + $expire <= time() && !in_array($action->id, $this->optional))
            {
                throw new BadRequestHttpException('您的登录验证已经过期，请重新登陆');
            }
        }



        // 分页
        $page = Yii::$app->request->get('page', 1);
        $this->limit = Yii::$app->request->get('per-page', $this->pageSize);
        $this->limit > 100 && $this->limit = 100;
        $this->offset = ($page - 1) * $this->pageSize;


        if(!empty($this->noCheckAuth) && is_array($this->noCheckAuth)){
            if(in_array("*",$this->noCheckAuth) || in_array($action->id,$this->noCheckAuth)){
                return true;
            }
        }

        $this->checkAccess($action->id, $this->modelClass, Yii::$app->request->get());

        return true;
    }

    /**
     * 解析错误
     *
     * @param $fistErrors
     * @return string
     */
    public function analyErr($firstErrors)
    {
        return Yii::$app->debris->analyErr($firstErrors);
    }

    /** 获取块应用的处理数据
     * @return mixed
     */
    public function getBlock($blockname='')
    {
        if(!$blockname){
            $blockname=$this->action->id;
        }


        $controllerNamespace=Yii::$app->controller->module->controllerNamespace;
        $blockNamespace=str_replace("controllers","blocks",$controllerNamespace);

        $controllerName=Yii::$app->controller->id;

        $controllerName=str_replace(array('/','-'),array('\\','_'),$controllerName);

        $action=Yii::$app->controller->action->id;
        $blockname=StringHelper::strUcwords($blockname);

        if(class_exists($blockNamespace.'\\'.$controllerName.'\\'.$blockname)){
            return  Yii::createObject($blockNamespace.'\\'.$controllerName.'\\'.$blockname);
        }else{
            throw new HttpException(406,'block 为空'.$controllerNamespace);
        }
    }

    
    /**充值系统默认的动作
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['view'], $actions['delete']);
        // 自定义数据indexDataProvider覆盖IndexAction中的prepareDataProvider()方法
        // $actions['index']['prepareDataProvider'] = [$this, 'indexDataProvider'];
        return $actions;
    }
}