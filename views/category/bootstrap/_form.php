<?php
use xz1mefx\ufu\widgets\CategoryTreeWidget;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model xz1mefx\ufu\models\UfuCategory */
/* @var $type integer|null */
/* @var $form yii\widgets\ActiveForm */

if ($model->isNewRecord) {
    $model->is_parent = 1;
}
?>

<div class="ufu-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, "type")->hiddenInput(['value' => 1])->label(FALSE) ?>

    <?= $form->field($model, "is_parent")->checkbox() ?>

    <div id="categoryTreeBlock" style="display: <?= $model->is_parent ? 'none' : 'block' ?>;">
        <label><?= $model->attributeLabels()['parent_id'] ?></label>
        <?= CategoryTreeWidget::widget([
            'multiselect' => FALSE,
            'name' => Html::getInputName($model, 'parent_id'),
            'selectedItems' => $model->parent_id,
            'ignoreItems' => $model->id,
            'onlyType' => $type,
        ]) ?>
    </div>

    <?= $form->field($model, "url")->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('ufu-tools', 'Create') : Yii::t('ufu-tools', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(<<<JS
$('#ufucategory-is_parent').on('click', function () {
    if ($(this).is(':checked')) {
        $('#categoryTreeBlock').slideUp();
    } else {
        $('#categoryTreeBlock').slideDown();
    }
});
JS
);
?>
