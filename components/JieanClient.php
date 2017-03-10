<?php
namespace app\components;

use Yii;
use yii\httpclient\Client;
use yii\helpers\Json;

/**
* 捷安数据平台调用客户端
*/
class JieanClient
{

    const ERROR_INVALID_PARAM = -100000;
    const ERROR_PROCESSING = -100001;
    const ERROR_NET_STAT = -100002;
    const ERROR_PURCHASING = -200007;
    const ERROR_GET_MOBILE_CARRIER = -200005;

    const TRANS_TYPE_MOBILE_IDENT = 'STD_VERI';

    private $custId;
    private $macStr;
    private $versionId = '01';
    private $chrSet = 'UTF-8';
    
    function __construct()
    {
        // $this->custId = Yii::$app->params['jiean']['custId'];
        // $this->macStr = Yii::$app->params['jiean']['macStr'];
    }
    public static function getMessageByErrorCode($errorCode)
    {
        $trans = [
            self::ERROR_INVALID_PARAM       => '输入参数错误',
            self::ERROR_PROCESSING          => '数据源查询错误',
            self::ERROR_NET_STAT            => '数据源查询错误',
            self::ERROR_PURCHASING          => '数据源查询错误',
            self::ERROR_GET_MOBILE_CARRIER  => '不支持该手机号段',
        ];

        return $trans[$errorCode] ?: 'Unknown Error';
    }
    private function generateOrdId()
    {
        return $this->custId . date('Ymd') . mt_rand(10000000, 99999999);
    }

    private static function generateSign($postdata)
    {
        return strtoupper(md5(implode('', $postdata) . $this->macStr));
    }

    private function sendQuery($transType, $jsonStr)
    {
        $query = [
            'versionId' => $this->versionId, // 定长 2 位 , 必填, 01
            'chrSet'    => $this->chrSet, // 变长 8 位 , 必填, UTF-8
            'custId'    => $this->custId, //变长 12 位 , 必填, 客户号,由平台分配给商户的唯一标识
            'ordId'     => $this->generateOrdId(), //变长 30 位 , 必填, 订单号,由商户系统产生的交易请求唯一标识码,不能含有中午字符 组成规则:商户简称+YYYYMMDD+流水号。
            'transType' => $transType, // 变长 12 位 , 必填, STD_VERI
            'busiType'  => '', // 变长 8 位 , 非必填, 业务类型,可以为空,由商户自己定义,用于区分订单类型
            'merPriv'   => '', // 变长 120 位 , 非必填, 商户私有域,可以为空,由商户自己定义,平台原样返回
            'retUrl'    => '', // 变长 160 位 , 非必填, 返回 URL 地址,可以为空,如果不为空,平台将交易响应异步通过该地址返回
            'jsonStr'   => $jsonStr, // 变长 999 位 , 必填, Json 字符串,包含和交易相关的请求数据,为 json 格式,详情参考 2.3 jsonStr 数据域详解
        ];
        // generate signature
        $sign = $this->generateSign($query);
        $query['macStr'] = $sign;
        
        // send query
        $client = new Client;
        try {
            $response = $client->post($this->reqUrl, $query)->send();
        } catch (\Exception $e) {
            throw new UserException(
                self::getMessageByErrorCode(self::ERROR_NET_STAT),
                self::ERROR_NET_STAT
            );
        }
        $raw = $response->getContent();
        Yii::$container->get('apiLog')->rawdata = $raw;

        return $raw;
    }

    public function isCharge($respCodes)
    {
        $successCodes = [
            '000', // 一致 收费
            '042', // 不一致 收费
            '043', // 名字不匹配 收费
            '044', // 身份证号不匹配 收费
            '308', // 系统无记录 收费
        ];

        return in_array($respCodes, $successCodes);
    }

    public function mobileIdent($mobile, $name, $idcard)
    {
        $params = [
            'CERT_ID'   => $idcard, // 身份证号
            'CERT_NAME' => $name, // 身份证名字
            'MP'        => $mobile, // 手机号
            'PROD_ID'   => 'MP3', // 产品代号 12 MP3
        ];

        $jsonStr = Json::encode($params);

        $raw = $this->sendQuery(static::TRANS_TYPE_MOBILE_IDENT, $jsonStr);

        $rs = static::xmlToArray($raw);
        if ($this->isCharge($rs['respCode'])) {
            Yii::$container->get('apiLog')->setChargeYes();
        }

        return $rs;
    }



    public function run()
    {

        $item = [
            'CERT_ID'   => '360721199001158735', // 身份证号
            'CERT_NAME' => '陈玉禄', // 身份证名字
            'MP'        => '18210021353', // 手机号
            'PROD_ID'   => 'MP3', // 产品代号 12 MP3
        ];

        $jsonStr = json_encode($item, JSON_UNESCAPED_UNICODE);

        $postdata = [
            'versionId' => '01', // 定长 2 位 , 必填, 01
            'chrSet' => 'UTF-8', // 变长 8 位 , 必填, UTF-8
            'custId' => $this->custId, //变长 12 位 , 必填, 客户号,由平台分配给商户的唯一标识
            'ordId' => $this->generateOrdId(), //变长 30 位 , 必填, 订单号,由商户系统产生的交易请求唯一标识码,不能含有中午字符 组成规则:商户简称+YYYYMMDD+流水号。
            'transType' => 'STD_VERI', // 变长 12 位 , 必填, STD_VERI
            'busiType' => '', // 变长 8 位 , 非必填, 业务类型,可以为空,由商户自己定义,用于区分订单类型
            'merPriv' => '', // 变长 120 位 , 非必填, 商户私有域,可以为空,由商户自己定义,平台原样返回
            'retUrl' => '', // 变长 160 位 , 非必填, 返回 URL 地址,可以为空,如果不为空,平台将交易响应异步通过该地址返回
            'jsonStr' => $jsonStr, // 变长 999 位 , 必填, Json 字符串,包含和交易相关的请求数据,为 json 格式,详情参考 2.3 jsonStr 数据域详解
            // 'macStr' => $this->macStr, // 定长 32 位 必填, 签名串,组成规则参见 2.2.2 数据安全
        ];

        $sign = strtoupper(md5(implode('', $postdata) . $this->macStr));
        $postdata['macStr'] = $sign;
        var_dump($postdata);

        $requri = 'http://api.jieandata.com/vpre/ccmn/verify';

        $curl = curl_init($requri);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $result = curl_exec($curl);
        curl_close($curl);

        var_dump($result);
    }

    public static function xmlToArray($xml='')
    {
        return Json::decode(Json::encode(simplexml_load_string($xml)));
    }

}

// $j = new JieanClient;
// $j->run();
// $j->toArray();
