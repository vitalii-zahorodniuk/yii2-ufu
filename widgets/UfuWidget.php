<?php
namespace xz1mefx\ufu\widgets;

use Yii;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\db\ActiveRecord;

/**
 * Class UfuWidget
 *
 * @property ActiveRecord $model
 * @property ActiveForm   $form
 * @property integer      $type
 * @property integer      $label
 *
 * @package xz1mefx\ufu\widgets
 */
class UfuWidget extends Widget
{

    public $label;

    public $model;
    public $form;

    public $categoryAttribute = 'categories';
    public $categoryMultiselect = FALSE;
    public $urlAttribute = 'url';
    public $type;
    public $disableRoot = TRUE;


    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->renderWidget();
    }

    public function renderWidget()
    {
        if (!empty($this->model->errors[$this->categoryAttribute])) {
            \Yii::$app->session->setFlash('error', $this->model->errors[$this->categoryAttribute]);
        }

        $categoriesName = Html::getInputName($this->model, $this->categoryAttribute);
        if ($this->categoryMultiselect && !preg_match('/\[\]$/', $categoriesName)) {
            $categoriesName .= '[]';
        }

        $content = '';
        $content .= $this->form->field($this->model, 'type')->hiddenInput(['value' => $this->type])->label(FALSE);
        $content .= "\n";
        $content .= Html::label($this->label ?: Yii::t('ufu-tools', 'Select the category:'));
        $content .= "\n";
        $content .= CategoryTreeWidget::widget([
            'multiselect' => $this->categoryMultiselect,
            'name' => $categoriesName,
            'selectedItems' => $this->model->{$this->categoryAttribute},
            'onlyType' => $this->type,
            'disableRoot' => $this->disableRoot,
        ]);
        $content .= "\n";
        $content .= $this->form->field($this->model, $this->urlAttribute)->widget(UrlInputWidget::className());
        $content .= "\n";

        return $content;
    }

}
