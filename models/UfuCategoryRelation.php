<?php

namespace xz1mefx\ufu\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ufu_category_relation}}".
 *
 * @property integer     $id
 * @property integer     $category_id
 * @property integer     $item_id
 * @property integer     $created_at
 * @property integer     $updated_at
 *
 * @property UfuCategory $category
 */
class UfuCategoryRelation extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ufu_category_relation}}';
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
            [['category_id', 'item_id'], 'required'],
            [['category_id', 'item_id', 'created_at', 'updated_at'], 'integer'],
            [['category_id', 'item_id'], 'unique', 'targetAttribute' => ['category_id', 'item_id'], 'message' => 'The combination of Category ID and Item ID has already been taken.'],
            [['category_id'], 'exist', 'skipOnError' => TRUE, 'targetClass' => UfuCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ufu-tools', 'ID'),
            'category_id' => Yii::t('ufu-tools', 'Category ID'),
            'item_id' => Yii::t('ufu-tools', 'Item ID'),
            'created_at' => Yii::t('ufu-tools', 'Created At'),
            'updated_at' => Yii::t('ufu-tools', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(UfuCategory::className(), ['id' => 'category_id']);
    }
}
