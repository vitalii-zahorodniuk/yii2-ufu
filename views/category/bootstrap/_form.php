<?php
use xz1mefx\ufu\widgets\CategoryTreeWidget;
use xz1mefx\ufu\widgets\UrlInputWidget;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model xz1mefx\ufu\models\UfuCategory */
/* @var $type integer|null */
/* @var $form yii\widgets\ActiveForm */
/* @var $canSetSection boolean */

if ($model->isNewRecord) {
    $model->is_parent = 1;
}
$typeIsSet = $model->isNewRecord && $type;
?>

<div class="ufu-category-form">

    <?php $form = ActiveForm::begin([
        'id' => 'ufuCategoryForm',
        'enableAjaxValidation' => TRUE,
        'validateOnType' => TRUE,
    ]); ?>

    <?= $form->field($model, "needToUpdateTree")->hiddenInput(['value' => 1])->label(FALSE) ?>

    <?php if ($typeIsSet || !$model->canUpdateType): ?>
        <?= $form->field($model, "type")->hiddenInput(['value' => $typeIsSet ? $type : $model->type])->label(FALSE) ?>
        <p class="text-info">
            <strong><?= Html::icon('info-sign') ?> <?= Yii::t('ufu-tools', 'Warning:') ?></strong>
            <?= Yii::t('ufu-tools', 'Change the type of category you can only for categories without relations, parents and children') ?>
        </p>
    <?php else: ?>
        <?= $form->field($model, "type")->dropDownList(Yii::$app->ufu->getDrDownUrlTypes(), ['prompt' => Yii::t('ufu-tools', 'Select type...')]) ?>
    <?php endif; ?>

    <?php if ($canSetSection && $model->canUpdateType): ?>
        <?= $form->field($model, "is_section")->checkbox() ?>
    <?php endif; ?>

    <div id="categoryCommonBlock" style="display: <?= $model->isNewRecord && !$type ? 'none' : 'block' ?>;">
        <?= $form->field($model, "is_parent")->checkbox() ?>

        <div id="categoryTreeBlock" style="display: <?= $model->is_parent ? 'none' : 'block' ?>;">
            <label><?= Yii::t('ufu-tools', 'Parent category') ?></label>
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

    <h5><strong><?= $model->getAttributeLabel('name') ?></strong></h5>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php foreach (Yii::$app->lang->getLangList() as $lang): ?>
                <?= $form->field($model, "multilangNames[{$lang['id']}]")->textInput(['placeholder' => Yii::t('ufu-tools', 'Enter a name...', [], $lang['locale'])])->label($lang['name']) ?>
            <?php endforeach; ?>
        </div>
    </div>

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
var urlHasBeenUpdated = false;
var ctree = $('#ctree');
var categoryTreeBlock = $('#categoryTreeBlock');
var ufucategoryIsParent = $('#ufucategory-is_parent');

$('#ufucategory-type').on('change', function () {
    validateUrl();
    ufucategoryIsParent.prop('checked', true);
    ctree.find('input').prop('checked', false);
    $('#categoryCommonBlock').slideDown();
    ctree.categoryTreeView('showOnlyType', $(this).val());
    categoryTreeBlock.hide();
});

ctree.on('click change', 'input', validateUrl);

ufucategoryIsParent.on('click', function () {
    // ctree.find('input').prop('checked', false);
    validateUrl();
    if ($(this).is(':checked')) {
        categoryTreeBlock.slideUp();
    } else {
        categoryTreeBlock.slideDown();
    }
});

$('#ufucategory-url').on('keyup blur', function () {
    urlHasBeenUpdated = true;
});

function validateUrl() {
    if (urlHasBeenUpdated) {
        $('#ufuCategoryForm').yiiActiveForm('validateAttribute', 'ufucategory-url');
    }
}
JS
);
?>
