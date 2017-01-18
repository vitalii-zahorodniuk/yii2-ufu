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
     * @param string $indexBy
     *
     * @return array
     */
    public function getTypesList($indexBy = 'id')
    {
        return ArrayHelper::index($this->urlTypes, $indexBy);
    }

    /**
     * @param      $id
     * @param null $key
     *
     * @return mixed
     */
    public function getTypeById($id, $key = NULL)
    {
        if ($key) {
            return ArrayHelper::getValue(ArrayHelper::getValue($this->getTypesList(), $id), $key);
        }
        return ArrayHelper::getValue($this->getTypesList(), $id);
    }

    /**
     * @return array
     */
    public function getTypesIdList()
    {
        return array_keys($this->getTypesList());
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
