<?php

namespace app\models;

use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $group
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $company
 * @property string $tel
 * @property string $tags
 * @property string $api_key
 * @property string $api_secret
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;
    const STATUS_PENDING = 3;

    const GROUP_ADMIN = 1;
    const GROUP_MEMBER = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'group'         => '分组',
            'username'      => '用户名',
            'password'      => '登录密码',
            `auth_key`      => 'Auth Key',
            'company'       => '公司名称',
            'tel'           => '联系电话',
            `tags`          => '自定义标签',
            'api_key'       => 'API key',
            'api_secret'    => 'API secret',
            'created_at'    => '添加时间',
            'updated_at'    => '更新时间',
            `status`        => '状态',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group', 'username', 'password'], 'required'],
            [['status'], 'string'],
            [['group', 'username', 'password', 'company', 'tel', 'api_key', 'api_secret'], 'string', 'max' => 100],
        ];
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findByApiKey($token);
    }

    /**
     * Finds user by api_key
     *
     * @param string $key
     * @return static|null
     */
    public static function findByApiKey($key)
    {
        return static::findOne(['api_key' => (string) $key, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => (string) $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateApiKey()
    {
        $this->api_key = Yii::$app->security->generateRandomString();
    }

    public function generateApiSecret()
    {
        $this->api_secret = Yii::$app->security->generateRandomString();
    }

    /**
     * Validates password
     * If you wanna to encrypt passwords, you should modify this method and next.
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function isAdmin()
    {
        return $this->group == self::GROUP_ADMIN;
    }

    public static function getStatusLabel($status)
    {
        return static::getStatusLabels()[$status] ?: null;
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_ACTIVE => '启用',
            self::STATUS_PENDING => '禁用',
        ];
    }

    public static function getGroupLabel($group)
    {
        return static::getGroupLabels()[$group] ?: null;
    }

    public static function getGroupLabels()
    {
        return [
            self::GROUP_ADMIN => '管理员',
            self::GROUP_MEMBER => '会员',
        ];
    }
}
