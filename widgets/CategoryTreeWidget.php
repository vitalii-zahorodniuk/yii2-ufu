<?php
namespace xz1mefx\ufu\widgets;

use xz1mefx\ufu\models\UfuCategory;
use xz1mefx\ufu\web\assets\CategoryTreeWidgetAsset;
use yii\bootstrap\Widget;
use yii\helpers\Json;

/**
 * Class CategoryTreeWidget
 * @package xz1mefx\ufu\widgets
 */
class CategoryTreeWidget extends Widget
{

    /**
     * @var string
     */
    public $emptyText = "<i>(noname)</i>";

    /**
     * @var bool
     */
    public $multiselect = TRUE;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var int|array
     */
    public $selectedItems = [];

    /**
     * @var bool
     */
    public $showSelected = TRUE;

    /**
     * @var string
     */
    public $height = '300px';

    /**
     * @var bool
     */
    public $onlyType = FALSE;

    /**
     * @inheritdoc
     */
    public function run()
    {
        CategoryTreeWidgetAsset::register($this->view);
        return $this->renderWidget();
    }

    /**
     * @return mixed
     */
    public function renderWidget()
    {
        $widgetOptions = Json::encode([
            'data' => UfuCategory::collectItemsTree(),
            'emptyText' => $this->emptyText,
            'multiselect' => $this->multiselect,
            'name' => $this->name,
            'selectedItems' => is_array($this->selectedItems) ? $this->selectedItems : [$this->selectedItems],
            'showSelected' => $this->showSelected,
            'height' => $this->height,
            'onlyType' => $this->onlyType,
        ]);

        $this->view->registerJs("$('#ctree').categoryTreeView($widgetOptions);");

        return '<div id="ctree"></div>';
    }

}
