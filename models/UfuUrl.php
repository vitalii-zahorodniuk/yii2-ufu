<?php
namespace xz1mefx\ufu\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "{{%ufu_url}}".
 *
 * @property integer $id
 * @property integer $segment_level
 * @property integer $is_category
 * @property integer $type
 * @property integer $item_id
 * @property string  $url
 * @property string  $full_path_hash
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property integer $parent_category_id For validation
 */
class UfuUrl extends ActiveRecord
{

    public $parent_category_id;

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
            [['segment_level', 'is_category', 'type', 'item_id', 'created_at', 'updated_at'], 'integer'],
            [['type', 'item_id', 'url'], 'required'],
            ['type', 'in', 'range' => Yii::$app->ufu->getTypesIdList()],
            ['url', 'string', 'min' => 1, 'max' => 255],
            ['url', function ($attribute, $params) {
                // url symbols check
                if (preg_match('/[^a-z0-9-]/iu', $this->{$attribute})) {
                    $this->addError($attribute, Yii::t('ufu-tools', 'URL must contain only the English characters, digits and hyphens'));
                }
                // unique check
                if ($this->segment_level == 1) {
                    $uniqueCheckQuery = self::find()->where(['url' => $this->url, 'segment_level' => 1]);
                } else {
                    if (empty($this->parent_category_id)) {
                        throw new BadRequestHttpException("You must set `parent_category_id` to validate!");
                    }
                    $uniqueCheckQuery = UfuCategory::find()->joinWith('ufuUrl')->where([UfuCategory::TABLE_ALIAS_UFU_URL . '.url' => $this->url, 'parent_id' => $this->parent_category_id]);
                }
                if ($uniqueCheckQuery->exists()) {
                    $this->addError($attribute, Yii::t('ufu-tools', 'This URL already exists, please enter another URL'));
                }
            }],
            [['full_path_hash'], 'string', 'max' => 32],
            [['full_path_hash'], 'unique'],
            ['parent_category_id', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ufu-tools', 'ID'),
            'segment_level' => Yii::t('ufu-tools', 'Segment Level'),
            'is_category' => Yii::t('ufu-tools', 'Is Category'),
            'type' => Yii::t('ufu-tools', 'Type'),
            'item_id' => Yii::t('ufu-tools', 'Item ID'),
            'url' => Yii::t('ufu-tools', 'Url'),
            'full_path_hash' => Yii::t('ufu-tools', 'Full Path Hash'),
            'created_at' => Yii::t('ufu-tools', 'Created At'),
            'updated_at' => Yii::t('ufu-tools', 'Updated At'),
        ];
    }

}
