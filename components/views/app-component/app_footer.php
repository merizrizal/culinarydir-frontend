<div class="module-small bg-dark" id="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="widget">
                    <h5 class="widget-title font-alt"><?= \Yii::t('app', 'About Us') ?></h5>
                    <ul class="icon-list">
                        <li><a href="">Tentang Kami</a></li>
                        <li><a href="">Beriklan Dengan Kami</a></li>
                        <li><a href="">Daftar Fotografer</a></li>
                        <li><a href="">Karir</a></li>
                        <li><a href="">Syarat Dan Ketentuan</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="widget">
                    <h5 class="widget-title font-alt"><?= \Yii::t('app', 'Help Centre') ?></h5>
                    <ul class="icon-list">
                        <li><i class="aicon aicon-phone1"></i> (+62) 813 8888 7777</li>
                        <li><i class="aicon aicon-mail2"></i> customercare@kulinerbandungclub</li>
                        <li><strong><?= \Yii::t('app', 'Operational Hours') ?>:</strong></li>
                        <li><i class="aicon aicon-icon-calendar-line"></i> Senin s/d Jumat</li>
                        <li><i class="aicon aicon-clock"></i> 09:30 - 17:00</li>
                    </ul>
                </div>
            </div>

            <div class="visible-sm col-sm-12 mt-20 clearfix">&nbsp;</div>

            <div class="col-md-4 col-sm-12 col-xs-12">
                <div class="widget">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="widget-title font-alt">&nbsp;</h5>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-sm-12">
                            <img class="img-responsive img-component" src="<?= \Yii::$app->urlManager->baseUrl . '/media/img/logo-footer.png' ?>">
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-sm-12">
                            <a href="https://play.google.com/store/apps/details?id=com.asikmakan.app&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1"><img alt="Temukan di Google Play" src="https://play.google.com/intl/en_us/badges/images/generic/id_badge_web_generic.png"/></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr class="divider-d">
<footer class="footer bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <p class="copyright font-alt">&copy; <?= \Yii::$app->formatter->asDate(time(), 'yyyy') ?> All Rights Reserved</p>
            </div>
        </div>
    </div>
</footer>