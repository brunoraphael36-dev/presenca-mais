<?php

namespace app\modules\grupo\models;

use app\models\Grupo;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GrupoSearch represents the model behind the search form of `app\models\Grupo`.
 */
class GrupoSearch extends Grupo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['descricao'], 'safe'],
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
        $query = Grupo::find()->orderBy(['descricao' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['ilike', 'descricao', $this->descricao]);

        return $dataProvider;
    }
}
