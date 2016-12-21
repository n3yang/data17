<?php
namespace app\models;

/**
* 手机运营商号段解析
*/
class MobileSegment
{

	/**
	 * 中国移动
	 */
	const CARRIER_MOBILE = 3;

	/**
	 * 中国联通
	 */
	const CARRIER_UNICOM = 2;

	/**
	 * 中国电信
	 */
	const CARRIER_TELECOM = 1;

	/**
	 * 手机号段字典
	 * @var array
	 */
	public static $mobileSegmentDict = [
		'175'		=> self::CARRIER_UNICOM,
		'1703'		=> self::CARRIER_MOBILE,
		'1706'		=> self::CARRIER_MOBILE,
		'10649'		=> self::CARRIER_TELECOM,
		'1701'		=> self::CARRIER_TELECOM,
		'1702'		=> self::CARRIER_TELECOM,
		'173'		=> self::CARRIER_TELECOM,
		'1707'		=> self::CARRIER_UNICOM,
		'1708'		=> self::CARRIER_UNICOM,
		'171'		=> self::CARRIER_UNICOM,
		'172'		=> self::CARRIER_MOBILE,
		'184'		=> self::CARRIER_MOBILE,
		'130'		=> self::CARRIER_UNICOM,
		'131'		=> self::CARRIER_UNICOM,
		'132'		=> self::CARRIER_UNICOM,
		'145'		=> self::CARRIER_UNICOM,
		'155'		=> self::CARRIER_UNICOM,
		'156'		=> self::CARRIER_UNICOM,
		'1709'		=> self::CARRIER_UNICOM,
		'176'		=> self::CARRIER_UNICOM,
		'185'		=> self::CARRIER_UNICOM,
		'186'		=> self::CARRIER_UNICOM,
		'133'		=> self::CARRIER_TELECOM,
		'142'		=> self::CARRIER_TELECOM,
		'144'		=> self::CARRIER_TELECOM,
		'146'		=> self::CARRIER_TELECOM,
		'148'		=> self::CARRIER_TELECOM,
		'149'		=> self::CARRIER_TELECOM,
		'153'		=> self::CARRIER_TELECOM,
		'1700'		=> self::CARRIER_TELECOM,
		'177'		=> self::CARRIER_TELECOM,
		'180'		=> self::CARRIER_TELECOM,
		'181'		=> self::CARRIER_TELECOM,
		'189'		=> self::CARRIER_TELECOM,
		'134'		=> self::CARRIER_MOBILE,
		'135'		=> self::CARRIER_MOBILE,
		'136'		=> self::CARRIER_MOBILE,
		'137'		=> self::CARRIER_MOBILE,
		'138'		=> self::CARRIER_MOBILE,
		'139'		=> self::CARRIER_MOBILE,
		'140'		=> self::CARRIER_MOBILE,
		'147'		=> self::CARRIER_MOBILE,
		'150'		=> self::CARRIER_MOBILE,
		'151'		=> self::CARRIER_MOBILE,
		'152'		=> self::CARRIER_MOBILE,
		'154'		=> self::CARRIER_MOBILE,
		'157'		=> self::CARRIER_MOBILE,
		'158'		=> self::CARRIER_MOBILE,
		'159'		=> self::CARRIER_MOBILE,
		'165'		=> self::CARRIER_MOBILE,
		'1705'		=> self::CARRIER_MOBILE,
		'178'		=> self::CARRIER_MOBILE,
		'182'		=> self::CARRIER_MOBILE,
		'183'		=> self::CARRIER_MOBILE,
		'187'		=> self::CARRIER_MOBILE,
		'188'		=> self::CARRIER_MOBILE,
	];
	
	public function __construct()
	{
		
	}

	/**
	 * 根据手机号段返回运营商ID
	 * 
	 * @param  string $mobile mobile
	 * @return string|null    运营商ID
	 */
	public static function findCarrier($mobile)
	{
		foreach (static::$mobileSegmentDict as $key => $value) {
			if (preg_match("/^$key/", $mobile)) {
				return $value;
			}
		}

		return null;
	}

}

