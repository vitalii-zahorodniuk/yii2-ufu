<?php
namespace xz1mefx\ufu\actions\category;

use xz1mefx\ufu\actions\BaseAction;
use xz1mefx\ufu\models\UfuCategory;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ViewAction
 *
 * @property string  $theme it can be IndexAction::THEME_BOOTSTRAP or IndexAction::THEME_ADMINLTE
 * @property string  $view  the view name (if need to override)
 *
 * @property boolean $canSetSection
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
        $model = UfuCategory::findOne($id);
        if ($model === NULL || (is_int($this->type) && $model->type != $this->type)) {
            throw new NotFoundHttpException(Yii::t('ufu-tools', 'The requested category does not exist'));
        }

        if ($model->is_section && !$this->canSetSection) {
            throw new ForbiddenHttpException();
        }

        return $this->controller->render(
            $this->view ?: "@vendor/xz1mefx/yii2-ufu/views/category/{$this->theme}/view",
            [
                'model' => $model,
                'type' => $this->type,
                'canUpdate' => $this->canUpdate,
                'canDelete' => $this->canDelete,
                'canSetSection' => $this->canSetSection,
            ]
        );
    }

}
