<?php
namespace xz1mefx\ufu\actions\category;

use xz1mefx\ufu\actions\BaseAction;
use xz1mefx\ufu\models\search\UfuCategorySearch;
use Yii;

/**
 * Class IndexAction
 *
 * @property string  $theme it can be IndexAction::THEME_BOOTSTRAP or IndexAction::THEME_ADMINLTE
 * @property string  $view  the view name (if need to override)
 *
 * @property integer $type
 *
 * @property bool    $canAdd
 * @property bool    $canUpdate
 * @property bool    $canDelete
 *
 * @package xz1mefx\ufu\actions\category
 */
class IndexAction extends BaseAction
{

    public $canAdd = TRUE;
    public $canUpdate = TRUE;
    public $canDelete = TRUE;

    /**
     * @return string
     */
    public function run()
    {
        $searchModel = new UfuCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->controller->render(
            $this->view ?: "@vendor/xz1mefx/yii2-ufu/views/category/{$this->theme}/index",
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'type' => $this->type,
                'canAdd' => $this->canAdd,
                'canUpdate' => $this->canUpdate,
                'canDelete' => $this->canDelete,
            ]
        );
    }

}
