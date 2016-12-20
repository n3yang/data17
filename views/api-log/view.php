<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Log */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <!--
        <?= Html::a('Update', ['update', 'id' => (string)$model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => (string)$model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        -->
        <?= Html::a('返回', Yii::$app->request->referrer, ['class' => 'btn btn-primary']); ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'level',
            'user_id',
            'url',
            'path_info',
            [
                // 'format' => 'ntext',
                'attribute' => 'http_get',
                'value' => var_export($model->http_get, JSON_UNESCAPED_UNICODE)
            ],
            [
                'attribute' => 'http_post',
                'value' => var_export($model->http_post, JSON_UNESCAPED_UNICODE)
            ],
            [
                'attribute' => 'rawdata',
                'value' => var_export($model->rawdata, JSON_UNESCAPED_UNICODE)
            ],
            [
                'attribute' => 'output',
                'value' => var_export($model->output, JSON_UNESCAPED_UNICODE)
            ],
            'exec_time',
            'ip',
            'created_at:datetime',
            // 'updated_at',
        ],
    ]) ?>

</div>
