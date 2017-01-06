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

    /**
     * @return array
     */
    public function getTypesIdList()
    {
        return ArrayHelper::getColumn($this->urlTypes, 'id');
    }

    /**
     * @param $id
     *
     * @return string|null
     */
    public function getTypeNameById($id)
    {
        return ArrayHelper::getValue(ArrayHelper::map($this->urlTypes, 'id', 'name'), $id);
    }

//    /**
//     * @param $name
//     *
//     * @return int|null
//     */
//    public function getTypeIdByName($name)
//    {
//        return ArrayHelper::getValue(ArrayHelper::map($this->urlTypes, 'name', 'id'), $name);
//    }

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

}
