<?php

namespace app\models;

use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use app\models\User;

/**
 * This is the model class for table "api_log".
 */
class ApiLog extends ActiveRecord
{
    const VERSION_1 = 'v1';
    const VERSION_DATALINK = 'datalink';

    private $execTimer = '';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_log';
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'version'       => 'API VERSION',
            'user_id'       => 'USER ID',
            'url'           => 'URL',
            'path_info'     => 'PATH INFO',
            'http_header'   => 'HTTP HEADER',
            'http_get'      => 'HTTP GET',
            'http_post'     => 'HTTP POST',
            'rawdata'       => '原始数据',
            'output'        => '返回数据',
            'exec_time'     => '执行时间 (ms)',
            'ip'            => 'IP地址',
            'created_at'    => '添加时间',
            'updated_at'    => '更新时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }

    public function writeBeforeApiAction()
    {
        $request = Yii::$app->request;
        $info = [
            'version'       => ApiLog::VERSION_1,
            'url'           => $request->url,
            'path_info'     => $request->pathInfo,
            'http_header'   => Json::encode($request->getHeaders()),
            'http_get'      => Json::encode($request->get()),
            'http_post'     => $request->getIsPost() && empty($request->post())
                                ? Json::encode($request->getRawBody()) 
                                : Json::encode($request->post()),
            'exec_time'     => 0,
            'ip'            => $request->userIP,
        ];

        $this->setAttributes($info, false);
        $this->execTimer = microtime(true);

        return $this;
    }

    /**
     * Records output data after api action
     * @param  string|array $output 
     * @return bool         
     */
    public function writeAfterApiAction($output = null)
    {
        $timer = (microtime(true) - $this->execTimer) * 1000;
        $this->exec_time = floatval(sprintf('%.2f', $timer));
        $this->output = $output;

        $identity = Yii::$app->user->identity;
        if ($identity) {
            $this->user_id = $identity->id;
        }

        return $this->save();
    }


}
