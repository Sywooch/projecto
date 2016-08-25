<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\ProductsDescripton;
use app\models\Producers;
use app\models\Vats;
use app\models\Languages;
use yii\helpers\ArrayHelper;
use dosamigos\ckeditor\CKEditor
/* @var $this yii\web\View */
/* @var $model app\models\Products */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="products-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php 

        (isset($model->is_active) ? '':  $model->is_active = 1); 
        (isset($model->is_archive) ? '':  $model->is_archive = 0);
        (isset($model->vats_id) ? '':  $model->vats_id = 1);
        (isset($model->stock) ? '':  $model->stock= 99);
        (isset($oPD->languages_id) ? '':  $oPD->languages_id = 1);
    ?>
    <?= $form->field($model, 'is_active')->checkbox() ?>
    <?= $form->field($oPD, 'name', ['labelOptions' => ['class'=>'col-sm-2']])->textInput() ?>
    <?= $form->field($oPD, 'name_model', ['labelOptions' => ['class'=>'col-sm-2']])->textInput() ?>
    <?= $form->field($oPD, 'name_subname', ['labelOptions' => ['class'=>'col-sm-2']])->textInput() ?>
    <?= $form->field($oPD, 'html_description', ['labelOptions' => ['class'=>'col-sm-2']])->widget(CKEditor::className(), [
        'options' => ['rows' => 4],
        'preset' => 'standard'
    ]) ?>
    <?= $form->field($oPD, 'html_description_short', ['labelOptions' => ['class'=>'col-sm-2']])->textInput() ?>
    <?= $form->field($oPD, 'keywords', ['labelOptions' => ['class'=>'col-sm-2']])->textInput() ?>

    <?= $form->field($model, 'producers_id',['options' => ['class' => 'inline'], 'labelOptions' => ['class'=>'col-sm-2']])->dropDownList(ArrayHelper::map(Producers::find()->all(), 'id', 'name'), ['prompt' => '-=Dostawca=-'])?>


    <?= $form->field($model, 'vats_id', ['labelOptions' => ['class'=>'col-sm-2']])->dropDownList(ArrayHelper::map(Vats::find()->all(), 'id', 'name'), ['prompt' => '-=Vat=-'])?>

    <?= $form->field($model, 'price_brutto_source',['labelOptions' => ['class'=>'col-sm-2']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price_brutto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stock')->textInput() ?>


    <?= $form->field($model, 'symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ean')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sort_order')->textInput() ?>
    <?= $form->field($model, 'is_archive')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Dodaj') : Yii::t('app', 'Zmień'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
