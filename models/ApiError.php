<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\UserException;
use yii\web\HttpException;
use yii\web\Response;

/**
* api errors
*/
class ApiError extends Model
{

    /**
     * 成功调用
     */
    const CODE_SUCCESS = '0';
    /**
     * 1 开头：平台级错误
     * 2 开头：业务逻辑错误，由业务自定义
     */
    const CODE_INVALID_PARAM = '100000';
    const CODE_PROCESSING = '100001';
    const CODE_NET_STAT = '100002';
    /**
     * 1401 开头：权限错误
     */
    const CODE_USER_NOT_EXISTS = '140101';
    const CODE_SIGN_MISSING = '140102';
    const CODE_SIGN_FAILED = '140103';
    const CODE_IP_FAILED = '140104';
    const CODE_API_KEY_MISSING = '140105';

    /**
     * 130 开头： HTTP 状态错误 的转译。 以下为举例，获取匹配 code 须调用 convertHttpException
     */
    const CODE_HTTP_000 = '130000';
    const CODE_HTTP_403 = '130403'; // Forbidden

    // const CODE_PURCHASING = '-200007';
    // const CODE_GET_MOBILE_CARRIER = '-200005';

    /**
     * get message by error code
     * @param  string $code error code
     * @return string       message
     */
    public static function getMessage($code = '')
    {
        $trans = [
            self::CODE_SUCCESS              => 'Success',

            self::CODE_INVALID_PARAM        => '输入参数错误',
            self::CODE_PROCESSING           => '数据源查询错误',
            self::CODE_NET_STAT             => '数据源查询错误',

            self::CODE_USER_NOT_EXISTS      => 'User does not exists',
            self::CODE_SIGN_MISSING         => 'Signature is missing',
            self::CODE_SIGN_FAILED          => 'Signature failed',
            self::CODE_IP_FAILED            => 'IP address failed',
            self::CODE_API_KEY_MISSING      => 'apiKey is missing',
            // self::CODE_PURCHASING          => '数据源查询错误',
            // self::CODE_GET_MOBILE_CARRIER  => '不支持该手机号段',
        ];

        return $trans[$code] ?: 'Unknown Error';
    }

    /**
     * throw new user exception
     * @param  string $code    error code
     * @param  string $message message
     */
    public static function throwException($code, $message = null)
    {
        $message = $message ?: self::getMessage($code);
        throw new UserException($message, $code);
    }

    /**
     * 将 yii2 的 HTTP exception code 转换为 api error code
     * @return string -130XXX
     */
    public static function convertHttpException($code)
    {
        // 非 HTTP status code 直接返回
        if ($code < 100 || $code >= 600 ) {
            return $code;
        }
        if (isset(Response::$httpStatuses[$code])) {
            $code = '130' . $code;
        } else {
            $code = static::CODE_HTTP_000;
        }

        return $code;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code'      => '错误代码',
            'message'   => '错误信息',
        ];
    }
}