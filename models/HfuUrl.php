<?php

namespace xz1mefx\hfu\models;

use Yii;

/**
 * This is the model class for table "{{%hfu_url}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $url
 * @property string $cache_md5_full_path
 * @property integer $cache_item_id
 * @property string $cache_items_list
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property HfuCategory[] $hfuCategories
 */
class HfuUrl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hfu_url}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'cache_item_id', 'created_at', 'updated_at'], 'integer'],
            [['url'], 'required'],
            [['url', 'cache_items_list'], 'string', 'max' => 255],
            [['cache_md5_full_path'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('hfu', 'ID'),
            'type' => Yii::t('hfu', 'Type'),
            'url' => Yii::t('hfu', 'Url'),
            'cache_md5_full_path' => Yii::t('hfu', 'Cache Md5 Full Path'),
            'cache_item_id' => Yii::t('hfu', 'Cache Item ID'),
            'cache_items_list' => Yii::t('hfu', 'Cache Items List'),
            'created_at' => Yii::t('hfu', 'Created At'),
            'updated_at' => Yii::t('hfu', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHfuCategories()
    {
        return $this->hasMany(HfuCategory::className(), ['url_id' => 'id']);
    }
}
