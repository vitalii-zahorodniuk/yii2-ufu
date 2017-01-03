<?php
use yii\bootstrap\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model xz1mefx\ufu\models\UfuCategory */
/* @var $type integer|null */
/* @var $canUpdate bool */
/* @var $canDelete bool */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ufu-tools', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ufu-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($canUpdate || $canDelete): ?>
        <p>
            <?php if ($canDelete): ?>
                <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php endif; ?>
            <?php if ($canDelete): ?>
                <?= Html::a(Yii::t('ufu-tools', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('ufu-tools', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'type',
                'format' => 'raw',
            ],
            [
                'attribute' => 'parentName',
                'format' => 'raw',
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
