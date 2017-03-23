<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;
use yii\helpers\Json;
use app\models\ApiError;

/**
 * 捷安数据平台调用客户端
 */
class JieanApiClient extends BaseApiClient
{

    const TRANS_TYPE_MOBILE_IDENT = 'STD_VERI';

    public $custId;
    public $macStr;
    public $versionId = '01';
    public $chrSet = 'UTF-8';
    public $reqUrl = 'http://api.jieandata.com/vpre/ccmn/verify';
    
    function init()
    {
        parent::init();
        $this->custId = Yii::$app->params['jiean']['custId'];
        $this->macStr = Yii::$app->params['jiean']['macStr'];
    }

    private function generateOrdId()
    {
        return $this->custId . date('Ymd') . mt_rand(10000000, 99999999);
    }

    private function generateSign($postdata)
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
        
        // send query request
        $raw = $this->httpPost($this->reqUrl, $query);

        return $raw;
    }

    /**
     * 通过返回值 respCode 判断否计费     
     * @param  string  $respCodes 返回值
     * @return boolean            
     */
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
            $this->logChargeYes();
        }

        return $rs;
    }

}

