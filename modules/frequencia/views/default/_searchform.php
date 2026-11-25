<?php

use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

/* @var $model \app\models\Participante */

$params = Yii::$app->request->queryParams;
?>

<?php $form = ActiveForm::begin([
    'action' => ['criar'],
    'method' => 'get'
]) ?>
<br>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'grupoId')->widget(Select2::className(), [
            'data' => ArrayHelper::map($grupos, 'id', 'descricao'),

            'options' => [
                'id' => 'participante-grupoid',
                'placeholder' => 'Selecione o grupo',
                'required' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ]
        ])->label('Grupo') ?>
    </div>
    <div class="col-md-6">
        <?= Html::label('Data da Frequência', 'data_frequencia', ['class' => 'form-label']) ?>
        <?= DatePicker::widget([
            'name' => 'data_frequencia',
            'value' => Yii::$app->request->get('data_frequencia'),
            'options' => [
                'id' => 'data_frequencia',
                'placeholder' => 'Selecione uma Data',
                'required' => true,
                'autocomplete' => 'off',
            ],
            'pluginOptions' => [
                'format' => 'dd/mm/yyyy',
                'autoclose' => true,
                'todayHighlight' => true,
                'endDate' => '0d'
            ]
        ]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-search-plus"></i> Filtrar', ['class' => 'btn btn-danger']) ?>
            <?php if (!empty($params)) {
                echo Html::a('<i class="fa fa-refresh"></i> Limpar Filtro', ['criar'], ['class' => 'btn btn-default']);
            } ?>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>