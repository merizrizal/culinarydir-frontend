<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-title">
                <h4 class="mt-0 mb-0 inline-block">Menu</h4>
            </div>

            <hr class="divider-w">

            <?php
            if (!empty($modelBusinessProduct)): ?>

                <div class="box-content">
                    <div class="row">

                        <?php
                        foreach ($modelBusinessProduct as $dataBusinessProduct): ?>

                            <div class="col-lg-4 col-md-4 col-sm-6 col-tab-6 col-xs-12">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-tab-8 col-xs-8">
                                        <strong><?= $dataBusinessProduct['name'] ?></strong>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-tab-4 col-xs-4 pull-right">
                                        <strong><?= Yii::$app->formatter->asCurrency($dataBusinessProduct['price']) ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-tab-12 col-xs-9">
                                        <p>

                                            <?= $dataBusinessProduct['description'] ?>

                                        </p>
                                    </div>
                                </div>
                            </div>

                        <?php
                        endforeach; ?>

                    </div>
                </div>

            <?php
            else: ?>

                <div class="box-content mt-10">
                    <p>Saat ini menu belum tersedia.</p>
                </div>

            <?php
            endif; ?>

        </div>
    </div>
</div>