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
 * @property integer                $is_parent
 * @property string                 $url
 * @property string                 $type
 * @property UfuCategoryRelation[]  $ufuCategoryRelations
 * @property UfuCategoryTranslate[] $ufuCategoryTranslates
 * @property MlLanguage[]           $languages
 * @property UfuUrl                 $ufuUrl
 * @property UfuCategory            $parent
 */
class UfuCategory extends ActiveRecord
{

    const TABLE_ALIAS_PARENT_UFU_CATEGORY = 'puc';
    const TABLE_ALIAS_UFU_CATEGORY_TRANSLATE = 'uct';
    const TABLE_ALIAS_PARENT_UFU_CATEGORY_TRANSLATE = 'puct';
    const TABLE_ALIAS_UFU_URL = 'uu';

    public $is_parent;

    private $_url;
    private $_type;

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
        if ($this->parent_id == 0) {
            $this->is_parent = 1; // to show
        }
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

        $url = $this->ufuUrl;
        if ($url === NULL) {
            $url = new UfuUrl();
        }
        $url->item_id = $this->id;
        $url->url = $this->url;
        $url->type = $this->type;
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
            // parent id
            ['parent_id', 'required'],
            ['parent_id', 'integer'],
            ['parent_id', 'default', 'value' => 0],
            // parents list
            ['parents_list', 'string'],
            // children list
            ['children_list', 'string'],
            // created-updated timestamps
            [['created_at', 'updated_at'], 'integer'],
            // virtual url field
            [['url'], 'safe'],
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
     * @return string
     */
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

    /**
     * @param $value
     */
    public function setUrl($value)
    {
        $this->_url = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        if (isset($this->_type)) {
            return $this->_type;
        }
        if ($this->ufuUrl) {
            return $this->_type = $this->ufuUrl->type;
        }
        return $this->_type = '';
    }

    /**
     * @param $value
     */
    public function setType($value)
    {
        $this->_type = $value;
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
