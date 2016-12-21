<?php

namespace app\controllers\api\v1;

use Yii;
use yii\base\Event;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\filters\ContentNegotiator;
use app\models\User;
use app\models\ApiLog;

abstract class ApiController extends Controller
{

    const ERROR_USER_NOT_EXISTS = 40101;
    const ERROR_SIGN_MISSING = 40102;
    const ERROR_SIGN_FAILED = 40103;
    const ERROR_IP_FAILED = 40104;
    const ERROR_API_KEY_MISSING = 40105;

    public $message = '';
    /**
     * model log
     * @var app\models\ApiLog
     */
    public $log = '';

    public function init()
    {
        parent::init();
        // disable session
        Yii::$app->user->enableSession = false;

        // responsive data format
        $negotiator = new ContentNegotiator;
        $negotiator->formats = [
            'application/json' => Response::FORMAT_JSON,
            'application/xml' => Response::FORMAT_XML,
            // 'text/html' => Response::FORMAT_HTML,
        ];
        $negotiator->negotiate();

        /**
         * register response event: before send
         * format return data
         */
        Event::on(Response::className(), Response::EVENT_BEFORE_SEND, [$this, 'formatDataBeforeSend']);

    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // disable system authenticator, contentNegotiator
        unset($behaviors['authenticator'], $behaviors['contentNegotiator']);

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // log input
        $log = new ApiLog;
        $this->log = $log->writeBeforeApiAction();
        Yii::$app->params['api'] = &$this->log;

        if (parent::beforeAction($action)) {
            // validata apiKey
            $this->validateApiKey();

            // validate sign
            if (!$this->validateApiSign()) {
                throw new UnauthorizedHttpException('Signature failed', static::ERROR_SIGN_FAILED);
            }
            // validate IP address
            if (!$this->validateIp()) {
                throw new UnauthorizedHttpException('IP address failed', static::ERROR_SIGN_FAILED);
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterAction($action, $result)
    {
        $rs = parent::afterAction($action, $result);

        $output['error'] = 0;
        $output['message'] = $this->message;
        $output['data'] = $rs;

        return $output;
    }

    public function formatDataBeforeSend($event)
    {
        // var_dump(Yii::$app->request->bodyParams);
        // var_dump(Yii::$app->request->post());
        // var_dump(Yii::$app->request->get());
        // var_dump(Yii::$app->request->getBodyParam('apiKey'));
        // var_dump(Yii::$app->request->userIP);
        // var_dump(Yii::$app->request->headers);
        $response = $event->sender;
        if (is_array($response->data) && $response->statusCode != 200) {
            // var_dump($response);exit;
            //unset($response->data['type'], $response->data['status'], $response->data['name']);
            $error = $response->data['code'] ?: $response->statusCode;
            $response->data = [
                'error'     => intval($error),
                'message'   => $response->data['message'],
                'data'      => [],
            ];
            $response->statusCode = 200;
        }

        // log return
        $this->log->writeAfterApiAction(Json::encode($response->data));
    }

    /**
     * validate ApiKey from request params (get and post)
     * @return bool true
     */
    public function validateApiKey()
    {
        $apiKey = Yii::$app->request->post('apiKey', Yii::$app->request->get('apiKey'));
        if (!$apiKey) {
            throw new UnauthorizedHttpException('apiKey is missing', static::ERROR_API_KEY_MISSING);
        }

        $identity = User::findByApiKey($apiKey);
        if (!$identity) {
            throw new UnauthorizedHttpException('User does not exists', static::ERROR_USER_NOT_EXISTS);
        }
        Yii::$app->user->login($identity);

        return true;
    }

    /**
     * validate apiSign from request params (get and post)
     * @return bool truee
     */
    public function validateApiSign()
    {
        $query = array_merge(Yii::$app->request->get(), Yii::$app->request->post());

        $sign = $query['apiSign'];
        if (!$sign) {
            throw new UnauthorizedHttpException('Signature is missing', static::ERROR_SIGN_MISSING);
        }

        unset($query['apiSign'], $query['apiKey']);
        // make sign
        ksort($query);
        $signString = http_build_query($query) . '&apiSecret=' . Yii::$app->user->identity->api_secret;

        return md5($signString) == $sign;
    }

    public function validateIp()
    {
        if (0==1) {
            throw new \yii\web\ForbiddenHttpException('IP is not allowed', static::ERROR_IP_FAILED);
        }

        return true;
    }


}
