<?php
namespace xz1mefx\ufu\models;

use xz1mefx\base\models\traits\CategoryTreeTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
class UfuCategory extends ActiveRecord
{

    use CategoryTreeTrait;

    const TABLE_ALIAS_PARENT_UFU_CATEGORY = 'puc';
    const TABLE_ALIAS_UFU_CATEGORY_TRANSLATE = 'uct';
    const TABLE_ALIAS_PARENT_UFU_CATEGORY_TRANSLATE = 'puct';
    const TABLE_ALIAS_UFU_URL = 'uu';

    public $is_parent;

    private $_segmentLevel;
    private $_type;
    private $_url;

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
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $url = $this->ufuUrl ?: new UfuUrl();
        $url->segment_level = $this->segmentLevel;
        $url->is_category = 1;
        $url->type = $this->type;
        $url->item_id = $this->id;
        $url->url = $this->url;
        $url->save();
    }

    /**
     * @param null|string|array $table     Examples:
     *                                     'table_name'
     *                                     or 'table_name tn'
     *                                     or ['table_name_1 tn1', 'table_name_2', 'tn3'=>'table_name_3']
     * @param string            $blockType 'WRITE'|'READ'
     */
    private static function lockTables($table = NULL, $blockType = 'WRITE')
    {
        $sql = "LOCK TABLES ";
        if ($table === NULL) {
            $sql .= self::tableName() . " $blockType";
        } else {
            $tmpSql = '';
            foreach ((is_array($table) ? $table : [$table]) as $key => $value) {
                $tmpSql .= empty($tmpSql) ? '' : ', ';
                $tmpSql .= "$value";
                $tmpSql .= is_string($key) ? " $key" : '';
                $tmpSql .= " $blockType";
            }
            $sql .= $tmpSql;
        }
//        die(Yii::$app->db->createCommand($sql)->rawSql);
        Yii::$app->db->createCommand($sql)->query();
    }

    /**
     *
     */
    private static function unlockTables()
    {
        Yii::$app->db->createCommand("UNLOCK TABLES;")->query();
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
                $category->save();
            }
        }
        // unlock locked tables
        self::unlockTables();
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
            [['segmentLevel'], 'integer'],
            [['segmentLevel'], 'default', 'value' => 1],
            // virtual type field
            [['type'], 'integer'],
            // virtual url field
            [['url'], 'required'],
            [['url'], 'string'],
            // virtual is_parent field
            [['is_parent'], 'safe'],
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
     * @return int
     */
    public function getSegmentLevel()
    {
        if (isset($this->_segmentLevel)) {
            return $this->_segmentLevel;
        }
        if ($this->ufuUrl) {
            return $this->_segmentLevel = $this->ufuUrl->segment_level;
        }
        return $this->_segmentLevel = 1;
    }

    /**
     * @param $value integer
     */
    public function setSegmentLevel($value)
    {
        $this->_segmentLevel = $value;
    }

    /**
     * @return int|null
     */
    public function getType()
    {
        if (isset($this->_type)) {
            return $this->_type;
        }
        if ($this->ufuUrl) {
            return $this->_type = $this->ufuUrl->type;
        }
        return $this->_type = NULL;
    }

    /**
     * @param $value integer
     */
    public function setType($value)
    {
        $this->_type = $value;
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        if (isset($this->_url)) {
            return $this->_url;
        }
        if ($this->ufuUrl) {
            return $this->_url = $this->ufuUrl->url;
        }
        return $this->_url = NULL;
    }

    /**
     * @param $value string
     */
    public function setUrl($value)
    {
        $this->_url = $value;
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
        return $this->ufuCategoryTranslate ? $this->ufuCategoryTranslate->name : '';
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
        return $this->parentUfuCategoryTranslate ? $this->parentUfuCategoryTranslate->name : '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUfuUrl()
    {
        return $this->hasOne(UfuUrl::className(), ['item_id' => 'id'])
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ufu_category}}';
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
