<?php

namespace app\controllers;

use yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\models\User;
use app\models\MemberResetPasswordForm;

class MemberController extends \yii\web\Controller
{
    public $layout = 'admin';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    $this->redirect('/');
                },
            ],
        ];
    }

    public function actionInfo()
    {
        $user = Yii::$app->user->identity;
        return $this->render('info', [
            'model' => $user,
        ]);
    }

    public function actionSecurity()
    {
        $user = Yii::$app->user->identity;

        if (Yii::$app->request->post('resetApiSecret') == '1') {
            $user->generateApiSecret();
            if ($user->save()) {
                $message = 'API SECRET 已重置';
            }
        }

        $resetForm = new MemberResetPasswordForm;

        if (Yii::$app->request->post('resetPassword') == '1') {
            $resetForm->load(Yii::$app->request->post());
            if ($resetForm->validate()) {
                $user->setPassword($resetForm->newPassword);
                $user->save();
                $message = '登录密码已更新';
            } else {
                $message = implode($resetForm->getFirstErrors(), "<br />");
                $messageError = true;
            }
        }

        return $this->render('security', [
            'model' => $user,
            'resetForm' => $resetForm,
            'message' => $message,
            'messageError' => $messageError,
        ]);
    }

    public function actionTag()
    {
        $model = Yii::$app->user->identity;
        
        if (Yii::$app->request->post()) {
            $tags = Yii::$app->request->post('User')['tags'];
            foreach($tags as $k => $v) {
                if (empty($v))
                    unset($tags[$k]);
            }
            $model->tags = implode(",", $tags);
            $model->save();
        }

        $tags = explode(',', $model->tags);

        return $this->render('tag', ['model' => $model, 'tags' => $tags]);
    }

    public function actionApiGuide()
    {
        return $this->render('api-guide');
    }
}
