<?php

namespace app\modules\frequencia\controllers;

use app\controllers\WebController;
use app\models\Participante;
use app\models\Grupo;
use app\modules\participante\models\ParticipanteSearch;
use app\modules\frequencia\models\PresencaForm;
use app\modules\frequencia\models\PresencaSearch;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use Yii;

class DefaultController extends WebController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            /** @var \app\models\User $user */
                            $user = Yii::$app->user->identity;
                            return $user && $user->hasPermission('presenca.access');
                        },
                    ],
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        $searchModel = new PresencaSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        $grupos = Grupo::find()->orderBy(['descricao' => SORT_ASC])->all();
        $participantes = Participante::find()->orderBy(['nome' => SORT_ASC])->all();

        return $this->render('index' , [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'grupos' => $grupos,
            'participantes' => $participantes,
        ]);
    }

    public function actionCriar()
    {
        /** @var PresencaForm $modelFrequencia */
        $modelFrequencia = new PresencaForm();
        $grupos = Grupo::find()->orderBy(['descricao' => SORT_ASC])->all();

        $searchModel = new ParticipanteSearch();
        $queryParams = Yii::$app->request->queryParams;

        if (!empty($queryParams) && array_filter($queryParams)) {
            $dataProvider = $searchModel->search($queryParams);
        } else {
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => Participante::find()->where('0=1'),
                'pagination' => ['pageSize' => 20],
            ]);
        }

        $post = Yii::$app->request->post();

        if (!$post) {
            return $this->render('form', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'model'        => $modelFrequencia,
                'grupos'       => $grupos,
            ]);
        }

        $dataBr = $post['data_frequencia'] ? $post['data_frequencia'] : null;

        $grupoId = isset($post['grupoId']) && $post['grupoId'] !== ''
            ? (int)$post['grupoId']
            : null;
        $itens   = $post['frequencias'] ? $post['frequencias'] : [];

        if (empty($dataBr)) {
            return $this->finalizeResponse(false, 'Data de presença é obrigatória.', [
                'redirectParams' => ['grupoId' => $grupoId, 'data_frequencia' => $dataBr],
            ]);
        }
        $dt = \DateTime::createFromFormat('d/m/Y', $dataBr);
        if ($dt === false) {
            return $this->finalizeResponse(false, 'Data de presença inválida.', [
                'redirectParams' => ['grupoId' => $grupoId, 'data_frequencia' => $dataBr],
            ]);
        }
        $dataSql = $dt->format('Y-m-d');

        if (empty($itens) || !is_array($itens)) {
            return $this->finalizeResponse(false, 'Nenhuma presença enviada.', [
                'redirectParams' => ['grupoId' => $grupoId, 'data_frequencia' => $dataBr],
            ]);
        }

        $tx = Yii::$app->db->beginTransaction();
        try {
            foreach ($itens as $item) {
                $matricula     = $item['matricula'] ? $item['matricula'] : null;
                $presenca      = isset($item['presenca']) ? (int)$item['presenca'] : null;
                $justificativa = $item['justificativa'] ? $item['justificativa'] : null;

                if (!$matricula || $presenca === null) {
                    throw new \RuntimeException('Item de presença incompleto (matrícula/presença ausentes).');
                }

                $presencaModel = \app\models\Presenca::findOne([
                    'matricula' => $matricula,
                    'data'      => $dataSql,
                    'grupoId'   => $grupoId,
                ]);

                if (!$presencaModel) {
                    $presencaModel = new \app\models\Presenca();
                    $presencaModel->matricula = $matricula;
                    $presencaModel->data      = $dataSql;
                    $presencaModel->grupoId   = $grupoId;
                }

                $presencaModel->presenca = $presenca;
                $presencaModel->justificativa = ($presenca === 2) ? $justificativa : null;

                if (!$presencaModel->save()) {
                    $erros = $presencaModel->getFirstErrors();
                    throw new \RuntimeException('Erro ao salvar presença: ' . implode('; ', $erros));
                }
            }

            $tx->commit();
            return $this->finalizeResponse(true, 'Presenças salvas com sucesso.', [
                'redirectParams' => ['grupoId' => $grupoId, 'data_frequencia' => $dataBr],
            ]);
        } catch (\Throwable $e) {
            $tx->rollBack();
            return $this->finalizeResponse(false, 'Erro ao salvar as presenças: ' . $e->getMessage(), [
                'redirectParams' => ['grupoId' => $grupoId, 'data_frequencia' => $dataBr],
            ]);
        }
    }

    protected function finalizeResponse(bool $ok, string $message, array $extra = [])
    {
        $redirectParams = $extra['redirectParams'] ? $extra['redirectParams'] : [];

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $this->asJson([
                'success' => $ok,
                'message' => $message,
                'redirect' => \yii\helpers\Url::to(array_merge(['criar'], $redirectParams)),
            ]);
        }

        Yii::$app->session->setFlash($ok ? 'success' : 'error', $message);
        return $this->redirect(array_merge(['criar'], $redirectParams));
    }

    public function actionCreditos()
    {
        return $this->render('_creditos');
    }
}