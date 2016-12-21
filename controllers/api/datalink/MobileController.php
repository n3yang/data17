<?php

namespace app\controllers\api\datalink;

use Yii;
use Exception;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\helpers\Json;
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

        $raw = Yii::$app->request->getRawBody();
        $res = Json::decode($raw);
        $mobile = $res['dat']['paramlist']['mobile'];
        $name = $res['dat']['paramlist']['name'];
        $idcard = $res['dat']['paramlist']['idcard'];

        if (empty($mobile) || empty($name) || empty($idcard)) {
            throw new BadRequestHttpException('Invalid Param');
        }

        $rs = JdwxClient::MobileIdent($mobile, $name, $idcard);

        return [
            'rows'      => $rs,
            'total'     => 1,
            'msg'       => $rs['msg'],
        ];
    }



}
