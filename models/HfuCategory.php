<?php

namespace xz1mefx\hfu\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%hfu_category}}".
 *
 * @property integer                $id
 * @property integer                $parent_id
 * @property integer                $url_id
 * @property integer                $created_at
 * @property integer                $updated_at
 *
 * @property HfuUrl                 $url
 * @property HfuCategoryTranslate[] $hfuCategoryTranslates
 */
class HfuCategory extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hfu_category}}';
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
            [['parent_id', 'url_id', 'created_at', 'updated_at'], 'integer'],
            [['url_id'], 'required'],
            [['url_id'], 'exist', 'skipOnError' => TRUE, 'targetClass' => HfuUrl::className(), 'targetAttribute' => ['url_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('hfu', 'ID'),
            'parent_id' => Yii::t('hfu', 'Parent ID'),
            'url_id' => Yii::t('hfu', 'Url ID'),
            'created_at' => Yii::t('hfu', 'Created At'),
            'updated_at' => Yii::t('hfu', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUrl()
    {
        return $this->hasOne(HfuUrl::className(), ['id' => 'url_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHfuCategoryTranslates()
    {
        return $this->hasMany(HfuCategoryTranslate::className(), ['category_id' => 'id']);
    }
}
