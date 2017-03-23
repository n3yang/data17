<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\caching\Cache;
use yii\helpers\Json;
use yii\httpclient\Client;

use app\models\ApiLog;
use app\models\ApiError;

/**
* API调用基础类，封装常用操作
*/
class BaseApiClient extends Component
{

    /**
     * yii http client
     * @var Client
     */
    public $client = null;

    /**
     * app model ApiLog
     * @var ApiLog
     */
    private $apiLog = null;

    /**
     * @var Cache
     */
    private $cache = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->client = new Client;
    }


    public function getCache()
    {
        if (($this->cache instanceof Cache) === false) {
            $this->cache = Yii::$app->cache;
        }
        
        return $this->cache;
    }

    /**
     * 从缓存获取数据。自动将缓存标示及缓存内容写入 ApiLog
     * @param mixed $key a key identifying the cached value.
     * @return mixed the value stored in cache, false if the value is not in the cache
     */
    public function readCache($key)
    {
        $value = $this->getCache()->get($key);
        if ($value !== false) {
            $this->logCacheYes();
            $this->logQueryResult($value);
        }

        return $value;
    }

    /**
     * Stores a value identified by a key into cache.
     * @param mixed $key a key identifying the value to be cached.
     * @param mixed $value the value to be cached
     * @param int $duration default duration in seconds before the cache will expire.
     * @param Dependency $dependency dependency of the cached item.
     * @return bool whether the value is successfully stored into cache
     */
    public function writeCache($key, $value, $duration = null, $dependency = null)
    {
        return $this->getCache()->set($key, $value, $duration, $dependency);
    }

    /**
     * get ApiLog model
     * @return ApiLog ApiLog model
     */
    public function getApiLog()
    {
        if ($this->apiLog instanceof ApiLog) {
            return $this->apiLog;
        }

        if (Yii::$container->get('apiLog') instanceof Apilog) {
            $this->apiLog = Yii::$container->get('apiLog');
        } else {
            $this->apiLog = new ApiLog;
        }

        return $this->apiLog;
    }

    /**
     * ApiLog 记录请求结果
     * @param  string $content 
     */
    public function logQueryResult($content)
    {
        $this->getApiLog()->rawdata = $content;
    }

    /**
     * ApiLog 记录此条数据收费
     */
    public function logChargeYes()
    {
        $this->getApiLog()->setChargeYes();
    }

    /**
     * ApiLog 记录此条数据来自缓存
     */
    public function logCacheYes()
    {
        $this->getApiLog()->setCacheYes();
    }

    /**
     * Send 'POST' request and log returned content
     * @param string $url target URL.
     * @param array|string $data if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return string response content
     */
    public function httpPost($url, $data = null, $headers = [], $options = [])
    {
        try {
            $response = $this->client->post($url, $data, $headers, $options)->send();
        } catch (\Exception $e) {
            ApiError::throwException(ApiError::CODE_NET_STAT);
        }
        $this->logQueryResult($response->getContent());

        return $response->getContent();
    }

    /**
     * Send 'GET' request and log returned content
     * @param string $url target URL.
     * @param array|string $data if array - request data, otherwise - request content.
     * @param array $headers request headers.
     * @param array $options request options.
     * @return string response content
     */
    public function httpGet($url, $data = null, $headers = [], $options = [])
    {
        try {
            $response = $this->client->get($url, $data, $headers, $options)->send();
        } catch (\Exception $e) {
            ApiError::throwException(ApiError::CODE_NET_STAT);
        }
        $this->logQueryResult($response->getContent());

        return $response->getContent();
    }

    /**
     * Convert simple XML to array
     * @param  string $xml xml string
     * @return array       array
     */
    public static function xmlToArray($xml = '')
    {
        return Json::decode(Json::encode(simplexml_load_string($xml)));
    }


}