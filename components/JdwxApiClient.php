<?php
namespace app\components;

use Yii;
use yii\httpclient\Client;
use yii\helpers\Json;
use app\models\ApiError;
use app\models\MobileSegment;

/**
 * 京东万象平台调用客户端
 */
class JdwxApiClient extends BaseApiClient
{

    const ERROR_GET_MOBILE_CARRIER = '200005';
    const ERROR_PURCHASING = '200007';


    public static function getMessageByErrorCode($errorCode)
    {
        $trans = [
            self::ERROR_GET_MOBILE_CARRIER  => '不支持该手机号段',
            self::ERROR_PURCHASING          => '数据源查询错误',
        ];

        return $trans[$errorCode] ?: 'Unknown Error';
    }

    public function MobileIdent($mobile, $name, $idcard)
    {

        $jdUris = array(
            MobileSegment::CARRIER_MOBILE     => 'https://way.jd.com/Yodata/Chinamobile', // 移动三要素认证
            MobileSegment::CARRIER_UNICOM     => 'https://way.jd.com/Yodata/uincom', // 联通三要素认证
            MobileSegment::CARRIER_TELECOM    => 'https://way.jd.com/Yodata/telecom', // 电信三要素认证
        );

        // 不支持该手机号段
        $carrier = MobileSegment::findCarrier($mobile);
        if (!$carrier) {
            ApiError::throwException(
                self::ERROR_GET_MOBILE_CARRIER,
                self::getMessageByErrorCode(self::ERROR_GET_MOBILE_CARRIER)
            );
        }

        // get cache
        $cacheKey = 'JdwxClient::MobileIdent'.$name.$mobile.$idcard;
        $cacheData = $this->readCache($cacheKey);
        if (!empty($cacheData)) {
            $raw = $cacheData;
        } else {
            // fetch data
            $uri = $jdUris[$carrier];
            $query = [
                'name'      => $name,
                'phone'     => $mobile,
                'idCard'    => $idcard,
                'appkey'    => Yii::$app->params['jdwx']['apiKey'],
            ];

            $raw = $this->httpGet($uri, $query);
        }

        $data = Json::decode($raw);
        // 京东万象接口返回异常状态
        if (empty($data['code']) || $data['code'] != '10000') {
            ApiError::throwException(ApiError::CODE_PROCESSING);
        }

        // set cache
        $this->writeCache($cacheKey, $raw, 3600 * 24 * 7);

        // 数据源返回非计费成功状态
        if ($data['charge'] != true) {
            ApiError::throwException(
                self::ERROR_PURCHASING,
                $data['result']['msg'] ?: self::getMessageByErrorCode(self::ERROR_PURCHASING)
            );
        }
        // record charge flag
        $this->logChargeYes();
        
        // 统一输出
        if ($data['result']['code'] == '200') {
            $data['result']['code'] = '0';
            $data['result']['data']['result'] = 'N';
        }
        if ($carrier == MobileSegment::CARRIER_UNICOM) {
            unset($data['result']['data']['isTrue']);
            unset($data['result']['data']['isTrueMsg']);
            $data['result']['data']['result'] = $data['result']['data']['result'] == '00'
                ? 'T'
                : 'F';
        }

        // rebuild serial number
        unset($data['result']['serialno']);

        return $data['result'];
    }
}
