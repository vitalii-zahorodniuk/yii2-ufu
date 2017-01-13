<?php
use xz1mefx\ufu\models\UfuCategory;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel xz1mefx\ufu\models\search\UfuCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type integer|null */
/* @var $canAdd bool */
/* @var $canUpdate bool */
/* @var $canDelete bool */
/* @var $canSetSection boolean */

$this->title = Yii::t('ufu-tools', 'Categories');

$this->params['breadcrumbs'][] = $this->title;

$this->params['title'] = $this->title;
?>

<div class="box box-primary">
    <div class="box-header">
        <?php if ($canAdd): ?>
            <?= Html::a(Yii::t('ufu-tools', 'Create Category'), ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                <?= Html::icon('minus', ['prefix' => 'fa fa-']) ?>
            </button>
        </div>
    </div>
    <div class="box-body">
        <?php if ($canDelete): ?>
            <p class="text-info">
                <strong><?= Html::icon('info-sign') ?> <?= Yii::t('ufu-tools', 'Warning:') ?></strong>
                <?= Yii::t('ufu-tools', 'You can delete the category only without relations, parents and children') ?>
            </p>
        <?php endif; ?>
        <div class="box-body-overflow">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::className()],

                    [
                        'attribute' => 'is_section',
                        'filter' => FALSE,
                        'content' => function ($model) {
                            /* @var $model UfuCategory */
                            return $model->is_section ? Html::icon('ok') : '';
                        },
                        'headerOptions' => ['class' => 'col-xs-1 col-sm-1'],
                        'contentOptions' => ['class' => 'col-xs-1 col-sm-1'],
                        'visible' => $canSetSection,
                    ],

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['class' => 'col-xs-1 col-sm-1'],
                        'contentOptions' => ['class' => 'col-xs-1 col-sm-1'],
                    ],
                    [
                        'attribute' => 'type',
                        'filter' => Yii::$app->ufu->getDrDownUrlTypes(),
                        'content' => function ($model) {
                            /* @var $model UfuCategory */
                            return (Yii::$app->ufu->getTypeNameById($model->type));
                        },
                        'visible' => !$type,
                    ],
//            [
//                'label' => Yii::t('ufu-tools', 'Is main category'),
//                'content' => function ($model) {
//                    /* @var $model UfuCategory */
//                    return $model->parent_id == 0 ? Html::icon('ok') : '';
//                },
//                'headerOptions' => ['class' => 'text-center col-xs-1 col-sm-1'],
//                'contentOptions' => ['class' => 'text-center col-xs-1 col-sm-1'],
//            ],
                    [
                        'attribute' => 'parentName',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'relationsCount',
                        'headerOptions' => ['class' => 'col-xs-1 col-sm-1'],
                        'contentOptions' => ['class' => 'col-xs-1 col-sm-1'],
                    ],
                    [
                        'attribute' => 'parentsCount',
                        'headerOptions' => ['class' => 'col-xs-1 col-sm-1'],
                        'contentOptions' => ['class' => 'col-xs-1 col-sm-1'],
                    ],
                    [
                        'attribute' => 'childrenCount',
                        'headerOptions' => ['class' => 'col-xs-1 col-sm-1'],
                        'contentOptions' => ['class' => 'col-xs-1 col-sm-1'],
                    ],

                    [
                        'class' => ActionColumn::className(),
                        'visible' => $canUpdate || $canDelete,
                        'headerOptions' => ['class' => 'text-center col-xs-1 col-sm-1'],
                        'contentOptions' => ['class' => 'text-center col-xs-1 col-sm-1'],
                        'template' => '{view} {update} {delete}',
                        'visibleButtons' => [
                            'update' => $canUpdate,
                            'delete' => function ($model, $key, $index) use ($canDelete) {
                                /* @var $model UfuCategory */
                                return $canDelete && $model->canDelete;
                            },
                        ],
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
