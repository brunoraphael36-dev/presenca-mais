<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;

/* @var $model \app\modules\grupo\models\GrupoSearch */

$params = Yii::$app->request->queryParams;
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get'
]) ?>
<br>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'descricao')->textInput()->label('Descrição') ?>
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