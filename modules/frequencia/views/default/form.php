<?php

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frequencia\models\FrequenciaSearch */

use app\models\Presenca;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

$this->title = 'Registrar Presença';

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Filtro</strong>
    </div>
    <div class="panel-body">
        <?= $this->render('_searchform', [
            'model' => $searchModel,
            'grupos' => $grupos,
        ]) ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Presença de Participantes</div>
    <div class="panel-body">
        <?php Pjax::begin(['id' => 'pjax-grid']); ?>

        <?= GridView::widget([
            'summary' => 'Exibindo {begin} - {end} de {totalCount} itens',
            'tableOptions' => ['class' => 'table table-bordered table-hover'],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'nome',
                    'label' => 'Nome',
                ],
                [
                    'attribute' => 'matricula',
                    'label' => 'Registro',
                ],
                [
                    'label' => 'Status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $dataSelecionada = Yii::$app->request->get('data_frequencia');
                        $status = 'Data não selecionada';
                        if ($dataSelecionada) {
                            $dataFormatada = \DateTime::createFromFormat('d/m/Y', $dataSelecionada)->format('Y-m-d');
                            $frequencia = Presenca::find()
                                ->where(['matricula' => $model->matricula, 'data' => $dataFormatada])
                                ->one();
                            if ($frequencia) {
                                switch ((int)$frequencia->presenca) {
                                    case 1:
                                        $status = 'Presente';
                                        break;
                                    case 0:
                                        $status = 'Faltou';
                                        break;
                                    case 2:
                                        $status = 'Falta Justificada';
                                        break;
                                }
                            } else {
                                $status = 'Não registrada';
                            }
                        }
                        return "<span id='status-{$model->matricula}'>$status</span>";
                    }
                ],

                // ---- NOVA COLUNA: Radios Presente/Faltou + botão Justificar/Badge + hidden da justificativa ----
                [
                    'label' => 'Presença',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'min-width:320px;'],
                    'value' => function ($model) {
                        $dataSelecionada = Yii::$app->request->get('data_frequencia');
                        $selected = null;       // 1=presente, 0=faltou, 2=justificada
                        $isJustificada = false;
                        $justificativaTexto = '';

                        if ($dataSelecionada) {
                            $dataFormatada = \DateTime::createFromFormat('d/m/Y', $dataSelecionada)->format('Y-m-d');
                            $frequencia = Presenca::find()
                                ->where(['matricula' => $model->matricula, 'data' => $dataFormatada])
                                ->one();

                            if ($frequencia) {
                                $selected = (int)$frequencia->presenca;
                                $isJustificada = ($selected === 2);
                                $justificativaTexto = $frequencia->justificativa ? $frequencia->justificativa : '';
                            } else {
                                // Sem registro: padrão “Presente” selecionado
                                $selected = 1;
                            }
                        }

                        $matricula = $model->matricula;
                        $nome = $model->nome;
                        $groupName = "presenca-{$matricula}";
                        $radioPresenteId = "radio-presente-{$matricula}";
                        $radioFaltouId   = "radio-faltou-{$matricula}";
                        $btnJustId       = "btn-justificar-{$matricula}";
                        $badgeId         = "lbl-justificada-{$matricula}";
                        $hiddenJustId    = "hidden-justificativa-{$matricula}";

                        $radioPresente = Html::radio($groupName, $selected === 1, [
                            'class' => 'radio-presenca',
                            'id' => $radioPresenteId,
                            'data-value' => 1,
                            'data-matricula' => $matricula,
                            'data-nome' => $nome,
                            'label' => ' Presente',
                            'style' => 'margin-right:8px;',
                        ]);

                        $radioFaltou = Html::radio($groupName, ($selected === 0 || $selected === 2), [
                            'class' => 'radio-presenca',
                            'id' => $radioFaltouId,
                            'data-value' => ($isJustificada ? 2 : 0),
                            'data-matricula' => $matricula,
                            'data-nome' => $nome,
                            'label' => ' Faltou',
                            'style' => 'margin-left:10px;',
                        ]);

                        // Botão Justificar (visível somente quando marcado Faltou e NÃO justificada)
                        $btnJustificar = Html::button(
                            '<span class="glyphicon glyphicon-pencil"></span> Justificar',
                            [
                                'class' => 'btn btn-xs btn-warning btn-justificar',
                                'id' => $btnJustId,
                                'data-matricula' => $matricula,
                                'data-nome' => $nome,
                                'title' => 'Falta Justificada',
                                'style' => (($selected === 0) ? 'margin-left:10px;background-color:#b71b1c;color:#fff;border-color:#b71b1c;' : 'display:none;margin-left:10px;background-color:#b71b1c;color:#fff;border-color:#b71b1c;'),
                            ]
                        );

                        // Badge “Justificada” quando presenca = 2
                        $badgeJustificada = $isJustificada
                            ? Html::tag('span', 'Justificada', [
                                'class' => 'label label-info',
                                'id' => $badgeId,
                                'style' => 'margin-left:10px;',
                            ])
                            : '';

                        // Hidden input para armazenar justificativa localmente até clicar "Salvar presença"
                        $hiddenJust = Html::hiddenInput("justificativa[$matricula]", $justificativaTexto, [
                            'id' => $hiddenJustId,
                        ]);

                        return $radioPresente . $radioFaltou . ' ' . ($isJustificada ? $badgeJustificada : $btnJustificar) . $hiddenJust;
                    },
                ],
            ],
        ]); ?>

        <div class="text-right" style="margin-top:15px;">
            <?= Html::button('<span class="glyphicon glyphicon-saved"></span> Salvar presença', [
                'class' => 'btn btn-danger',
                'id' => 'btn-salvar-todos',
            ]) ?>
            <?= Html::a('<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> Cancelar', 'index', ['class' => 'btn btn-default']) ?>
        </div>

        <?php Pjax::end(); ?>

    </div>
