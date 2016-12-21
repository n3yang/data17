<?php

namespace app\controllers\api\datalink;

use Yii;
use yii\web\UnauthorizedHttpException;
use yii\helpers\Json;
use app\models\User;


abstract class ApiController extends \app\controllers\api\v1\ApiController
{

    const ERROR_NO_RESULT = 20001;

    /**
     * @inheritdoc
     */
    public function validateApiKey()
    {
        $apiKey = yii::$app->params['datalink']['userApiKey'];
        $identity = User::findByApiKey($apiKey);
        if (!$identity) {
            throw new UnauthorizedHttpException('User does not exists', static::ERROR_USER_NOT_EXISTS);
        }
        Yii::$app->user->login($identity);
    }

    /**
     * @inheritdoc
     */
    public function validateApiSign()
    {
        return true;

        $sign = Yii::$app->request->get('sign');
        if (!$sign) {
            throw new UnauthorizedHttpException('Signature is missing', static::ERROR_SIGN_MISSING);
        }

        $query = [
            'productid' => Yii::$app->request->get('productid'), // 产品编号（UUID）。
            'security'  => Yii::$app->request->get('security'), // 产品是否需要加密，由卖家定义。
            'nonce'     => Yii::$app->request->get('nonce'), // 平台产生的随机数，可以通过 nonce 接口验证 nonce 是否有效。
            'timestamp' => Yii::$app->request->get('timestamp'), // 时间戳。
            'appsecret' => Yii::$app->params['datalink']['appSecret'],
        ];
        ksort($query);
        $signString = http_build_query($query);
        var_dump($signString);
        var_dump(hash('sha1', $signString));
        var_dump($sign);
        exit;

        return hash('sha1', $signString) == $sign;
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

        }

        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function afterAction($action, $result)
    {
        $rs = parent::afterAction($action, $result);
        $rs = $rs['data'];
        
        if ($rs instanceof \Exception) {
            return [
                'dat' => [
                    'rows'  => [],
                    'total' => 0,
                ],
                'msg'   => $rs->getMessage(),
                'ret'   => $rs->getCode(),
            ];
        }
        
        return [
            'dat' => [
                'rows'  => $rs['rows'],
                'total' => $rs['total'],
            ],
            'msg' => $rs['msg'],
            'ret' => '10000', // 9999 程序异常报错, 10000 正常
            'ver' => '1',
        ];
    }

    public function formatDataBeforeSend($event)
    {
        // var_dump(Yii::$app->request->bodyParams);
        // var_dump(Yii::$app->request->post());
        // var_dump(Yii::$app->request->get());
        // var_dump(Yii::$app->request->getBodyParam('apiKey'));
        // var_dump(Yii::$app->request->userIP);
        // var_dump(Yii::$app->request->headers);
        // var_dump(Yii::$app->request->getRawBody());
        $response = $event->sender;
        if (is_array($response->data) && $response->statusCode != 200) {
            // var_dump($response);exit;
            //unset($response->data['type'], $response->data['status'], $response->data['name']);
            $error = $response->data['code'] ?: $response->statusCode;
            $response->data = [
                'ret'   => $error,
                'msg'   => $response->data['message'],
                'dat' => [
                    'rows'  => [],
                    'total' => 0,
                ],
                'ver'   => '1',
            ];
            $response->statusCode = 200;
        }

        // var_dump($response->statusCode);exit;

        // log return
        $this->log->writeAfterApiAction(Json::encode($response->data));
    }
}
