<?php 

/* @var $this yii\web\View */
/* @var $username string */
/* @var $queryParams array */ ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white p-0">
            <div class="box-content mt-20 p-0">

                <!-- Nav tabs -->
                <ul class="view-journey nav nav-tabs mb-10" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#view-review" aria-controls="view-review" role="tab" data-toggle="tab"><i class="aicon aicon-document-edit"></i> <?= Yii::t('app', 'Review') ?> (<span class="total-user-post"></span>)</a>
                    </li>
                    <li role="presentation">
                        <a href="#view-love" aria-controls="view-love" role="tab" data-toggle="tab"><i class="fa fa-heart"></i> Love (<span class="total-user-love"></span>)</a>
                    </li>
                    <li role="presentation">
                        <a href="#view-been-there" aria-controls="view-been-there" role="tab" data-toggle="tab"><i class="aicon aicon-icon-been-there"></i> Been There (<span class="total-user-visit"></span>)</a>
                    </li>
                </ul>

                <div class="tab-content p-15">
                    <div role="tabpanel" class="tab-pane fade in active p-0" id="view-review">
                        <?= $this->render('journey/_review', [
                            'username' => $username,
                            'queryParams' => $queryParams,
                        ]) ?>
                    </div>

                    <div role="tabpanel" class="tab-pane fade p-0" id="view-love">
                        <?= $this->render('journey/_love', [
                            'username' => $username,
                            'queryParams' => $queryParams,
                        ]) ?>
                    </div>

                    <div role="tabpanel" class="tab-pane fade p-0" id="view-been-there">
                        <?= $this->render('journey/_been_there', [
                            'username' => $username,
                            'queryParams' => $queryParams,
                        ]) ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>