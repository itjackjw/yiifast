<?php


namespace yiifast\froms;


use yii\base\Model;

abstract class LoginFrom extends Model
{

    /**用户名
     * @var
     */
    public $username;

    /**用户密码
     * @var
     */
    public $password;

    /** 记住自己
     * @var bool
     */
    public $rememberMe=true;


    /** 用户信息
     * @var
     */
    protected $_user;


    /**验证规则
     * @return array
     */
    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['password', 'validatePassword'],
            ['rememberMe','boolean']
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username'=>"用户名",
            'password'=>"密码"
        ];
    }


    /** 获取用户
     * @return mixed
     */
    abstract public function getUser();


    /** 验证用户密码
     * @param $attribute
     */
    public function validatePassword($attribute)
    {
        if(!$this->hasErrors()){
            $user=$this->getUser();
            if(!$user || !$user->validatePassword($this->password)){
                $this->addError($attribute,'账户或密码错误');
            }
        }
    }
    
}