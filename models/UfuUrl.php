<?php

namespace xz1mefx\ufu\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ufu_url}}".
 *
 * @property integer $id
 * @property integer $is_first_segment
 * @property integer $model
 * @property integer $item_id
 * @property string  $url
 * @property string  $full_path_hash
 * @property integer $created_at
 * @property integer $updated_at
 */
class UfuUrl extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ufu_url}}';
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
            [['is_first_segment', 'item_id', 'created_at', 'updated_at'], 'integer'],
            [['model', 'item_id', 'url'], 'required'],
            [['model', 'url'], 'string', 'max' => 255],
            [['full_path_hash'], 'string', 'max' => 32],
            [['item_id'], 'unique'],
            [['full_path_hash'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ufu-tools', 'ID'),
            'is_first_segment' => Yii::t('ufu-tools', 'Is First Segment'),
            'model' => Yii::t('ufu-tools', 'Model'),
            'item_id' => Yii::t('ufu-tools', 'Item ID'),
            'url' => Yii::t('ufu-tools', 'Url'),
            'full_path_hash' => Yii::t('ufu-tools', 'Full Path Hash'),
            'created_at' => Yii::t('ufu-tools', 'Created At'),
            'updated_at' => Yii::t('ufu-tools', 'Updated At'),
        ];
    }
}
