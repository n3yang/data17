<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?//= Html::a('Create Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            'created_at:datetime',
            // 'id',
            // 'level',
            'user.username',
            'user_id',
            // 'api_key',
            // 'url',
            'path_info',
            // 'http_get',
            // 'http_post',
            // 'rawdata',
            // 'output',
            'exec_time',
            // 'ip',
            // 'updated_at',
            [
                'label' => 'Error',
                'format' => 'raw', 
                'value' => function($model){
                    $output = Json::decode($model->output);
                    return $output['error'] ?: null;
                }
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
        'rowOptions' => function ($model, $key, $index, $grid){
            $output = Json::decode($model->output, true);
            if ($output['error'] != '0') {
                return ['class' => 'warning'];
            } else {
                return ['class' => 'success'];
            }
        },
    ]); ?>


    <div>
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>
