<?php

namespace app\controllers\api\datalink;

use Yii;
use Exception;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\models\JdwxClient;

class MobileController extends ApiController
{

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        throw new \yii\web\NotFoundHttpException('NOTHING HERE');
    }

    /**
     * sketch of mobile
     * 
     * @return array 
     */
    public function actionIdent()
    {

        $dat = Yii::$app->request->getBodyParam('dat');
        $mobile = $dat['paramlist']['mobile'];
        $name = $dat['paramlist']['name'];
        $idcard = $dat['paramlist']['idcard'];

        if (empty($mobile) || empty($name) || empty($idcard)) {
            throw new BadRequestHttpException('Invalid Param');
        }

        try {
            $rs = JdwxClient::MobileIdent($mobile, $name, $idcard);
        } catch (Exception $e) {
            return $e;
        }

        return [
            'rows'      => $rs,
            'total'     => 1,
            'msg'       => $rs['msg'],
        ];
    }



}
