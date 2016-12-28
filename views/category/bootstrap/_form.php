<?php
use xz1mefx\ufu\widgets\CategoryTreeWidget;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model xz1mefx\ufu\models\UfuCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ufu-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= CategoryTreeWidget::widget([
        'multiselect' => FALSE,
        'name' => 'parent_id',
//        'selectedItem' => [5, 4, 1024, 2500],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('ufu-tools', 'Create') : Yii::t('ufu-tools', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
