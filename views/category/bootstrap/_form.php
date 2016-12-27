<?php
use xz1mefx\widgets\BootstrapTreeViewInputWidget;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model xz1mefx\ufu\models\UfuCategory */
/* @var $form yii\widgets\ActiveForm */
\xz1mefx\ufu\models\UfuCategory::tryInitMainStaticVars();
?>

<div class="ufu-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_id')->widget(
        BootstrapTreeViewInputWidget::className(),
        [
            'pluginOptions' => [
                'onNodeExpanded' => new \yii\web\JsExpression('function(event, node) {console.log(event);console.log(node);}'),
                'showTags' => TRUE,
                'multiSelect' => TRUE,
            ],
            'pluginData' => [
                [
                    'item-id' => 22,
                    'text' => 'c1',
                    'tags' => ['c1'],
                    'state' => ['selected' => TRUE],
                ],
                [
                    'text' => 'c2',
                    'state' => [
                        'expanded' => FALSE,
                    ],
                    'nodes' => [
                        [
                            'text' => 'c1',
                        ],
                        [
                            'text' => 'c2',
                            'nodes' => [
                                [
                                    'text' => 'c1',
                                    'tags' => ['c1'],
                                    'state' => ['selected' => TRUE],
                                ],
                            ],
                        ],
                        [
                            'text' => 'c2',
                            'state' => [
                                'expanded' => FALSE,
                            ],
                            'nodes' => [
                                [
                                    'text' => 'c1',
                                ],
                                [
                                    'text' => 'c2',
                                    'nodes' => [
                                        [
                                            'text' => 'c1',
                                            'tags' => ['c1'],
                                            'state' => ['selected' => TRUE],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'text' => 'c1',
                ],
            ],
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('ufu-tools', 'Create') : Yii::t('ufu-tools', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
