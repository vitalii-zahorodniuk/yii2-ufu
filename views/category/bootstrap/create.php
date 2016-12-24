<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model xz1mefx\ufu\models\UfuCategory */

$this->title = Yii::t('ufu', 'Create Ufu Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ufu', 'Ufu Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ufu-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
