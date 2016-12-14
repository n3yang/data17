<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Log */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'level') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'url') ?>

    <?= $form->field($model, 'path_info') ?>

    <?= $form->field($model, 'http_get') ?>

    <?= $form->field($model, 'http_post') ?>

    <?= $form->field($model, 'rawdata') ?>

    <?= $form->field($model, 'output') ?>

    <?= $form->field($model, 'exec_time') ?>

    <?= $form->field($model, 'ip') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
