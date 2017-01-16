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

$typeIsSet = $model->isNewRecord && $type;
?>

<div class="box box-primary">
    <div class="box-body">
        <div class="box-body-overflow">
            <?php $form = ActiveForm::begin([
                'id' => 'ufuCategoryForm',
                'enableAjaxValidation' => TRUE,
                'validateOnType' => TRUE,
            ]); ?>

            <?= $form->field($model, "needToUpdateTree")->hiddenInput(['value' => 1])->label(FALSE) ?>

            <?= $form->field($model, "type")
                ->dropDownList(
                    Yii::$app->ufu->getDrDownUrlTypes(),
                    [
                        'prompt' => Yii::t('ufu-tools', 'Select type...'),
                        'disabled' => ($typeIsSet || !$model->canUpdateType),
                    ])
            ?>

            <?php if ($typeIsSet || !$model->canUpdateType): ?>
                <?= $form->field($model, "type")->hiddenInput()->label(FALSE) /* default value for disabled type form field */ ?>
                <p class="text-info">
                    <strong><?= Html::icon('info-sign') ?> <?= Yii::t('ufu-tools', 'Warning:') ?></strong>
                    <?= Yii::t('ufu-tools', 'Change the type of category you can only for categories without relations, parents and children') ?>
                </p>
            <?php endif; ?>

            <?php if ($canSetSection): ?>
                <div id="categorySectionBlock"
                     style="display: <?= ($model->isNewRecord && !($type || $model->type)) ? 'none' : 'block' ?>;">
                    <?= $form->field($model, "is_section")->checkbox(['disabled' => !$model->canUpdateType]) ?>
                </div>
                <?php if (!$model->canUpdateType): ?>
                    <?= $form->field($model, "is_section")->hiddenInput()->label(FALSE) /* default value for disabled is_section form field */ ?>
                <?php endif; ?>
            <?php endif; ?>

            <div id="categoryTreeBlock"
                 style="display: <?= ($model->is_section || ($model->isNewRecord && !($type || $model->type))) ? 'none' : 'block' ?>;">
                <label><?= Yii::t('ufu-tools', 'Parent category') ?></label>
                <?= CategoryTreeWidget::widget([
                    'multiselect' => FALSE,
                    'name' => Html::getInputName($model, 'parent_id'),
                    'selectedItems' => $model->parent_id,
                    'ignoreItems' => $model->id,
                    'onlyType' => $typeIsSet ? $type : $model->type,
                ]) ?>
                <?= $form->field($model, "ctree_error", ['options' => ['class' => 'form-group', 'style' => 'margin-top: -15px;']])->hiddenInput()->label(FALSE) ?>
            </div>

            <?= $form->field($model, "url")->widget(UrlInputWidget::className()) ?>

            <h5><strong><?= $model->getAttributeLabel('name') ?></strong></h5>
            <div class="panel panel-default" style="background-color: #f6f8fa;">
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
    </div>
</div>

<?php
$this->registerJs(<<<JS
var urlHasBeenUpdated = false;
var ufuCategoryForm = $('#ufuCategoryForm');
var ufuCategoryType = $('#ufucategory-type');
var ctree = $('#ctree');
var ufucategoryIsSection = $('#ufucategory-is_section');
var categorySectionBlock = $('#categorySectionBlock');
var categoryTreeBlock = $('#categoryTreeBlock');
var ufuCategoryUrl = $('#ufucategory-url');

ufuCategoryType.on('change', function () {
    customValidation();
    ctree.find('input').prop('checked', false);
    ctree.find('input[value="0"]').prop('checked', true);
    if ($(this).val()) {
        categorySectionBlock.slideDown();
        ctree.categoryTreeView('showOnlyType', $(this).val());
        categoryTreeBlock.slideDown();
    }
    else {
        categorySectionBlock.slideUp();
        categoryTreeBlock.slideUp();
    }
});

ctree.on('click change', 'input', customValidation);

ufucategoryIsSection.on('click', function () {
    customValidation();
    ctree.find('input[value="0"]').prop('checked', true);
    if ($(this).is(':checked')) {
        categoryTreeBlock.slideUp();
    } else {
        categoryTreeBlock.slideDown();
    }
});

ufuCategoryUrl.on('keyup blur', function () {
    urlHasBeenUpdated = true;
});

function customValidation() {
    ufuCategoryForm.yiiActiveForm('validateAttribute', 'ufucategory-ctree_error');
    if (urlHasBeenUpdated || ufuCategoryUrl.val().length) {
        ufuCategoryForm.yiiActiveForm('validateAttribute', 'ufucategory-url');
    }
}
JS
);
?>
