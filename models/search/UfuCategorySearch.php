<?php

namespace xz1mefx\ufu\models\search;

use xz1mefx\ufu\models\UfuCategory;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class UfuCategorySearch
 *
 * @property string $name
 * @property string $parentName
 *
 * @package xz1mefx\ufu\models\search
 */
class UfuCategorySearch extends UfuCategory
{

//    public $is_section;
    public $parentName;
    public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_section', 'type', 'parent_id', 'created_at', 'updated_at'], 'integer'],
            [['parentName', 'name', 'parents_list', 'children_list'], 'safe'],
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
        $query = UfuCategory::find()
            ->joinWith([
                'parentUfuCategoryTranslate',
                'ufuCategoryTranslate',
                'ufuUrl',
            ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

//        $dataProvider->sort->attributes['type'] = [
//            'asc' => [self::TABLE_ALIAS_UFU_URL . '.type' => SORT_ASC],
//            'desc' => [self::TABLE_ALIAS_UFU_URL . '.type' => SORT_DESC],
//        ];

        $dataProvider->sort->attributes['parentName'] = [
            'asc' => [self::TABLE_ALIAS_PARENT_UFU_CATEGORY_TRANSLATE . '.name' => SORT_ASC],
            'desc' => [self::TABLE_ALIAS_PARENT_UFU_CATEGORY_TRANSLATE . '.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['name'] = [
            'asc' => [self::TABLE_ALIAS_UFU_CATEGORY_TRANSLATE . '.name' => SORT_ASC],
            'desc' => [self::TABLE_ALIAS_UFU_CATEGORY_TRANSLATE . '.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            UfuCategory::tableName() . '.id' => $this->id,
            UfuCategory::tableName() . '.is_section' => $this->is_section,
            self::TABLE_ALIAS_UFU_URL . '.type' => $this->type,
            UfuCategory::tableName() . '.parent_id' => $this->parent_id,
            UfuCategory::tableName() . '.created_at' => $this->created_at,
            UfuCategory::tableName() . '.updated_at' => $this->updated_at,
        ]);

        $query
            ->andFilterWhere(['like', self::TABLE_ALIAS_PARENT_UFU_CATEGORY_TRANSLATE . '.name', $this->parentName])
            ->andFilterWhere(['like', self::TABLE_ALIAS_UFU_CATEGORY_TRANSLATE . '.name', $this->name])
            ->andFilterWhere(['like', UfuCategory::tableName() . '.parents_list', $this->parents_list])
            ->andFilterWhere(['like', UfuCategory::tableName() . '.children_list', $this->children_list]);

//        die($query->createCommand()->rawSql);

        return $dataProvider;
    }
}
