<?php
use yii\bootstrap\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel xz1mefx\ufu\models\search\UfuCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
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
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'parent_id',
            'ufuCategoryTranslate.name',

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
