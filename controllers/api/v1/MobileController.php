<?php

namespace app\controllers\api\v1;

use Yii;
use app\models\ApiError;
use app\models\Hash;
use app\components\JieanApiClient;
use app\components\JdwxApiClient;

class MobileController extends ApiController
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        throw new \yii\web\NotFoundHttpException('HAHA');
    }

    public function actionIdentJiean()
    {
        $request = Yii::$app->request;
        $mobile = $request->post('mobile');
        $name = $request->post('name');
        $idcode = $request->post('idcode');;

        if (empty($mobile) || empty($name) || empty($idcode)) {
            ApiError::throwException(ApiError::CODE_INVALID_PARAM);
        }

        $j = new JieanApiClient;
        $rs = $j->MobileIdent($mobile, $name, $idcode);

        return $rs;
    }

    public function actionIdentJdwx()
    {
        $request = Yii::$app->request;
        $mobile = $request->post('mobile');
        $name = $request->post('name');
        $idcode = $request->post('idcode');;

        if (empty($mobile) || empty($name) || empty($idcode)) {
            ApiError::throwException(ApiError::CODE_INVALID_PARAM);
        }

        $j = new JdwxApiClient;
        $rs = $j->MobileIdent($mobile, $name, $idcode);

        return $rs;
    }

    /**
     * convert md5 string to mobile.
     * 
     * @return array 
     */
    public function actionMd5decode($md5)
    {
        $mobile = (string) Hash::findMobileByMd5($md5);
        $this->log->rawdata = $mobile;

        return ['mobile' => $mobile];
    }


    public function actionPos()
    {
        return ['u' => md5(time())];
    }
}
