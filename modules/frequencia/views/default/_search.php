<?php

use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

/* @var $model \app\modules\frequencia\models\FrequenciaSearch */

$params = Yii::$app->request->queryParams;
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get'
]) ?>
<br>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'data_inicio')->widget(DatePicker::class, [
            'options' => ['placeholder' => 'Data inicial'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd/mm/yyyy',
                'todayHighlight' => true,
                'endDate' => '0d'
            ]
        ])->label('Data inicial') ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'data_fim')->widget(DatePicker::class, [
            'options' => ['placeholder' => 'Data final'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd/mm/yyyy',
                'todayHighlight' => true,
                'endDate' => '0d'
            ]
        ])->label('Data final') ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'matricula')->widget(Select2::className(), [
            'data' => ArrayHelper::map($participantes, 'matricula', 'nome'),
            'options' => ['placeholder' => 'Selecione o participante'],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ])->label('Participante') ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'grupoId')->widget(Select2::className(), [
            'data' => ArrayHelper::map($grupos, 'id', 'descricao'),
            'options' => ['placeholder' => 'Selecione o grupo'],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ])->label('Grupo') ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-search-plus"></i> Filtrar', ['class' => 'btn btn-danger']) ?>
            <?php if (!empty($params)) {
                echo Html::a('<i class="fa fa-refresh"></i> Limpar Filtro', ['index'], ['class' => 'btn btn-default']);
            } ?>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>