</div>

<?php

Modal::begin([
    'id' => 'modal-justificativa',
    'header' => '<h4>Justificar Falta</h4>',
]);

echo '<div class="form-group">
        <label>Observação</label>
        <textarea id="input-justificativa" class="form-control" rows="3"></textarea>
      </div>
      <div class="text-right">
        <button id="btn-salvar-justificativa" class="btn btn-success" style="background-color:#b71b1c;color:#fff;border-color:#b71b1c;">Salvar</button>
      </div>';

Modal::end();
?>


<?php
$script = <<<JS
function validarData() {
    var dataFrequencia = $('#data_frequencia').val()
    if (!dataFrequencia) {
        alert('Por favor, selecione a data da frequência antes de continuar.');
        return false;
    }
    return dataFrequencia;
}

function validarGrupo() {
    var grupoId = $('#participante-grupoid').val() || '';
    console.log('Grupo selecionado:', grupoId);
    if (!grupoId) {
        alert('Por favor, selecione o grupo antes de continuar.');
        return false;
    }
    return grupoId;
}

// CSS spinner
$('<style>.spinning { animation: spin 1s linear infinite; } @keyframes spin { 100% { transform: rotate(360deg); } }</style>').appendTo('head');

$(document).on('change', '.radio-presenca', function() {
    var dataFrequencia = validarData();
    if (!dataFrequencia) return;

    var \$radio = $(this);
    var matricula = \$radio.data('matricula');
    var valor = parseInt(\$radio.data('value'), 10); // 1=Presente, 0=Faltou, 2=Falta Justificada (visual)
    var statusCell = $('#status-' + matricula);
    var btnId = '#btn-justificar-' + matricula;

    if (valor === 1) {
        statusCell.text('Presente');
        $(btnId).hide();
        $('#lbl-justificada-' + matricula).remove();
        $('#hidden-justificativa-' + matricula).val('');
    } else {
        statusCell.text('Faltou');
        $(btnId).show();
        $('#lbl-justificada-' + matricula).remove();
    }
});

$(document).on('click', '.btn-justificar', function(e) {
    e.preventDefault();
    var dataFrequencia = validarData();
    if (!dataFrequencia) return;

    var nome = $(this).data('nome');
    var matricula = $(this).data('matricula');

    $('#modal-justificativa').data('nome', nome);
    $('#modal-justificativa').data('matricula', matricula);
    $('#modal-justificativa').data('dataFrequencia', dataFrequencia);

    var atual = $('#hidden-justificativa-' + matricula).val() || '';
    $('#input-justificativa').val(atual);

    $('#modal-justificativa').modal('show');
});

$('#btn-salvar-justificativa').on('click', function() {
    var justificativa = $('#input-justificativa').val().trim();
    var nome = $('#modal-justificativa').data('nome');
    var matricula = $('#modal-justificativa').data('matricula');

    if (!justificativa) {
        alert('Informe a justificativa.');
        return;
    }

    $('#hidden-justificativa-' + matricula).val(justificativa);

    var statusCell = $('#status-' + matricula);
    statusCell.text('Falta Justificada');

    $('#modal-justificativa').modal('hide');
    $('#input-justificativa').val('');

    $('#btn-justificar-' + matricula).hide();

    var badgeId = 'lbl-justificada-' + matricula;
    if (!$('#' + badgeId).length) {
        $('<span/>', {
            id: badgeId,
            class: 'label label-info',
            text: 'Justificada',
            style: 'margin-left:10px;'
        }).insertAfter('#radio-faltou-' + matricula);
    }

    $('#radio-faltou-' + matricula).prop('checked', true);
});

$('#btn-salvar-todos').on('click', function() {
    var dataFrequencia = validarData();
    var grupoId = validarGrupo();
    if (!dataFrequencia || !grupoId) return;

    var itens = [];
    $('input[id^="radio-presente-"]').each(function() {
        var matricula = $(this).data('matricula');
        var nome = $(this).data('nome');

        var selecionado = $('input[name="presenca-' + matricula + '"].radio-presenca:checked').data('value');
        var justificativa = $('#hidden-justificativa-' + matricula).val();

        var presencaFinal;
        if (selecionado === 1) {
            presencaFinal = 1;
            justificativa = null;
        } else {
            presencaFinal = justificativa ? 2 : 0;
        }

        itens.push({
            matricula: matricula,
            presenca: presencaFinal,
            justificativa: justificativa
        });
    });

    if (itens.length === 0) {
        alert('Nenhum item para salvar.');
        return;
    }

    var \$btn = $(this);
    \$btn.prop('disabled', true).text('Salvando...');

    $.post('criar', {
        data_frequencia: dataFrequencia,
        grupoId: grupoId,
        frequencias: itens,
        _csrf: yii.getCsrfToken()
    }).done(function(resp) {
        alert('Frequências salvas com sucesso.');
        \$btn.prop('disabled', false).html('<span class="glyphicon glyphicon-saved"></span> Salvar presença');
    }).fail(function() {
        alert('Erro ao salvar as frequências. Tente novamente.');
        \$btn.prop('disabled', false).html('<span class="glyphicon glyphicon-saved"></span> Salvar presença');
    });
});
JS;
$this->registerJs($script);
?>