<?php

namespace app\modules\participante\models;

use app\models\Participante;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ParticipanteSearch represents the model behind the search form of `app\models\Participante`.
 */
class ParticipanteSearch extends Participante
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'nome', 'matricula', 'idade', 'grupoId'], 'safe'],
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
        $query = Participante::find()->orderBy(['nome' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'grupoId' => $this->grupoId,
        ]);

        $query->andFilterWhere(['ilike', 'nome', $this->nome]);

        return $dataProvider;
    }
}

