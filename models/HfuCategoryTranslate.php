<?php

namespace xz1mefx\hfu\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%hfu_category_translate}}".
 *
 * @property integer     $id
 * @property integer     $category_id
 * @property integer     $language_id
 * @property string      $name
 * @property integer     $created_at
 * @property integer     $updated_at
 *
 * @property MlLanguage  $language
 * @property HfuCategory $category
 */
class HfuCategoryTranslate extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hfu_category_translate}}';
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
            [['category_id', 'language_id', 'name'], 'required'],
            [['category_id', 'language_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['language_id'], 'exist', 'skipOnError' => TRUE, 'targetClass' => MlLanguage::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => TRUE, 'targetClass' => HfuCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('hfu', 'ID'),
            'category_id' => Yii::t('hfu', 'Category ID'),
            'language_id' => Yii::t('hfu', 'Language ID'),
            'name' => Yii::t('hfu', 'Name'),
            'created_at' => Yii::t('hfu', 'Created At'),
            'updated_at' => Yii::t('hfu', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(MlLanguage::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(HfuCategory::className(), ['id' => 'category_id']);
    }
}
