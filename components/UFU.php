<?php
namespace xz1mefx\ufu\components;

use yii\base\Component;

/**
 * Class UFU
 * @package xz1mefx\ufu\components
 */
class UFU extends Component
{

    public $categoryTypes = [];

    public function init()
    {
        print_r($this->categoryTypes);
        die();
        parent::init(); // TODO: Change the autogenerated stub
    }
}