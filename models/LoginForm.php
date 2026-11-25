<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;      // aceita username ou email
    public $password;
    public $rememberMe = true;

    private $_user;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Usuário ou E-mail',
            'password' => 'Senha',
            'rememberMe' => 'Lembrar-me',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Usuário/e-mail ou senha inválidos.');
            } elseif ($user->status != User::STATUS_ACTIVE) {
                $this->addError($attribute, 'Usuário inativo.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            // 3600*24*30 = 30 dias de sessão caso rememberMe
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsernameOrEmail($this->username);
        }
        return $this->_user;
    }
}