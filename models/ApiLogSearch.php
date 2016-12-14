<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ApiLog;

/**
 * ApiLogSearch represents the model behind the search form about `app\models\ApiLog`.
 */
class ApiLogSearch extends ApiLog
{
    public $output_error = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['output_error'], 'number'],
            [['id', 'level', 'user_id', 'url', 'path_info', 'http_get', 'http_post', 'rawdata', 'output', 'exec_time', 'ip', 'created_at', 'updated_at', 'output_error'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = ApiLog::find()->with('user');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder'=> ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'level', $this->level])
            ->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'path_info', $this->path_info])
            ->andFilterWhere(['like', 'http_get', $this->http_get])
            ->andFilterWhere(['like', 'http_post', $this->http_post])
            ->andFilterWhere(['like', 'rawdata', $this->rawdata])
            ->andFilterWhere(['like', 'output', $this->output])
            ->andFilterWhere(['like', 'exec_time', $this->exec_time])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        if (!is_null($this->output_error)) {
            $query->andFilterWhere(['=', 'output.error' , (int) $this->output_error]);
        }

        return $dataProvider;
    }

}
