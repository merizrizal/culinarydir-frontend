<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-title">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h4 class="mt-0 mb-0 inline-block"><?= 'About ' . $businessName ?> </h4>
                    </div>
                </div>
            </div>

            <hr class="divider-w">

            <div class="box-content mt-10">
                <p>

                    <?= !empty($businessAbout) ? $businessAbout : 'Data belum tersedia.' ?>

                </p>
            </div>
        </div>
    </div>
</div>