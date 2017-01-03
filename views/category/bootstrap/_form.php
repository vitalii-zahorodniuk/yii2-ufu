<?php
use xz1mefx\ufu\widgets\CategoryTreeWidget;
use xz1mefx\ufu\widgets\UrlInputWidget;
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

    <?php $form = ActiveForm::begin(['enableAjaxValidation' => TRUE, 'validateOnType' => TRUE]); ?>

    <?php if ($model->isNewRecord && $type): ?>
        <?= $form->field($model, "type")->hiddenInput(['value' => $type])->label(FALSE) ?>
    <?php else: ?>
        <?= $form->field($model, "type")->dropDownList(Yii::$app->ufu->getDrDownCategoryTypes(), ['prompt' => Yii::t('ufu-tools', 'Select type...')]) ?>
    <?php endif; ?>

    <div id="categoryCommonBlock" style="display: <?= $model->isNewRecord && !$type ? 'none' : 'block' ?>;">
        <?= $form->field($model, "is_parent")->checkbox() ?>

        <div id="categoryTreeBlock" style="display: <?= $model->is_parent ? 'none' : 'block' ?>;">
            <label><?= $model->attributeLabels()['parent_id'] ?></label>
            <?= CategoryTreeWidget::widget([
                'multiselect' => FALSE,
                'name' => Html::getInputName($model, 'parent_id'),
                'selectedItems' => $model->parent_id,
                'ignoreItems' => $model->id,
                'onlyType' => $model->isNewRecord && $type ? $type : $model->type,
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, "url")->widget(UrlInputWidget::className()) ?>

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
$('#ufucategory-type').on('change', function () {
    $('#ctree').categoryTreeView('showOnlyType', $(this).val());
    $('#categoryCommonBlock').slideDown();
});
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
