<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<?
if (!empty($message)) {
    echo Alert::widget([
        'options' => [
            'class' => $messageError ? 'alert-danger' : 'alert-success'
        ],
        'body' => $message,
    ]);
}
?>

<div class="security-form">

    <h3>添加业务标签</h3>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tags[]')->textInput(['value' => $tags[0]])->label('标签一') ?>
    <?= $form->field($model, 'tags[]')->textInput(['value' => $tags[1]])->label('标签二') ?>
    <?= $form->field($model, 'tags[]')->textInput(['value' => $tags[2]])->label('标签三') ?>
    <?= $form->field($model, 'tags[]')->textInput(['value' => $tags[3]])->label('标签四') ?>
    <?= $form->field($model, 'tags[]')->textInput(['value' => $tags[4]])->label('标签五') ?>

    <div class="form-group">
        <?= Html::submitButton('更新', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

