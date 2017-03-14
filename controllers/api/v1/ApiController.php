<?php

namespace app\controllers\api\v1;

use Yii;
use yii\base\Event;
use yii\web\Response;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\filters\ContentNegotiator;
use app\models\ApiLog;
use app\models\ApiError;
use app\models\User;

abstract class ApiController extends Controller
{

    public $message = '';

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
        $apiLog = new ApiLog;
        $apiLog->writeBeforeApiAction();
        Yii::$container->set('apiLog', $apiLog);

        if (parent::beforeAction($action)) {
            // validata apiKey
            $this->validateApiKey();

            // validate sign
            if (!$this->validateApiSign()) {
                ApiError::throwException(ApiError::CODE_SIGN_FAILED);
            }
            // validate IP address
            if (!$this->validateIp()) {
                ApiError::throwException(ApiError::CODE_IP_FAILED);
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

        $output['error'] = ApiError::CODE_SUCCESS;
        $output['message'] = ApiError::getMessage(ApiError::CODE_SUCCESS);
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
            $error = $response->data['code'] ?: ApiError::convertHttpException($response->statusCode);
            $message = $response->data['message'] ?: Response::$httpStatuses[$response->statusCode];
            $response->data = [
                'error'     => strval($error),
                'message'   => $message,
                'data'      => [],
            ];
            $response->statusCode = 200;
        }

        // log return
        
        Yii::$container->get('apiLog')->writeAfterApiAction(Json::encode($response->data));
    }

    /**
     * validate ApiKey from request params (get and post)
     * @return bool true
     */
    public function validateApiKey()
    {
        $apiKey = Yii::$app->request->post('apiKey', Yii::$app->request->get('apiKey'));
        if (!$apiKey) {
            ApiError::throwException(ApiError::CODE_API_KEY_MISSING);
        }

        $identity = User::findByApiKey($apiKey);
        if (!$identity) {
            ApiError::throwException(ApiError::CODE_USER_NOT_EXISTS);
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
            ApiError::throwException(ApiError::CODE_SIGN_MISSING);
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
            ApiError::throwException(ApiError::CODE_IP_FAILED);
        }

        return true;
    }


}
