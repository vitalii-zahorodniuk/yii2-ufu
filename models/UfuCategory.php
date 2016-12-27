<?php

namespace xz1mefx\ufu\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%ufu_category}}".
 *
 * @property integer                $id
 * @property integer                $parent_id
 * @property string                 $parents_list
 * @property string                 $children_list
 * @property integer                $created_at
 * @property integer                $updated_at
 *
 * @property UfuCategoryRelation[]  $ufuCategoryRelations
 * @property UfuCategoryTranslate[] $ufuCategoryTranslates
 * @property UfuCategoryTranslate   $ufuCategoryTranslate
 * @property MlLanguage[]           $languages
 */
class UfuCategory extends ActiveRecord
{

    private static $_categoriesDataList;
    private static $_categoriesDataTree;

    /**
     * Init variables
     */
    public static function tryInitMainStaticVars()
    {
        if (!isset(self::$_categoriesDataList, self::$_categoriesDataTree)) {
            $preparedData = ArrayHelper::map(
                self::find()
                    ->joinWith('ufuCategoryTranslate')
                    ->select([
                        'ufu_category.id',
                        'ufu_category.parent_id',
                        'ufu_category_translate.name',
                    ])
                    ->asArray()
                    ->all(),
                'id',
                function ($element) {
                    /* @var self $element */
                    return [
                        'id' => (int)$element['id'],
                        'parent_id' => (int)$element['parent_id'],
                        'name' => (string)$element['name'],
                    ];
                },
                'parent_id'
            );
            self::$_categoriesDataTree = self::constructTreeRecursive($preparedData, self::$_categoriesDataList);
            unset($preparedData);
        }
    }

    /**
     * @param array $data
     * @param array $resCategoriesList
     * @param int   $parent_id
     * @param array $parentsList
     *
     * @return array
     */
    private static function constructTreeRecursive(&$data, &$resCategoriesList, $parent_id = 0, $parentsList = [])
    {
        $res = [];
        if (isset($data[$parent_id])) {
            foreach ($data[$parent_id] as $category) {
                $preparedParentsList = $parentsList;
                $preparedParentsList[] = $category['parent_id'];
                $resCategoriesList[$category['id']] = [
                    'id' => $category['id'],
                    'parent_id' => $category['parent_id'],
                    'parents_list' => $preparedParentsList,
                    'name' => isset($category['name']) ? $category['name'] : '',
                ];
                $res[$category['id']] = $resCategoriesList[$category['id']];
                $res[$category['id']]['childs'] = self::constructTreeRecursive($data, $resCategoriesList, $category['id'], $preparedParentsList);
            }
        }

        return $res;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ufu_category}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'created_at', 'updated_at'], 'integer'],
            [['parents_list', 'children_list'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ufu-tools', 'ID'),
            'parent_id' => Yii::t('ufu-tools', 'Parent ID'),
            'parents_list' => Yii::t('ufu-tools', 'Parents List'),
            'children_list' => Yii::t('ufu-tools', 'Children List'),
            'created_at' => Yii::t('ufu-tools', 'Created At'),
            'updated_at' => Yii::t('ufu-tools', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUfuCategoryRelations()
    {
        return $this->hasMany(UfuCategoryRelation::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUfuCategoryTranslate()
    {
        return $this->hasOne(UfuCategoryTranslate::className(), ['category_id' => 'id'])->andOnCondition(['language_id' => Yii::$app->lang->id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUfuCategoryTranslates()
    {
        return $this->hasMany(UfuCategoryTranslate::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(MlLanguage::className(), ['id' => 'language_id'])->viaTable('{{%ufu_category_translate}}', ['category_id' => 'id']);
    }

}
