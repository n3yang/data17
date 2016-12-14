<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?// = $form->field($model, '_id') ?>

    <?// = $form->field($model, 'level') ?>

    <?= $form->field($model, 'user_id') ?>

    <?// = $form->field($model, 'url') ?>

    <?php echo $form->field($model, 'path_info') ?>

    <?php // echo $form->field($model, 'http_get') ?>

    <?php // echo $form->field($model, 'http_post') ?>

    <?php // echo $form->field($model, 'rawdata') ?>

    <?php echo $form->field($model, 'output') ?>

    <?php // echo $form->field($model, 'exec_time') ?>

    <?php echo $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php echo $form->field($model, 'output_error')->label('Error') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
