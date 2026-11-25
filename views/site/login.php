<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';

// Adiciona o CSS diretamente na view
$this->registerCss("
    body {
        background: #eee !important;
    }

    .wrapper {
        margin-top: 80px;
        margin-bottom: 80px;
    }

    .form {
        max-width: 380px;
        padding: 15px 35px 45px;
        margin: 0 auto;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .logo {
        margin-bottom: 25px;
    }

    .login-brand {
        display: block;
        width: min(100%, 360px);
        height: 120px;
        margin: 0 auto 18px;
        object-fit: cover;
        object-position: center 54%;
    }

    .form-group .form-control {
        width: auto;
    }

    .form-group {
        margin-bottom: 0;
        width: auto;
    }

    button {
        margin-top: 15px;
    }
");
?>

<div class="wrapper text-center">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'enableClientValidation' => false
    ]); ?>

    <div class="form">
        <div class="row logo">
            <?= Html::img(Url::to('@web/uploads/presenca-plus-logo.jpeg'), [
                'alt' => 'Logo Presença+',
                'class' => 'login-brand',
            ]) ?>
            <h1><?= Html::encode($this->title) ?></h1>
            <p>Preencha os campos a seguir para fazer login:</p>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Email') ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'password')->passwordInput()->label('Senha') ?>
        </div>

        <div class="form-group" style="text-align:right;">
            <?= Html::submitButton('Entrar', [
                'class' => 'btn btn-success',
                'style' => 'background-color:#54bd30; border:2px solid #49a929; width:150px; margin-right:50px;'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>