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

$this->title = Yii::t('ufu-tools', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ufu-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($canAdd): ?>
        <p>
            <?= Html::a(Yii::t('ufu-tools', 'Create Category'), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],

            [
                'attribute' => 'id',
                'headerOptions' => ['class' => 'col-xs-1 col-sm-1'],
                'contentOptions' => ['class' => 'col-xs-1 col-sm-1'],
            ],
            [
                'attribute' => 'type',
                'filter' => Yii::$app->ufu->getDrDownCategoryTypes(),
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
                'class' => ActionColumn::className(),
                'visible' => $canUpdate || $canDelete,
                'headerOptions' => ['class' => 'text-center col-xs-1 col-sm-1'],
                'contentOptions' => ['class' => 'text-center col-xs-1 col-sm-1'],
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'update' => $canUpdate,
                    'delete' => $canDelete,
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
