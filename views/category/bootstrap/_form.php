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
$typeIsSet = $model->isNewRecord && $type;
?>

<div class="ufu-category-form">

    <?php $form = ActiveForm::begin(['enableAjaxValidation' => TRUE, 'validateOnType' => TRUE]); ?>

    <?php if ($typeIsSet || !$model->canUpdateType): ?>
        <?= $form->field($model, "type")->hiddenInput(['value' => $typeIsSet ? $type : $model->type])->label(FALSE) ?>
        <p class="text-info">
            <strong><?= Html::icon('info-sign') ?> <?= Yii::t('ufu-tools', 'Warning:') ?></strong>
            <?= Yii::t('ufu-tools', 'Change the type of category you can only for categories without relations, parents and children') ?>
        </p>
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
                'onlyType' => $typeIsSet ? $type : $model->type,
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
var ctree = $('#ctree');
var categoryTreeBlock = $('#categoryTreeBlock');
var ufucategoryIsParent = $('#ufucategory-is_parent');

$('#ufucategory-type').on('change', function () {
    ufucategoryIsParent.prop('checked', true);
    ctree.find('input').prop('checked', false);
    $('#categoryCommonBlock').slideDown();
    ctree.categoryTreeView('showOnlyType', $(this).val());
    categoryTreeBlock.hide();
});

ufucategoryIsParent.on('click', function () {
    if ($(this).is(':checked')) {
        categoryTreeBlock.slideUp();
    } else {
        categoryTreeBlock.slideDown();
    }
});
JS
);
?>
