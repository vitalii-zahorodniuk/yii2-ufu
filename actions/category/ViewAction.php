<?php
namespace xz1mefx\ufu\actions\category;

use xz1mefx\ufu\actions\BaseAction;
use xz1mefx\ufu\models\UfuCategory;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class ViewAction
 *
 * @property string  $theme it can be IndexAction::THEME_BOOTSTRAP or IndexAction::THEME_ADMINLTE
 * @property string  $view  the view name (if need to override)
 *
 * @property bool    $canUpdate
 * @property bool    $canDelete
 *
 * @property integer $type
 *
 * @package xz1mefx\ufu\actions\category
 */
class ViewAction extends BaseAction
{

    public $canUpdate = TRUE;
    public $canDelete = TRUE;

    /**
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        if (($model = UfuCategory::findOne($id)) === NULL) {
            throw new NotFoundHttpException(Yii::t('ufu-tools', 'The requested category does not exist'));
        }

        return $this->controller->render(
            $this->view ?: "@vendor/xz1mefx/yii2-ufu/views/category/{$this->theme}/view",
            [
                'model' => $model,
                'type' => $this->type,
                'canUpdate' => $this->canUpdate,
                'canDelete' => $this->canDelete,
            ]
        );
    }

}
