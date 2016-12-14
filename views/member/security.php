<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Alert;
use app\model\MemberResetPasswordForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<?
if (!empty($message)) {
    $class = $messageError ? 'alert-danger' : 'alert-success';
    echo Alert::widget([
        'options' => [
            'class' => $class,
        ],
        'body' => $message,
    ]);
}
?>

<div class="security-form">

    <h3>重置通讯密钥 [点击重置按钮，生成新的通讯密钥]</h3>

    <?php
    $form = ActiveForm::begin([
        'id' => '123',
        'method' => 'post'
    ]);
    ?>

    <?= $form->field($model, 'api_secret') ?>

    <p class="text-danger">如果 App Secret 泄露，可以通过重置更换，原 App Secret 将作废</p>

    <div class="form-group">
        <?= Html::hiddenInput('resetApiSecret', $value = '1'); ?>
        <?= Html::submitButton('重置', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<hr />

<div class="security-form">

    <h3>修改登录密码</h3>

    <?php $form = ActiveForm::begin(); ?>

    <?//= $form->textInput() ?>

    <?= $form->field($resetForm, 'password') ?>
    <?= $form->field($resetForm, 'newPassword') ?>
    <?= $form->field($resetForm, 'rePassword') ?>

    <div class="form-group">
        <?= Html::hiddenInput('resetPassword', $value = '1'); ?>
        <?= Html::submitButton('重置', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

