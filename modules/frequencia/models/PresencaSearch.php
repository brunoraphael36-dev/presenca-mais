<?php

namespace app\modules\frequencia\models;

use app\models\Presenca;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PresencaSearch represents the model behind the search form of `app\models\Presenca`.
 */
class PresencaSearch extends Presenca
{
    public $data_inicio;
    public $data_fim;

    public $grupoDescricao;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['matricula', 'justificativa', 'grupoDescricao'], 'safe'],
            [['justificativa'], 'string', 'max' => 255],

            [['presenca', 'grupoId'], 'integer', 'skipOnEmpty' => true],

            ['grupoId', 'filter', 'filter' => function ($value) {
                return $value === '' ? null : $value;
            }],
            ['presenca', 'filter', 'filter' => function ($value) {
                return $value === '' ? null : $value;
            }],

            [['data_inicio', 'data_fim'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Presenca::find()
            ->joinWith(['grupo'])
            ->orderBy(['data' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['grupoDescricao'] = [
            'asc'  => ['grupo.descricao' => SORT_ASC],
            'desc' => ['grupo.descricao' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'presenca' => $this->presenca,
            'grupoId'  => $this->grupoId,
        ]);

        $query->andFilterWhere(['like', 'matricula', $this->matricula]);
        $query->andFilterWhere(['like', 'justificativa', $this->justificativa]);
        $query->andFilterWhere(['like', 'grupo.descricao', $this->grupoDescricao]);

        $from = $this->parseBrDate($this->data_inicio);
        $to   = $this->parseBrDate($this->data_fim);

        if ($from && $to) {
            $query->andFilterWhere(['between', 'data', $from, $to]);
        } elseif ($from) {
            $query->andFilterWhere(['>=', 'data', $from]);
        } elseif ($to) {
            $query->andFilterWhere(['<=', 'data', $to]);
        }

        return $dataProvider;
    }

    /**
     * Converte 'dd/mm/yyyy' -> 'yyyy-mm-dd'. Retorna null se vazio ou inválido.
     */
    protected function parseBrDate($value)
    {
        if (empty($value)) {
            return null;
        }
        $dt = \DateTime::createFromFormat('d/m/Y', $value);
        return $dt ? $dt->format('Y-m-d') : null;
    }
}

class FrequenciaSearch extends PresencaSearch
{
}

