<?php
namespace xz1mefx\ufu\actions\category;

use xz1mefx\ufu\actions\BaseAction;
use xz1mefx\ufu\models\UfuCategory;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class DeleteAction
 *
 * @property string $theme it can be IndexAction::THEME_BOOTSTRAP or IndexAction::THEME_ADMINLTE
 * @property string $view  the view name (if need to override)
 *
 * @package xz1mefx\ufu\actions\category
 */
class DeleteAction extends BaseAction
{

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

        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('ufu-tools', 'Category deleted successfully!'));

        return $this->controller->redirect(['index']);
    }

}
