<?php
use yii\bootstrap\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model xz1mefx\ufu\models\UfuCategory */
/* @var $type integer|null */
/* @var $canUpdate bool */
/* @var $canDelete bool */
/* @var $canSetSection boolean */

$this->title = $model->id;

$this->params['breadcrumbs'][] = ['label' => Yii::t('ufu-tools', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['title'] = $this->title;
?>

<div class="box box-primary">
    <div class="box-header">
        <?php if ($canUpdate): ?>
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php else: ?>
            &nbsp;
        <?php endif; ?>
        <?php if ($canDelete && $model->canDelete): ?>
            <?= Html::a(Yii::t('ufu-tools', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('ufu-tools', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                <?= Html::icon('minus', ['prefix' => 'fa fa-']) ?>
            </button>
        </div>
    </div>
    <div class="box-body">
        <?php if ($canDelete && !$model->canDelete): ?>
            <p class="text-info">
                <strong><?= Html::icon('info-sign') ?> <?= Yii::t('ufu-tools', 'Warning:') ?></strong>
                <?= Yii::t('ufu-tools', 'You can delete the category only without relations, parents and children') ?>
            </p>
        <?php endif; ?>
        <div class="box-body-overflow">
            <?= DetailView::widget([
                'options' => ['class' => 'table table-striped table-bordered table-hover'],
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'typeName',
                    ],
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
                    ],
                    [
                        'attribute' => 'parentsCount',
                    ],
                    [
                        'attribute' => 'childrenCount',
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
