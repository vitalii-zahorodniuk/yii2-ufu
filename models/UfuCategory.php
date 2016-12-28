<?php

namespace xz1mefx\ufu\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ufu_category}}".
 *
 * @property integer                $id
 * @property integer                $parent_id
 * @property integer                $type
 * @property string                 $parents_list
 * @property string                 $children_list
 * @property integer                $created_at
 * @property integer                $updated_at
 *
 * @property string                 $url
 * @property integer                $isFirstSegment
 *
 * @property UfuCategoryRelation[]  $ufuCategoryRelations
 * @property UfuCategoryTranslate[] $ufuCategoryTranslates
 * @property UfuCategoryTranslate   $ufuCategoryTranslate
 * @property MlLanguage[]           $languages
 * @property UfuUrl                 $ufuUrl
 */
class UfuCategory extends ActiveRecord
{

    public $_url;
    public $_isFirstSegment;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ufu_category}}';
    }

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isFirstSegment) {
            $this->parent_id = 0;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $url = $this->ufuUrl;
        if ($url === NULL) {
            $url = new UfuUrl();
        }
        $url->item_id = $this->id;
        $url->model = __CLASS__;
        $url->is_first_segment = $this->isFirstSegment;
        $url->url = $this->url;
        $url->save();

        Yii::$app->multilangCache->flush(); // TODO: make correct clearing in future
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
            [['parent_id'], 'required'],
            [['parent_id', 'type', 'created_at', 'updated_at'], 'integer'],
            ['parent_id', 'default', 'value' => 0],
            [['parents_list', 'children_list'], 'string'],

            [['url', 'isFirstSegment'], 'safe'],
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

    public function getUrl()
    {
        if (isset($this->_url)) {
            return $this->_url;
        }
        if ($this->ufuUrl) {
            return $this->_url = $this->ufuUrl->url;
        }
        return $this->_url = '';
    }

    public function setUrl($value)
    {
        $this->_url = $value;
    }

    public function getIsFirstSegment()
    {
        if (isset($this->_isFirstSegment)) {
            return $this->_isFirstSegment;
        }
        if ($this->ufuUrl) {
            return $this->_isFirstSegment = $this->ufuUrl->is_first_segment;
        }
        return $this->_isFirstSegment = 0;
    }

    public function setIsFirstSegment($value)
    {
        $this->_isFirstSegment = $value;
    }

    /**
     * @return array
     */
    public static function collectItemsTree()
    {
        $preparedData = ArrayHelper::map(
            UfuCategory::find()
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
        return self::_collectItemsTreeRecursive($preparedData);
    }

    /**
     * @param array $data
     * @param int   $parent_id
     * @param array $parentsList
     *
     * @return array
     */
    private static function _collectItemsTreeRecursive(&$data, $parent_id = 0, $parentsList = [])
    {
        $res = [];
        if (isset($data[$parent_id])) {
            foreach ($data[$parent_id] as $category) {
                $preparedParentsIdsList = $parentsIdsList;
                $preparedParentsIdsList[] = $category['parent_id'];

                $resCategoriesList[$category['id']] = [
                    'id' => $category['id'],
                    'parent_id' => $category['parent_id'],
                    'parents_id_list' => $preparedParentsIdsList,
                    'name' => $category['name'],
                ];
                $res[$category['id']] = $resCategoriesList[$category['id']];
                $res[$category['id']]['childs'] = self::_collectItemsTreeRecursive($data, $category['id'], $preparedParentsIdsList);
            }
        }

        return $res;
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
    public function getUfuUrl()
    {
//        die($this->hasOne(UfuUrl::className(), ['item_id' => 'id'])->andOnCondition(['model' => __CLASS__])->createCommand()->rawSql);
        return $this->hasOne(UfuUrl::className(), ['item_id' => 'id'])->andOnCondition(['model' => __CLASS__]);
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
