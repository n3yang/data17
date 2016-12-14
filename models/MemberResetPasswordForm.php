<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class MemberResetPasswordForm extends Model
{
    public $password;
    public $newPassword;
    public $rePassword;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password', 'newPassword', 'rePassword'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['newPassword', 'compare', 'compareAttribute' => 'rePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password'      => '原登录密码',
            'newPassword'   => '新登录密码',
            'rePassword'    => '重复输入新密码',
        ];
    }

    public function validatePassword()
    {
        $user = Yii::$app->user->identity;

        if (!$user->validatePassword($this->password)) {
            $this->addError('password', '原登录密码输入错误');
        }
    }

}