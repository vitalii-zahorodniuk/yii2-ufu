<?php
namespace xz1mefx\ufu\widgets;

use xz1mefx\ufu\models\UfuCategory;
use xz1mefx\ufu\web\assets\CategoryTreeWidgetAsset;
use Yii;
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
     * @var string
     */
    public $emptyLabelText = "<i>(noname)</i>";

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
    public $selectedItems = 0;

    /**
     * @var int|array
     */
    public $ignoreItems = [];

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
    public $onlyType = NULL;

    /**
     * @var string
     */
    public $rootLabelText = '';

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
        if (empty($this->selectedItems)) {
            $this->selectedItems = [0];
        }
        if (empty($this->rootLabelText)) {
            $this->rootLabelText = Yii::t('ufu-tools', 'The root of the site');
        }
        $widgetOptions = Json::encode([
            'data' => self::collectItemsTree(),
            'emptyLabelText' => $this->emptyLabelText,
            'multiselect' => $this->multiselect,
            'name' => $this->name,
            'selectedItems' => is_array($this->selectedItems) ? $this->selectedItems : [$this->selectedItems],
            'ignoreItems' => is_array($this->ignoreItems) ? $this->ignoreItems : [$this->ignoreItems],
            'showSelected' => $this->showSelected,
            'height' => $this->height,
            'onlyType' => ((is_bool($this->onlyType) || $this->onlyType === NULL) ? FALSE : (int)$this->onlyType),
            'rootLabelText' => $this->rootLabelText,
        ]);

        $this->view->registerJs("$('#ctree').categoryTreeView($widgetOptions);");

        return '<div id="ctree"></div>';
    }

    /**
     * @return array
     */
    public static function collectItemsTree()
    {
        $preparedData = ArrayHelper::map(
            UfuCategory::find()
                ->joinWith(['ufuCategoryTranslate', 'ufuUrl'])
                ->select([
                    UfuCategory::tableName() . '.id',
                    UfuCategory::tableName() . '.is_section',
                    UfuCategory::tableName() . '.parent_id',
                    UfuCategory::TABLE_ALIAS_UFU_CATEGORY_TRANSLATE . '.name',
                    UfuCategory::TABLE_ALIAS_UFU_URL . '.type',
                ])
                ->asArray()
                ->all(),
            'id',
            function ($element) {
                /* @var UfuCategory $element */
                return [
                    'id' => (int)$element['id'],
                    'is_section' => (int)$element['is_section'],
                    'parent_id' => (int)$element['parent_id'],
                    'name' => (string)$element['name'],
                    'type' => (int)$element['type'],
                ];
            },
            'parent_id'
        );

        return self::_collectItemsTreeRecursive($preparedData);
    }

    /**
     * @param array $data
     * @param int   $parent_id
     * @param array $parentsList
     *
     * @return array
     */
    private static function _collectItemsTreeRecursive(&$data, $parent_id = 0, $parentsList = [])
    {
        $res = [];
        if (isset($data[$parent_id])) {
            foreach ($data[$parent_id] as $category) {
                $preparedParentsIdsList = $parentsList;
                $preparedParentsIdsList[] = $category['parent_id'];

                $resCategoriesList[$category['id']] = [
                    'id' => $category['id'],
                    'is_section' => $category['is_section'],
                    'parent_id' => $category['parent_id'],
                    'parents_id_list' => $preparedParentsIdsList,
                    'name' => $category['name'],
                    'type' => $category['type'],
                ];

                $res[$category['id']] = $resCategoriesList[$category['id']];
                $res[$category['id']]['childs'] = self::_collectItemsTreeRecursive($data, $category['id'], $preparedParentsIdsList);
            }
        }

        return $res;
    }

}
