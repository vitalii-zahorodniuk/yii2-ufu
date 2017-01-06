<?php
namespace xz1mefx\ufu\widgets;

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

    public $model;
    public $form;
    public $type;
    public $label;

    /**
     * @inheritdoc
     */
    public function run()
    {

        return $this->renderWidget();
    }

    public function renderWidget()
    {
        $content = '';
        $content .= '<label>';
        $content .= $this->label ?: \Yii::t('ufu-tools', "Select category");
        $content .= '</label>';
        $content .= "\n";
        $content .= CategoryTreeWidget::widget([
            'multiselect' => TRUE,
            'name' => Html::getInputName($this->model, 'category'),
//            'selectedItems' => $this->model->ufuCategoryRelations,
            'onlyType' => $this->type,
        ]);
        $content .= "\n";
        $content .= $this->form->field($this->model, "url")->widget(UrlInputWidget::className());
        $content .= "\n";
        return $content;
    }

}
