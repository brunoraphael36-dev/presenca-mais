<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Login';

?>
<div class="wrapper text-center">
l
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'enableClientValidation'=>false
    ]); ?>

    <div class="form">
        <div class="row logo">
            <?= Html::img(Url::home(true) .'/uploads/logo-colorida.png') ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Email') ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'password')->passwordInput()->label('Senha') ?>
        </div>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Entrar', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>