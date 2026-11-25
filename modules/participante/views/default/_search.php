<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $model \app\modules\participante\models\ParticipanteSearch */

$params = Yii::$app->request->queryParams;
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get'
]) ?>
<br>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'nome')->textInput()->label('Nome') ?>
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