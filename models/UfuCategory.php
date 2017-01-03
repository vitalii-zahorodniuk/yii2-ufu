<?php
namespace xz1mefx\ufu\models;

use xz1mefx\base\models\traits\CategoryTreeTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

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
 * @property UfuCategoryTranslate   $ufuCategoryTranslate
 * @property string                 $name
 *
 * @property UfuCategoryTranslate   $parentUfuCategoryTranslate
 * @property string                 $parentName
 *
 * @property integer                $segmentLevel
 * @property string                 $type
 * @property string                 $url
 *
 * @property integer                $is_parent
 * @property UfuCategoryRelation[]  $ufuCategoryRelations
 * @property UfuCategoryTranslate[] $ufuCategoryTranslates
 * @property MlLanguage[]           $languages
 * @property UfuUrl                 $ufuUrl
 * @property UfuCategory            $parent
 */
class UfuCategory extends UrlActiveRecord
{

    use CategoryTreeTrait;

    const TABLE_ALIAS_PARENT_UFU_CATEGORY = 'puc';
    const TABLE_ALIAS_UFU_CATEGORY_TRANSLATE = 'uct';
    const TABLE_ALIAS_PARENT_UFU_CATEGORY_TRANSLATE = 'puct';
    const TABLE_ALIAS_UFU_URL = 'uu';

    public $is_parent;

    /**
     * @return bool
     */
    public static function checkRecordsExist()
    {
        return self::find()->exists();
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->is_parent = (int)($this->parent_id == 0 || $this->isNewRecord);
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->is_parent) {
            $this->parent_id = 0; // to save
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    /**
     *
     */
    public function updateCategoryTree()
    {
        // lock tables
        self::lockTables([
            UfuUrl::tableName(),
            self::TABLE_ALIAS_UFU_URL => UfuUrl::tableName(),
            self::tableName(),
        ]);
        // clear cached data
        self::resetItemsIdTreeCache();
        // get flat tree array
        $itemsTreeList = self::collectItemsIdTree(TRUE);
        // refresh data
        if (isset($itemsTreeList[$this->id])) {
            foreach (self::findAll(array_merge($itemsTreeList[$this->id]['parents_id_list'], [$this->id], $itemsTreeList[$this->id]['children_id_list'])) as $category) {
                // Update category
                $category->parents_list = Json::encode($itemsTreeList[$category->id]['parents_id_list']);
                $category->children_list = Json::encode($itemsTreeList[$category->id]['children_id_list']);
                $category->segmentLevel = Json::encode($itemsTreeList[$category->id]['level']);
                if (in_array($itemsTreeList[$category->id], $itemsTreeList[$this->id]['children_id_list'])) {
                    $category->type = $this->type;
                }
                $category->save(FALSE);
            }
        }
        // unlock locked tables
        self::unlockTables();
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
            // parent id
            ['parent_id', 'integer'],
            ['parent_id', 'default', 'value' => 0],
            // parents list
            ['parents_list', 'string'],
            ['parents_list', 'default', 'value' => '[]'],
            // children list
            ['children_list', 'string'],
            ['children_list', 'default', 'value' => '[]'],
            // created-updated timestamps
            [['created_at', 'updated_at'], 'integer'],
            // virtual segment level field
            ['segmentLevel', 'integer'],
            ['segmentLevel', 'default', 'value' => 1],
            // virtual type field
            ['type', 'integer'],
            // virtual url field
            ['url', 'required'],
            ['url', 'string', 'min' => 1, 'max' => 255],
            ['url', 'validateUfuUrl'],
            // virtual is_parent field
            ['is_parent', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ufu-tools', 'ID'),
            'is_parent' => Yii::t('ufu-tools', 'Is Parent'),
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
        return $this
            ->hasOne(UfuCategoryTranslate::className(), ['category_id' => 'id'])
            ->from([self::TABLE_ALIAS_UFU_CATEGORY_TRANSLATE => UfuCategoryTranslate::tableName()])
            ->andOnCondition([self::TABLE_ALIAS_UFU_CATEGORY_TRANSLATE . '.language_id' => Yii::$app->lang->id]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return empty($this->ufuCategoryTranslate->name) ? Yii::t('ufu-tools', '<i>(has no translation)</i>') : $this->ufuCategoryTranslate->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentUfuCategoryTranslate()
    {
        return $this
            ->hasOne(UfuCategoryTranslate::className(), ['category_id' => 'parent_id'])
            ->from([self::TABLE_ALIAS_PARENT_UFU_CATEGORY_TRANSLATE => UfuCategoryTranslate::tableName()])
            ->andOnCondition([self::TABLE_ALIAS_PARENT_UFU_CATEGORY_TRANSLATE . '.language_id' => Yii::$app->lang->id]);
    }

    /**
     * @return string
     */
    public function getParentName()
    {
        if ($this->parent_id == 0) {
            return '';
        }
        return empty($this->parentUfuCategoryTranslate->name) ? Yii::t('ufu-tools', '<i>(has no translation)</i>') : $this->parentUfuCategoryTranslate->name;
    }

    /**
     * @inheritdoc
     */
    public function getUfuUrl()
    {
        return $this->hasOne(UfuUrl::className(), ['item_id' => 'id'])
            ->andOnCondition(['is_category' => 1])
            ->from([self::TABLE_ALIAS_UFU_URL => UfuUrl::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this
            ->hasOne(self::className(), ['id' => 'parent_id'])
            ->from([self::TABLE_ALIAS_PARENT_UFU_CATEGORY => self::tableName()]);
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
        return $this
            ->hasMany(MlLanguage::className(), ['id' => 'language_id'])
            ->viaTable(UfuCategoryTranslate::tableName(), ['category_id' => 'id']);
    }

}
