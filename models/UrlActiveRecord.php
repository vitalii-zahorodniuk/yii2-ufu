<?php
namespace xz1mefx\ufu\models;

use xz1mefx\base\db\ActiveRecord;
use Yii;

/**
 * Class UrlActiveRecord
 * @package xz1mefx\ufu\models
 *
 * @property integer $segmentLevel
 * @property string  $type
 * @property string  $url
 *
 * @property string  $typeName
 *
 * @property UfuUrl  $ufuUrl
 */
abstract class UrlActiveRecord extends ActiveRecord
{

    private $_segmentLevel;
    private $_type;
    private $_url;

    /**
     * @return UfuUrl
     */
    abstract public function getUfuUrl();

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $url = $this->ufuUrl ?: new UfuUrl();
        $url->segment_level = $this->segmentLevel;
        $url->is_category = (int)($this instanceof UfuCategory);
        $url->type = $this->type;
        $url->item_id = $this->id;
        $url->url = $this->url;
        $url->save();
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateUfuUrl($attribute, $params)
    {
        // url symbols check
        if (preg_match('/[^a-z0-9-]/iu', $this->{$attribute})) {
            $this->addError($attribute, Yii::t('ufu-tools', 'URL must contain only the English characters, digits and hyphens'));
        }
        //
        $url = new UfuUrl();
        $url->segment_level = $this->segmentLevel;
        $url->url = $this->url;
        if (!$url->validate(['segment_level', 'url'])) {
            foreach ($url->errors as $error) {
                $this->addError("url", $error);
            }
        }
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
     * @return string
     */
    public function getTypeName()
    {
        return Yii::$app->ufu->getTypeNameById($this->type);
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

}