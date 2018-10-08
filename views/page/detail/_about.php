<?php 

/* @var $businessName string */
/* @var $businessAbout string */ ?>

<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="box bg-white">
            <div class="box-title">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h4 class="mt-0 mb-0 inline-block"><?= Yii::t('app', 'About') . ' ' . $businessName ?> </h4>
                    </div>
                </div>
            </div>

            <hr class="divider-w">

            <div class="box-content mt-10">
            	<div class="row">
            		<div class="col-md-12 col-xs-12">

                        <?= !empty($businessAbout) ? $businessAbout : '<p>' . Yii::t('app', 'Data Not Available') . '.</p>' ?>
        
            		</div>
            	</div>
            </div>
        </div>
    </div>
</div>