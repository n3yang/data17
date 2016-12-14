<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            ['attribute' => 'group', 'value' => function($model){return $model->getGroupLabel($model->group);}],
            'username',
            // 'password',
            // 'auth_key',
            'company',
            // 'tel',
            // 'tags',
            // 'api_key',
            // 'api_secret',
            ['attribute' => 'status', 'value' => function($model){return $model->getStatusLabel($model->status);}],
            'created_at:datetime',
            'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
