<?php
namespace xz1mefx\ufu\widgets;

use xz1mefx\ufu\models\UfuCategory;
use xz1mefx\ufu\web\assets\CategoryTreeWidgetAsset;
use Yii;
use yii\base\InvalidValueException;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class CategoryTreeWidget
 * @package xz1mefx\ufu\widgets
 */
class CategoryTreeWidget extends Widget
{

    /**
     * @var bool
     */
    public $multiselect = FALSE;

    /**
     * @var string
     */
    public $name = NULL;

    /**
     * @var null|array
     */
    public $selectedItem = NULL;

    /**
     * @var string
     */
    public $emptyItemText = "<i>(not set)</i>\n";

    /**
     * @var array Category tree widget cache key
     */
    private $_treeWidgetCacheKey;

    private $_selectedItemsJson = NULL;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (empty($this->name) || !is_string($this->name)) {
            throw new InvalidValueException("Incorrect name value!");
        }

        $this->_treeWidgetCacheKey = [
            Yii::$app->language,
            CategoryTreeWidget::className(),
            $this->multiselect,
        ];

        $this->_selectedItemsJson = Json::encode(empty($this->selectedItem) ? [] : (is_array($this->selectedItem) ? $this->selectedItem : [$this->selectedItem]));

        CategoryTreeWidgetAsset::register($this->view);

        return $this->renderWidget();
    }

    /**
     * @return mixed
     */
    public function renderWidget()
    {
        $widgetId = 'ufu-ctree-' . self::$counter;

        $content = "<div id=\"$widgetId\" class=\"panel panel-default panel-body ufu-ctree\">\n";

        $widgetContent = Yii::$app->multilangCache->get($this->_treeWidgetCacheKey);
        if ($widgetContent === FALSE) {
            $widgetContent = $this->renderItems();
            Yii::$app->multilangCache->set($this->_treeWidgetCacheKey, $widgetContent);
        }

        $content .= $widgetContent;
        $content .= '</div>';

        $this->view->registerJs(<<<JS
{$this->_selectedItemsJson}.forEach(function (item, i, arr) {
    $('#{$widgetId}')
        .find('input[value="' + item + '"]')
        .trigger('click')
        .parents('ul.ufu-ctree-child')
        .each(function () {
            $(this).show();
            $(this).parent().find('> span.glyphicon-chevron-right').hide();
            $(this).parent().find('> span.glyphicon-chevron-down').show();
        });
});
JS
        );

        return $content;
    }

    /**
     * @return string
     */
    private function renderItems()
    {
        $preparedData = ArrayHelper::map(
            UfuCategory::find()
                ->joinWith('ufuCategoryTranslate')
                ->select([
                    'ufu_category.id',
                    'ufu_category.parent_id',
                    'ufu_category_translate.name',
                ])
                ->asArray()
                ->all(),
            'id',
            function ($element) {
                /* @var self $element */
                return [
                    'id' => (int)$element['id'],
                    'parent_id' => (int)$element['parent_id'],
                    'name' => (string)$element['name'],
                ];
            },
            'parent_id'
        );
        return $this->_renderItemsRecursive($preparedData);
    }

    /**
     * @param array $data
     * @param int   $parent_id
     * @param array $parentsList
     *
     * @return string
     */
    private function _renderItemsRecursive(&$data, $parent_id = 0, $parentsList = [])
    {
        $items = '';
        if (isset($data[$parent_id])) {
            foreach ($data[$parent_id] as $category) {
                $preparedParentsList = $parentsList;
                $preparedParentsList[] = $category['parent_id'];

                $items .= "<li ufu-ctree-cid=\"{$category['id']}\">\n";
                $childItems = $this->_renderItemsRecursive($data, $category['id'], $preparedParentsList);
                if (empty($childItems)) {
                    $items .= "<span class=\"ufu-ctree-nochevron\"></span>";
                } else {
                    $items .= "<span class=\"glyphicon glyphicon-chevron-right\"></span>";
                    $items .= "<span class=\"glyphicon glyphicon-chevron-down\"></span>";
                }
                if ($this->multiselect) {
                    $items .= Html::checkbox($this->name, FALSE, ['value' => $category['id']]);
                } else {
                    $items .= Html::radio($this->name, FALSE, ['value' => $category['id']]);
                }
                $items .= "\n";
                $items .= '<span class="ufu-ctree-item-label">' . (empty($category['name']) ? $this->emptyItemText : $category['name']) . "</span>\n";
                $items .= $childItems;
                $items .= "</li>\n";
            }
        }
        if (empty($items)) {
            return '';
        }
        return strtr("<ul class=\"{class}\">\n{items}</ul>\n", [
            '{class}' => $parent_id > 0 ? 'ufu-ctree-child' : '',
            '{items}' => $items,
        ]);
    }

}
