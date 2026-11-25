<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = \Yii::$app->controller->route == 'grupo/default/criar' ? 'Adicionar Grupo' : 'Editar Grupo';

?>

<div class="panel panel-default">
    <div class="panel-body">
        <?php $form = ActiveForm::begin([]); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'descricao')->label('Descrição') ?>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton('<span class="glyphicon glyphicon-saved"></span> Salvar', [
                    'class' => 'btn btn-danger',
                ]) ?>
                <?= Html::a('<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> Cancelar', 'index', ['class' => 'btn btn-default']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>