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
            'version',
            'user_id',
            'url',
            'path_info',
            [
                // 'format' => 'ntext',
                'attribute' => 'http_get',
                'value' => $model->http_get
            ],
            [
                'attribute' => 'http_post',
                'value' => $model->http_post
            ],
            [
                'attribute' => 'http_header',
                'value' => $model->http_header
            ],
            [
                'attribute' => 'rawdata',
                'value' => $model->rawdata
            ],
            [
                'attribute' => 'output',
                'value' => $model->output
            ],
            'exec_time',
            'ip',
            'created_at:datetime',
            // 'updated_at',
        ],
    ]) ?>

</div>
