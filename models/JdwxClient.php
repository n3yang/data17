<?php
namespace app\models;

use yii;
// use yii\base\Model;
use yii\httpclient\Client;
use yii\helpers\Json;
use yii\base\UserException;

/**
 * 
 *
 */
class JdwxClient
{

    const ERROR_INVALID_PARAM = -100000;
    const ERROR_PROCESSING = -100001;
    const ERROR_PURCHASING = -200007;
    const ERROR_GET_MOBILE_CARRIER = -200005;

    private $errorCode = null;

    public function __construct()
    {
        
    }



    public static function getMessageByErrorCode($errorCode)
    {
        $trans = [
            self::ERROR_INVALID_PARAM       => '输入参数错误',
            self::ERROR_PROCESSING          => '数据源查询错误',
            self::ERROR_PURCHASING          => '数据源查询错误',
            self::ERROR_GET_MOBILE_CARRIER  => '不支持该手机号段',
        ];

        return $trans[$errorCode] ?: 'Unknown Error';
    }

    public static function MobileIdent($mobile, $name, $idcard)
    {

        $jdUris = array(
            MobileSegment::CARRIER_MOBILE     => 'https://way.jd.com/Yodata/Chinamobile', // 移动三要素认证
            MobileSegment::CARRIER_UNICOM     => 'https://way.jd.com/Yodata/uincom', // 联通三要素认证
            MobileSegment::CARRIER_TELECOM    => 'https://way.jd.com/Yodata/telecom', // 电信三要素认证
        );

        // 不支持该手机号段
        $carrier = MobileSegment::findCarrier($mobile);
        if (!$carrier) {
            throw new UserException(
                self::getMessageByErrorCode(self::ERROR_GET_MOBILE_CARRIER),
                self::ERROR_GET_MOBILE_CARRIER
            );
        }

        $uri = $jdUris[$carrier];
        $query = [
            'name'      => $name,
            'phone'     => $mobile,
            'idCard'    => $idcard,
            'appkey'    => Yii::$app->params['jdwx']['apiKey'],
        ];
        $client = new Client;
        $response = $client->get($uri, $query)->send();
        // log raw
        Yii::$app->controller->log->rawdata = $response->getContent();

        $data = $response->getData();
        // 京东万象接口返回异常状态
        if (empty($data['code']) || $data['code'] != '10000') {
            throw new UserException(
                self::getMessageByErrorCode(self::ERROR_PROCESSING),
                self::ERROR_PROCESSING
            );
        }

        // 数据源返回非计费成功状态
        if ($data['charge'] != true) {
            throw new UserException(
                $data['result']['msg'] ?: self::getMessageByErrorCode(self::ERROR_PURCHASING),
                self::ERROR_PURCHASING
            );
        }
        
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

        return $data['result'];
    }
}
