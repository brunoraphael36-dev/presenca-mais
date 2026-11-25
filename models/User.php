<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            [['username', 'email', 'password_hash'], 'required'],
            [['username'], 'string', 'max' => 50],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['username', 'email'], 'unique'],
            [['status'], 'integer'],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () {
                    return time();
                },
            ],
        ];
    }

    // ===== IdentityInterface =====
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Não usado em login web tradicional; implementar se usar API.
        return null;
    }

    public static function findByUsernameOrEmail($login)
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['or', ['username' => $login], ['email' => $login]])
            ->one();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    // ===== Senha =====
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password); // bcrypt por padrão
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function findByPasswordResetToken($token)
    {
        if (!$token) return null;
        return static::findOne(['password_reset_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function hasPermission($perm)
    {
        return (new \yii\db\Query())
            ->from('user_permission up')
            ->innerJoin('permission p', 'p.id = up.permission_id')
            ->where(['up.user_id' => $this->id, 'p.name' => $perm])
            ->exists();
    
    } 
}