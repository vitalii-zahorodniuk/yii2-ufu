<?php
namespace xz1mefx\ufu\actions\category;

use xz1mefx\ufu\actions\BaseAction;
use xz1mefx\ufu\models\UfuCategory;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class UpdateAction
 *
 * @property string  $theme it can be IndexAction::THEME_BOOTSTRAP or IndexAction::THEME_ADMINLTE
 * @property string  $view  the view name (if need to override)
 *
 * @property boolean $canSetSection
 *
 * @property integer $type
 *
 * @package xz1mefx\ufu\actions\category
 */
class UpdateAction extends BaseAction
{

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
            throw new ForbiddenHttpException(Yii::t('ufu-tools', 'You have insufficient privileges!'));
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('ufu-tools', 'Category updated successfully!'));
            return $this->controller->redirect(['index']);
        }

        return $this->controller->render(
            $this->view ?: "@vendor/xz1mefx/yii2-ufu/views/category/{$this->theme}/update",
            [
                'model' => $model,
                'type' => $this->type,
                'canSetSection' => $this->canSetSection,
            ]
        );
    }

}
