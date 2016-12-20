<?php

namespace app\controllers\api\v1;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Hash;
use app\models\MobileBackendApi;

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

    /**
     * sketch of mobile
     * 
     * @return array 
     */
    public function actionSketch($mobile)
    {
        $tags = MobileBackendApi::getMobileSketch($mobile);
        if ($tags == MobileBackendApi::ERROR_INVALID_PARAM) {
            throw new BadRequestHttpException('Invalid Param');
        }
        if ($tags == MobileBackendApi::ERROR_PROCESSING) {
            throw new ServerErrorHttpException("Error Processing Request");
        }

        return $tags;
    }

    public function actionPos()
    {
        return ['u' => md5(time())];
    }
}
