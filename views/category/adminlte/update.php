<?php

/* @var $this yii\web\View */
/* @var $model xz1mefx\ufu\models\UfuCategory */
/* @var $type integer|null */

$this->title = Yii::t('ufu-tools', 'Update category:') . ' ' . $model->id;

$this->params['breadcrumbs'][] = ['label' => Yii::t('ufu-tools', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('ufu-tools', 'Update');

$this->params['title'] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
    'type' => $type,
]) ?>
