<?php
namespace xz1mefx\ufu\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class UFU
 * @package xz1mefx\ufu\components
 */
class UFU extends Component
{

    public $urlTypes = [];

    private $_indexedUrlTypes = [];
    private $_typesIdList = [];

    public function init()
    {
        parent::init();
        $this->_indexedUrlTypes = ArrayHelper::index($this->urlTypes, 'id');
    }

    /**
     * @return array
     */
    public function getTypesList()
    {
        return $this->_indexedUrlTypes;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getTypeById($id, $key = NULL)
    {
        if ($key) {
            return ArrayHelper::getValue(ArrayHelper::getValue($this->_indexedUrlTypes, $id), $key);
        }
        return ArrayHelper::getValue($this->_indexedUrlTypes, $id);
    }

    /**
     * @return array
     */
    public function getTypesIdList()
    {
        if ($this->_typesIdList) {
            return $this->_typesIdList;
        }
        return $this->_typesIdList = array_keys($this->_indexedUrlTypes, 'id');
    }

    /**
     * @param bool $translated
     *
     * @return array
     */
    public function getDrDownUrlTypes($translated = TRUE)
    {
        return ArrayHelper::map($this->urlTypes, 'id', $translated ? function ($element) {
            return \Yii::t('ufu-tools', "Type \"{$element['name']}\"");
        } : 'name');
    }

    /**
     * @param      $id
     * @param bool $translated
     *
     * @return null|string
     */
    public function getTypeNameById($id, $translated = TRUE)
    {
        return ArrayHelper::getValue($this->getDrDownUrlTypes($translated), $id);
    }

}
