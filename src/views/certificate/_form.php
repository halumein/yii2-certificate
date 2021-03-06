<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\bootstrap\Modal;
use dvizh\certificate\assets\Asset;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

Asset::register($this);

?>

<div class="certificate-certificate-form">
    <div class="container-fluid">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'data-role' => 'send-form']) ?>
                    <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success hidden' : 'btn btn-primary hidden', 'data-role' => 'send-form2']) ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        if ($model->isNewRecord) {
                            $code = strtoupper(base_convert(uniqid(rand(), true), 5, 36));
                            $params = ['value' => $code];
                            $date = '';
                            $type = 'Количество | Сумма';
                        } else {
                            $params = [];
                            if (empty($model->date_elapsed)) {
                                $date = '';
                            } else {
                                $date = date('d.m.Y', strtotime($model->date_elapsed));
                            }
                            if ($model->type == 'item') {
                                $type = 'Количество использований';
                            } else
                                if ($model->type == 'sum') {
                                    $type = 'Сумма';
                                }
                        }
                        echo $form->field($model, 'code')->textInput($params)->label('Код сертификата');
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'employment')->dropDownList([
                            'reusable' => 'Многоразовый',
                            'disposable' => 'Одноразовый',
                        ],
                            [
                                'options' => [
                                    'reusable' => [
                                        'selected ' => true
                                    ]
                                ]
                            ],
                            [
                                'prompt' => 'Тип использования:'
                            ])->hint('В этом поле заполняется тип использования сертификата.')->label('Вид использования сертификата')
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'type')->dropDownList([
                            'item' => 'Количество применений',
                            'sum' => 'Сумма',
                        ],
                            [
                                'prompt' => 'Тип сертификата:'
                            ])->label('Тип сертификата')
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'status')->dropDownList([
                            'active' => 'Активен',
                            'elapsed' => 'Срок действия истек',
                            'empty' => 'Израсходован',
                            'banned' => 'Заблокирован',
                        ],
                            [
                                'options' => [
                                    'active' => [
                                        'selected ' => true
                                    ]
                                ]
                            ],
                            [
                                'prompt' => 'Статус сертиификата:'
                            ])->label('Статус') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'date_elapsed')->widget(DatePicker::classname(), [
                            'language' => 'ru',
                            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                            'options' => [
                                'placeholder' => 'Дата истечения сертификата',
                                'value' => $date,
                            ],
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'd.m.yyyy',
                            ],
                        ])->label('Дата истечения сертификата')->hint('Выберите дату истечения срока действия сертификата')
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        echo $form->field($model, 'target_user')->label('Клиент')
                            ->widget(Select2::classname(), [
                                'data' => ArrayHelper::map($clients, 'id', 'name'),
                                'language' => 'ru',
                                'options' => ['placeholder' => 'Выберите клиента ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'data-role' => 'send-form']) ?>
                    <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success hidden' : 'btn btn-primary hidden', 'data-role' => 'send-form2']) ?>
                </div>
            </div>
            <div class="col-md-6 certificate-right-column">
                <div class="row">
                    <?php foreach ($targetModelList as $modelName => $modelType){ ?>
                    <div class="col-md-4">
                    <?php
                    Modal::begin([
                        'header' => '<h2>Сертификат для: "' . $modelName . '"</h2>',
                        'size' => 'modal-lg',
                        'options' => [
                            'class' => 'product-modal',
                        ],
                        'toggleButton' => [
                            'tag' => 'button',
                            'class' => 'btn btn-sm  btn-primary btn-block form-group ',
                            'label' => $modelName . ' <i class="glyphicon glyphicon-plus"></i>',
                            'data-model' => $modelType['model'],
                        ]
                    ]);
                    ?>
                    <iframe src="<?= Url::to(['/certificate/tools/model-window', 'targetModel' => $modelName])  ?>" frameborder="0"
                            style="width: 100%; height: 500px;">
                    </iframe>
                    <?php
                    Modal::end();
                    ?>
                </div>
                <?php } ?>
            </div>
            <br>
            <div class="row">
                <table class="table table-bordered table-striped table-condensed">
                    <tbody data-role="model-list" id="modelList">
                    <tr>
                        <th>Наименование</th>
                        <th data-role="certificate-type"><?= $type ?></th>
                        <th>Удаление</th>
                    </tr>
                    <?php
                    if (isset($items)) {
                        foreach ($items as $item) {
                            foreach ($item as $item_id => $item_attr) {
                                ?>
                                <tr data-role="item">
                                    <td><label>
                                            <?= $item_attr['name'] ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" data-role="product-model-amount"
                                               name="targetModels<?= $item_id ?>"
                                               data-name="<?= str_replace(['[', '\\', ']'], '', $item_id) ?>"
                                               value="<?= $item_attr['amount'] ?>" required>
                                    </td>
                                    <td>
                                            <span data-href="ajax-delete-target-item"
                                                  class="btn glyphicon glyphicon-remove" style="color: red;"
                                                  data-role="remove-target-item"
                                                  data-target-model="<?= $item_attr['model'] ?>"
                                                  data-target-model-id="<?= $item_attr['model_id'] ?>"></span>
                                    </td>

                                </tr>
                            <?php }
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <div class="hidden" data-role="error-validate">
                    <div class="alert alert-danger text-center">
                        Вы не добавили ни одного элемента в сертификат!
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php if ($model->getTransactions()->all()) { ?>
                        <h3>История использований</h3>
                        <div class="table-responsive">
                            <table id="scrolling-table" class="table table-bordered table-hover scrolling-table">
                                <thead>
                                <tr class="fixed">
                                    <th>Дата использования</th>
                                    <th><?= $type ?></th>
                                    <th>Номер заказа</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($model->getTransactions()->orderBy(['date' => SORT_DESC])->all() as $certificateUse) { ?>
                                    <tr>
                                        <td><?= date('d.m.Y H:i:s', strtotime($certificateUse->date)) ?></td>
                                        <td><?= $certificateUse->amount ?></td>
                                        <td>
                                            <a href="<?= Url::to(['/order/order/view', 'id' => $certificateUse->order_id]) ?>"><?= $certificateUse->order_id ?></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>
