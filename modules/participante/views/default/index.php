<?php

/* @var $searchModel app\modules\participante\models\ParticipanteSearch */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Participantes";
?>

<div class="panel panel-default">
    <div class="panel-body">
        <a href="<?= Url::to(['criar']); ?>" class="btn btn-danger" role="button"><i
                class="glyphicon glyphicon-plus"></i> Adicionar novo participante</a>
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
        ]) ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Participantes</div>
    <div class="panel-body">
        <div class="table-responsive">
            <?= GridView::widget([
                'summary' => 'Exibindo {begin} - {end} de {totalCount} itens',
                'tableOptions' => ['class' => 'table table-bordered table-hover'],
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'nome',
                        'label' => 'Nome'
                    ],
                    [
                        'attribute' => 'matricula',
                        'label' => 'Registro'
                    ],
                    [
                        'attribute' => 'idade',
                        'label' => 'idade'
                    ],
                    [
                        'attribute' => 'grupoId',
                        'label' => 'Grupo',
                        'value' => function ($model) {
                            return $model->grupo->descricao;
                        }
                    ],
                    [
                        'class' => '\yii\grid\ActionColumn',
                        'headerOptions' => ['widht' => '70'],
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" style="color:#222d32;"></span>', ['editar', 'id' => $model->id]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash" style="color:#222d32;"></span>', ['excluir', 'id' => $model->id], [
                                    'data' => [
                                        'confirm' => 'Tem certeza que deseja deletar este item?',
                                        'method' => 'post',
                                    ],
                                ]);
                            }
                        ],
                    ]
                ],
            ]) ?>
        </div>
    </div>
</div>