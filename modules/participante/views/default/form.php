<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title = \Yii::$app->controller->route == 'participante/default/criar' ? 'Adicionar Participante' : 'Editar Participante';

?>

<div class="panel panel-default">
    <div class="panel-body">
        <?php $form = ActiveForm::begin([]); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'nome')->label('Nome') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'matricula')
                    ->textInput(['maxlength' => 7])
                ->label('Registro') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'idade')->label('Idade') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'grupoId')->widget(Select2::className(), [
                    'data' => ArrayHelper::map($grupos, 'id', 'descricao'),
                    'options' => ['placeholder' => 'Selecione o grupo'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ])->label('Grupo') ?>
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