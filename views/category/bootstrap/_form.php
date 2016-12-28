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

        <?= $form->field($model, "isFirstSegment")->checkbox() ?>

        <div id="categoryTreeBlock" style="display: <?= $model->isFirstSegment ? 'none' : 'block' ?>;">
            <label><?= $model->attributeLabels()['parent_id'] ?></label>
            <?= CategoryTreeWidget::widget([
                'multiselect' => FALSE,
                'model' => $model,
                'name' => 'parent_id',
                'selectedItem' => $model->parent_id,
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
$('#ufucategory-isfirstsegment').on('click', function() {
    if ($(this).is(':checked')) {
        $('#categoryTreeBlock').slideUp();
    }else{
        $('#categoryTreeBlock').slideDown();
    }
});
JS
);
