<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\FaqGroup */

$this->title = 'Dodaj grupę';
$this->params['breadcrumbs'][] = ['label' => 'Faq Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faq-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
