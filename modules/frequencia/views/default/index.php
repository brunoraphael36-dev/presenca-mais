<?php

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frequencia\models\FrequenciaSearch */

use app\models\Presenca;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Registro de Presença';

?>

<div class="panel panel-default">
    <div class="panel-body">
        <a href="<?= Url::to(['criar']); ?>" class="btn btn-danger" role="button"><i
                class="glyphicon glyphicon-plus"></i> Registrar presença</a>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Filtro</strong>
    </div>
    <div class="panel-body">
        <?= $this->render('_search', [
            'model' => $searchModel,
            'grupos' => $grupos,
            'participantes' => $participantes,
        ]) ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Presença</div>
    <div class="panel-body">
        <?= GridView::widget([
            'summary' => 'Exibindo {begin} - {end} de {totalCount} itens',
            'tableOptions' => ['class' => 'table table-bordered table-hover'],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'matricula',
                    'label' => 'Registro',
                ],
                [
                    'attribute' => 'data',
                    'label' => 'Data',
                    'value' => function ($model) {
                        return (new DateTime($model->data))->format('d/m/Y');
                    }
                ],
                [
                    'attribute' => 'presenca',
                    'label' => 'Presença',
                    'value' => function ($model) {
                        switch ($model->presenca) {
                            case 1:
                                return Presenca::PRESENTE;
                            case 0:
                                return Presenca::FALTOU;
                            case 2:
                                return Presenca::FALTA_JUSTIFICADA;
                            default:
                                return 'Não informado';
                        }
                    }

                ],
                [
                    'attribute' => 'justificativa',
                    'label' => 'Justificativa',
                ],

                [
                    'attribute' => 'grupoDescricao',
                    'label' => 'Grupo',
                    'value' => function ($model) {
                        return $model->grupo ? $model->grupo->descricao : null;
                    },
                ],
            ],
        ]); ?>
    </div>
</div>