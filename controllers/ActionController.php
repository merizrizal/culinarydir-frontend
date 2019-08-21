<?php
namespace frontend\controllers;

use core\models\BusinessDetail;
use core\models\BusinessDetailVote;
use core\models\PromoItem;
use core\models\UserLove;
use core\models\UserPost;
use core\models\UserPostComment;
use core\models\UserPostLove;
use core\models\UserPostMain;
use core\models\UserPromoItem;
use core\models\UserReport;
use core\models\UserVisit;
use core\models\UserVote;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Action Controller
 */
class ActionController extends base\BaseController
{

    /**
     *
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'submit-review' => ['POST'],
                        'submit-comment' => ['POST'],
                        'submit-likes' => ['POST'],
                        'submit-photo' => ['POST'],
                        'submit-user-love' => ['POST'],
                        'submit-user-visit' => ['POST'],
                        'submit-report' => ['POST'],
                        'claim-promo' => ['POST']
                    ]
                ]
            ]);
    }

    public function actionSubmitReview()
    {
        if (!empty(($post = \Yii::$app->request->post()))) {

            $modelUserPostMain = UserPostMain::find()
                ->joinWith([
                    'userPostComments'
                ])
                ->andWhere(['unique_id' => $post['business_id'] . '-' . \Yii::$app->user->getIdentity()->id])
                ->one();

            if (!empty($modelUserPostMain)) {

                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $this->updateReview($post, $modelUserPostMain);
            } else {

                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $this->createReview($post);
            }
        }
    }

    public function actionSubmitComment()
    {
        if (!empty(($post = \Yii::$app->request->post()))) {

            $flag = false;

            $modelUserPostMain = UserPostMain::find()
                ->andWhere(['id' => $post['user_post_main_id']])
                ->andWhere(['is_publish' => true])
                ->asArray()->one();

            if (!empty($modelUserPostMain)) {

                $modelUserPostComment = new UserPostComment();

                $modelUserPostComment->user_post_main_id = $post['user_post_main_id'];
                $modelUserPostComment->user_id = \Yii::$app->user->getIdentity()->id;
                $modelUserPostComment->text = $post['text'];

                $flag = $modelUserPostComment->save();
            }

            $result = [];

            if ($flag) {

                $result['success'] = true;
                $result['user_post_main_id'] = $modelUserPostComment->user_post_main_id;
            } else {

                $result['success'] = false;
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal';
                $result['message'] = 'Komentar Anda gagal disimpan';
                $result['type'] = 'danger';
            }

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitLikes()
    {
        if (!empty(($post = \Yii::$app->request->post()))) {

            $transaction = \Yii::$app->db->beginTransaction();
            $flag = false;

            $modelUserPostMain = UserPostMain::find()
                ->andWhere(['id' => $post['user_post_main_id']])
                ->andWhere(['is_publish' => true])
                ->one();

            if (!empty($modelUserPostMain)) {

                $modelUserPostLove = UserPostLove::find()
                    ->andWhere(['unique_id' => $post['user_post_main_id'] . '-' . \Yii::$app->user->getIdentity()->id])
                    ->one();

                if (!empty($modelUserPostLove)) {

                    $modelUserPostLove->is_active = !$modelUserPostLove->is_active;
                } else {

                    $modelUserPostLove = new UserPostLove();

                    $modelUserPostLove->user_post_main_id = $post['user_post_main_id'];
                    $modelUserPostLove->user_id = \Yii::$app->user->getIdentity()->id;
                    $modelUserPostLove->is_active = true;
                    $modelUserPostLove->unique_id = $post['user_post_main_id'] . '-' . \Yii::$app->user->getIdentity()->id;
                }

                if (($flag = $modelUserPostLove->save())) {

                    if ($modelUserPostLove->is_active) {

                        $modelUserPostMain->love_value += 1;
                    } else {

                        $modelUserPostMain->love_value -= 1;
                    }

                    $flag = $modelUserPostMain->save();
                }
            }

            $result = [];

            if ($flag) {

                $transaction->commit();

                $result['success'] = true;
                $result['is_active'] = $modelUserPostLove->is_active;
            } else {

                $transaction->rollBack();

                $result['success'] = false;
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal';
                $result['message'] = 'Proses like gagal disimpan';
                $result['type'] = 'danger';
            }

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitPhoto()
    {
        if (!empty(($post = \Yii::$app->request->post()))) {

            $transaction = \Yii::$app->db->beginTransaction();
            $flag = false;

            $modelUserPostMainPhoto = new UserPostMain();

            $image = Tools::uploadFileWithoutModel('/img/user_post/', 'Post[image]', $modelUserPostMainPhoto->id, '', true);

            $modelUserPostMainPhoto->unique_id = \Yii::$app->security->generateRandomString();
            $modelUserPostMainPhoto->business_id = $post['business_id'];
            $modelUserPostMainPhoto->user_id = \Yii::$app->user->getIdentity()->id;
            $modelUserPostMainPhoto->type = 'Photo';
            $modelUserPostMainPhoto->text = $post['Post']['text'];
            $modelUserPostMainPhoto->image = $image;
            $modelUserPostMainPhoto->is_publish = true;
            $modelUserPostMainPhoto->love_value = 0;

            $flag = $modelUserPostMainPhoto->save();

            $modelUserPostMainPhoto->image = \Yii::$app->params['endPointLoadImage'] . 'user-post?image=' . $modelUserPostMainPhoto->image;

            $result = [];

            if ($flag) {

                $transaction->commit();

                $result['userPostMainPhoto'] = $modelUserPostMainPhoto->toArray();
                $result['success'] = true;
                $result['icon'] = 'aicon aicon-icon-tick-in-circle';
                $result['title'] = 'Upload Foto Sukses';
                $result['message'] = 'Foto Anda berhasil disimpan';
                $result['type'] = 'success';
            } else {

                $transaction->rollBack();

                $result['success'] = false;
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Upload Foto Gagal';
                $result['message'] = '
                    Foto Anda gagal disimpan<br>
                    <ol>
                        <li>Pastikan panjang caption Anda lebih dari 20 karakter.</li>
                        <li>Pastikan Anda memilih foto dengan ukuran max. 2Mb.</li>
                    </ol>
                ';
                $result['type'] = 'danger';
            }

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitUserLove()
    {
        if (!empty(($post = \Yii::$app->request->post()))) {

            $transaction = \Yii::$app->db->beginTransaction();
            $flag = false;

            $modelUserLove = UserLove::find()
                ->andWhere(['unique_id' => \Yii::$app->user->getIdentity()->id . '-' . $post['business_id']])
                ->one();

            if (!empty($modelUserLove)) {

                $modelUserLove->is_active = !$modelUserLove->is_active;
            } else {

                $modelUserLove = new UserLove();

                $modelUserLove->business_id = $post['business_id'];
                $modelUserLove->user_id = \Yii::$app->user->getIdentity()->id;
                $modelUserLove->is_active = true;
                $modelUserLove->unique_id = \Yii::$app->user->getIdentity()->id . '-' . $post['business_id'];
            }

            if (($flag = $modelUserLove->save())) {

                $modelBusinessDetail = BusinessDetail::find()
                    ->where(['business_id' => $post['business_id']])
                    ->one();

                if ($modelUserLove->is_active) {

                    $modelBusinessDetail->love_value += 1;
                } else {

                    $modelBusinessDetail->love_value -= 1;
                }

                $flag = $modelBusinessDetail->save();
            }

            $result = [];

            if ($flag) {

                $transaction->commit();

                $result['success'] = true;
                $result['is_active'] = $modelUserLove->is_active;
            } else {

                $transaction->rollBack();

                $result['success'] = false;
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal';
                $result['message'] = 'Proses love gagal disimpan';
                $result['type'] = 'danger';
            }

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitUserVisit()
    {
        if (!empty(($post = \Yii::$app->request->post()))) {

            $transaction = \Yii::$app->db->beginTransaction();
            $flag = false;

            $modelUserVisit = UserVisit::find()
                ->andWhere(['unique_id' => \Yii::$app->user->getIdentity()->id . '-' . $post['business_id']])
                ->one();

            if (!empty($modelUserVisit)) {

                $modelUserVisit->is_active = !$modelUserVisit->is_active;
            } else {

                $modelUserVisit = new UserVisit();

                $modelUserVisit->business_id = $post['business_id'];
                $modelUserVisit->user_id = \Yii::$app->user->getIdentity()->id;
                $modelUserVisit->is_active = true;
                $modelUserVisit->unique_id = \Yii::$app->user->getIdentity()->id . '-' . $post['business_id'];
            }

            if (($flag = $modelUserVisit->save())) {

                $modelBusinessDetail = BusinessDetail::find()
                    ->where(['business_id' => $post['business_id']])
                    ->one();

                if ($modelUserVisit->is_active) {

                    $modelBusinessDetail->visit_value += 1;
                } else {

                    $modelBusinessDetail->visit_value -= 1;
                }

                $flag = $modelBusinessDetail->save();
            }

            $result = [];

            if ($flag) {

                $transaction->commit();

                $result['success'] = true;
                $result['is_active'] = $modelUserVisit->is_active;
            } else {

                $transaction->rollBack();

                $result['success'] = false;
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal';
                $result['message'] = 'Proses been here gagal disimpan';
                $result['type'] = 'danger';
            }

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitReport()
    {
        if (!empty(($post = \Yii::$app->request->post()))) {

            $modelUserReport = new UserReport();

            $modelUserReport->business_id = $post['business_id'];
            $modelUserReport->user_id = \Yii::$app->user->getIdentity()->id;
            $modelUserReport->report_status = $post['UserReport']['report_status'];
            $modelUserReport->text = $post['UserReport']['text'];

            $result = [];

            if ($modelUserReport->save()) {

                $result['success'] = true;
                $result['icon'] = 'aicon aicon-icon-tick-in-circle';
                $result['title'] = 'Report Berhasil';
                $result['message'] = 'Report Anda berhasil disimpan.';
                $result['type'] = 'success';
            } else {

                $result['success'] = false;
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Report Gagal';
                $result['message'] = 'Report Anda gagal disimpan.';
                $result['type'] = 'danger';
            }

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionClaimPromo()
    {
        $post = \Yii::$app->request->post();

        $modelPromoItem = PromoItem::find()
            ->joinWith([
                'userPromoItem',
                'promo',
            ])
            ->andWhere(['promo_item.promo_id' => $post['promo_id']])
            ->andWhere(['promo_item.business_claimed' => null])
            ->andWhere(['promo_item.not_active' => false])
            ->andWhere(['user_promo_item.user_id' => null])
            ->asArray()->one();

        $result = [];

        if (!empty($modelPromoItem)) {

            $modelUserPromoItem = new UserPromoItem();
            $modelUserPromoItem->promo_item_id = $modelPromoItem['id'];
            $modelUserPromoItem->promo_id = $modelPromoItem['promo_id'];
            $modelUserPromoItem->user_id = \Yii::$app->user->getIdentity()->id;
            $modelUserPromoItem->unique_id = $modelPromoItem['promo_id'] . '-' . $modelUserPromoItem->user_id;

            if ($modelUserPromoItem->save()) {

                \Yii::$app->mailer->compose(['html' => 'claim_promo'], [
                    'modelPromoItem' => $modelPromoItem,
                    'fullName' => \Yii::$app->user->getIdentity()->full_name,
                    'dateStart' => \Yii::$app->formatter->asDate($modelPromoItem['promo']['date_start'], 'long'),
                    'dateEnd' => \Yii::$app->formatter->asDate($modelPromoItem['promo']['date_end'], 'long')
                ])
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' Support'])
                ->setTo(\Yii::$app->user->getIdentity()->email)
                ->setSubject(\Yii::$app->name . ' Promo Code')
                ->send();

                $result['success'] = true;
                $result['icon'] = 'aicon aicon-icon-tick-in-circle';
                $result['title'] = 'Claim Promo Berhasil';
                $result['message'] = 'Harap periksa email anda untuk mendapatkan kode promo.';
                $result['type'] = 'success';
            } else {

                $result['success'] = false;
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Claim Promo Gagal';
                $result['message'] = 'Anda gagal mengklaim promo ini.';
                $result['type'] = 'danger';
            }
        } else {

            $result['success'] = false;
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Claim Promo Gagal';
            $result['message'] = 'Promo sudah habis.';
            $result['type'] = 'danger';
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    private function createReview($post)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        $flag = false;
        $result = [];

        $modelUserPostMain = new UserPostMain();

        $modelUserPostMain->unique_id = $post['business_id'] . '-' . \Yii::$app->user->getIdentity()->id;
        $modelUserPostMain->business_id = $post['business_id'];
        $modelUserPostMain->user_id = \Yii::$app->user->getIdentity()->id;
        $modelUserPostMain->type = 'Review';
        $modelUserPostMain->text = preg_replace("/\r\n/", "", $post['Post']['review']['text']);
        $modelUserPostMain->is_publish = true;
        $modelUserPostMain->love_value = 0;

        $flag = $modelUserPostMain->save();

        if ($flag) {

            $modelUserPost = new UserPost();

            $modelUserPost->business_id = $modelUserPostMain->business_id;
            $modelUserPost->type = $modelUserPostMain->type;
            $modelUserPost->user_id = $modelUserPostMain->user_id;
            $modelUserPost->text = $modelUserPostMain->text;
            $modelUserPost->is_publish = $modelUserPostMain->is_publish;
            $modelUserPost->love_value = $modelUserPostMain->love_value;
            $modelUserPost->user_post_main_id = $modelUserPostMain->id;

            $flag = $modelUserPost->save();
        }

        if ($flag) {

            $images = Tools::uploadFilesWithoutModel('/img/user_post/', 'Post[photo][image]', $modelUserPostMain->id, '', true);

            $dataUserPostMainPhoto = [];

            foreach ($images as $photoIndex => $image) {

                $modelUserPostMainPhoto = new UserPostMain();

                $modelUserPostMainPhoto->parent_id = $modelUserPostMain->id;
                $modelUserPostMainPhoto->unique_id = \Yii::$app->security->generateRandomString() . $photoIndex;
                $modelUserPostMainPhoto->business_id = $post['business_id'];
                $modelUserPostMainPhoto->user_id = \Yii::$app->user->getIdentity()->id;
                $modelUserPostMainPhoto->type = 'Photo';
                $modelUserPostMainPhoto->image = $image;
                $modelUserPostMainPhoto->is_publish = true;
                $modelUserPostMainPhoto->love_value = 0;

                if (($flag = $modelUserPostMainPhoto->save())) {

                    $modelUserPostMainPhoto->image = \Yii::$app->params['endPointLoadImage'] . 'user-post?image=' . $modelUserPostMainPhoto->image . '&w=72&h=72';

                    array_push($dataUserPostMainPhoto, $modelUserPostMainPhoto->toArray());

                    $modelUserPostPhoto = new UserPost();

                    $modelUserPostPhoto->parent_id = $modelUserPost->id;
                    $modelUserPostPhoto->business_id = $modelUserPostMainPhoto->business_id;
                    $modelUserPostPhoto->type = $modelUserPostMainPhoto->type;
                    $modelUserPostPhoto->user_id = $modelUserPostMainPhoto->user_id;
                    $modelUserPostPhoto->image = $modelUserPostMainPhoto->image;
                    $modelUserPostPhoto->is_publish = $modelUserPostMainPhoto->is_publish;
                    $modelUserPostPhoto->love_value = $modelUserPostMainPhoto->love_value;

                    if (!($flag = $modelUserPostPhoto->save())) {

                        break;
                    }
                } else {

                    break;
                }
            }
        }

        if ($flag) {

            foreach ($post['Post']['review']['rating'] as $ratingComponentId => $voteValue) {

                $modelUserVote = new UserVote();

                $modelUserVote->rating_component_id = $ratingComponentId;
                $modelUserVote->vote_value = $voteValue;
                $modelUserVote->user_post_main_id = $modelUserPostMain->id;

                if (!($flag = $modelUserVote->save())) {

                    $result['errorVote'] = $modelUserVote->getErrors();
                    break;
                }
            }
        }

        if ($flag) {

            $modelBusinessDetail = BusinessDetail::find()
                ->joinWith(['business'])
                ->andWhere(['business_id' => $modelUserPostMain->business_id])
                ->one();

            foreach ($post['Post']['review']['rating'] as $votePoint) {

                $modelBusinessDetail->total_vote_points += $votePoint;
            }

            $modelBusinessDetail->voters += 1;
            $modelBusinessDetail->vote_points = $modelBusinessDetail->total_vote_points / count($post['Post']['review']['rating']);
            $modelBusinessDetail->vote_value = $modelBusinessDetail->vote_points / $modelBusinessDetail->voters;

            $flag = $modelBusinessDetail->save();
        }

        if ($flag) {

            foreach ($post['Post']['review']['rating'] as $ratingComponentId => $votePoint) {

                $modelBusinessDetailVote = BusinessDetailVote::find()
                    ->andWhere(['business_id' => $modelUserPostMain->business_id])
                    ->andWhere(['rating_component_id' => $ratingComponentId])
                    ->one();

                if (empty($modelBusinessDetailVote)) {

                    $modelBusinessDetailVote = new BusinessDetailVote();

                    $modelBusinessDetailVote->business_id = $post['business_id'];
                    $modelBusinessDetailVote->rating_component_id = $ratingComponentId;
                }

                $modelBusinessDetailVote->total_vote_points += $votePoint;
                $modelBusinessDetailVote->vote_value = $modelBusinessDetailVote->total_vote_points / $modelBusinessDetail->voters;

                if (!($flag = $modelBusinessDetailVote->save())) {

                    break;
                }
            }
        }

        if ($flag) {

            $transaction->commit();

            $result['success'] = true;
            $result['icon'] = 'aicon aicon-icon-tick-in-circle';
            $result['title'] = 'Review Tersimpan';
            $result['message'] = 'Review anda berhasil disimpan';
            $result['type'] = 'success';
            $result['updated'] = false;
            $result['user'] = \Yii::$app->user->getIdentity()->full_name;
            $result['userCreated'] = \Yii::$app->formatter->asRelativeTime($modelUserPostMain->created_at);
            $result['userPostMain'] = $modelUserPostMain->toArray();
            $result['userPostMainPhoto'] = $dataUserPostMainPhoto;
            $result['shareUrl']['notAbsolute'] = \Yii::$app->urlManager->createUrl(['page/review', 'id' => $modelUserPostMain->id, 'uniqueName' => $modelBusinessDetail->business->unique_name]);
            $result['shareUrl']['absolute'] = \Yii::$app->urlManager->createAbsoluteUrl($result['shareUrl']['notAbsolute']);
            $result['deleteUrlPhoto'] = \Yii::$app->urlManager->createUrl(['user-action/delete-photo']);
            $result['deleteUrlReview'] = \Yii::$app->urlManager->createUrl(['user-action/delete-user-post', 'id' => $modelUserPostMain->id]);
        } else {

            $transaction->rollBack();

            $result['success'] = false;
        }

        return $result;
    }

    private function updateReview($post, $modelUserPostMain = [])
    {
        $transaction = \Yii::$app->db->beginTransaction();
        $flag = false;
        $result = [];

        $isUpdate = $modelUserPostMain->is_publish;

        $modelUserPostMain->text = preg_replace("/\r\n/", " ", $post['Post']['review']['text']);
        $modelUserPostMain->is_publish = true;
        $modelUserPostMain->created_at = \Yii::$app->formatter->asDatetime(time());

        $flag = $modelUserPostMain->save();

        if ($flag) {

            $modelUserPost = new UserPost();

            $modelUserPost->business_id = $modelUserPostMain->business_id;
            $modelUserPost->type = $modelUserPostMain->type;
            $modelUserPost->user_id = $modelUserPostMain->user_id;
            $modelUserPost->text = $modelUserPostMain->text;
            $modelUserPost->is_publish = $modelUserPostMain->is_publish;
            $modelUserPost->love_value = $modelUserPostMain->love_value;
            $modelUserPost->user_post_main_id = $modelUserPostMain->id;

            $flag = $modelUserPost->save();
        }

        if ($flag) {

            $images = Tools::uploadFilesWithoutModel('/img/user_post/', 'Post[photo][image]', $modelUserPostMain->id, '', true);

            $dataUserPostMainPhoto = [];

            foreach ($images as $photoIndex => $image) {

                $modelUserPostMainPhoto = new UserPostMain();

                $modelUserPostMainPhoto->parent_id = $modelUserPostMain->id;
                $modelUserPostMainPhoto->unique_id = \Yii::$app->security->generateRandomString() . $photoIndex;
                $modelUserPostMainPhoto->business_id = $post['business_id'];
                $modelUserPostMainPhoto->user_id = \Yii::$app->user->getIdentity()->id;
                $modelUserPostMainPhoto->type = 'Photo';
                $modelUserPostMainPhoto->image = $image;
                $modelUserPostMainPhoto->is_publish = true;
                $modelUserPostMainPhoto->love_value = 0;

                if (($flag = $modelUserPostMainPhoto->save())) {

                    $modelUserPostMainPhoto->image = \Yii::$app->params['endPointLoadImage'] . 'user-post?image=' . $modelUserPostMainPhoto->image . '&w=72&h=72';

                    array_push($dataUserPostMainPhoto, $modelUserPostMainPhoto->toArray());

                    $modelUserPostPhoto = new UserPost();

                    $modelUserPostPhoto->parent_id = $modelUserPost->id;
                    $modelUserPostPhoto->business_id = $modelUserPostMainPhoto->business_id;
                    $modelUserPostPhoto->type = $modelUserPostMainPhoto->type;
                    $modelUserPostPhoto->user_id = $modelUserPostMainPhoto->user_id;
                    $modelUserPostPhoto->image = $modelUserPostMainPhoto->image;
                    $modelUserPostPhoto->is_publish = $modelUserPostMainPhoto->is_publish;
                    $modelUserPostPhoto->love_value = $modelUserPostMainPhoto->love_value;

                    if (!($flag = $modelUserPostPhoto->save())) {

                       break;
                    }
                } else {

                    break;
                }
            }

            if ($flag && !empty($post['ImageReviewDelete'])) {

                $modelUserPhoto = UserPostMain::findAll(['parent_id' => $modelUserPostMain->id]);

                foreach ($modelUserPhoto as $dataUserPhoto) {

                    foreach ($post['ImageReviewDelete'] as $deletedPhotoId) {

                        if ($deletedPhotoId == $dataUserPhoto->id) {

                            $dataUserPhoto->is_publish = false;

                            if (!($flag = $dataUserPhoto->save())) {

                                break 2;
                            } else {

                                break;
                            }
                        }
                    }
                }
            }
        }

        if ($flag) {

            $prevUserVote = [];
            $prevUserVoteTotal = 0;

            foreach ($post['Post']['review']['rating'] as $ratingComponentId => $voteValue) {

                $modelUserVote = UserVote::find()
                    ->andWhere(['user_post_main_id' => $modelUserPostMain->id])
                    ->andWhere(['rating_component_id' => $ratingComponentId])
                    ->one();

                $prevUserVote[$ratingComponentId] = $modelUserVote->vote_value;
                $prevUserVoteTotal += $modelUserVote->vote_value;

                $modelUserVote->vote_value = $voteValue;

                if (!($flag = $modelUserVote->save())) {

                    $result['errorVote'] = $modelUserVote->getErrors();
                    break;
                }
            }
        }

        if ($flag) {

            $modelBusinessDetail = BusinessDetail::find()
                ->joinWith(['business'])
                ->andWhere(['business_id' => $post['business_id']])
                ->one();

            $modelBusinessDetail->total_vote_points -= $prevUserVoteTotal;

            foreach ($post['Post']['review']['rating'] as $votePoint) {

                $modelBusinessDetail->total_vote_points += $votePoint;
            }

            $modelBusinessDetail->voters = (!$isUpdate) ? $modelBusinessDetail->voters + 1 : $modelBusinessDetail->voters;
            $modelBusinessDetail->vote_points = $modelBusinessDetail->total_vote_points / count($post['Post']['review']['rating']);
            $modelBusinessDetail->vote_value = $modelBusinessDetail->vote_points / $modelBusinessDetail->voters;

            $flag = $modelBusinessDetail->save();
        }

        if ($flag) {

            foreach ($post['Post']['review']['rating'] as $ratingComponentId => $votePoint) {

                $modelBusinessDetailVote = BusinessDetailVote::find()
                    ->andWhere(['business_id' => $post['business_id']])
                    ->andWhere(['rating_component_id' => $ratingComponentId])
                    ->one();

                $modelBusinessDetailVote->total_vote_points -= $prevUserVote[$ratingComponentId];

                $modelBusinessDetailVote->total_vote_points += $votePoint;
                $modelBusinessDetailVote->vote_value = $modelBusinessDetailVote->total_vote_points / $modelBusinessDetail->voters;

                if (!($flag = $modelBusinessDetailVote->save())) {

                    break;
                }
            }
        }

        if ($flag) {

            $transaction->commit();

            $result['success'] = true;
            $result['icon'] = 'aicon aicon-icon-tick-in-circle';
            $result['title'] = 'Review Tersimpan.';
            $result['message'] = 'Review baru anda berhasil disimpan.';
            $result['type'] = 'success';
            $result['updated'] = $isUpdate;
            $result['user'] = \Yii::$app->user->getIdentity()->full_name;
            $result['userCreated'] = \Yii::$app->formatter->asRelativeTime($modelUserPostMain->created_at);
            $result['userPostMain'] = $modelUserPostMain->toArray();
            $result['userPostComments'] = $this->renderPartial('@frontend/views/data/post_comment', [
                'userPostId' => $modelUserPostMain->id,
                'modelUserPostComment' => $modelUserPostMain->userPostComments,
            ]);
            $result['commentCount'] = count($modelUserPostMain->userPostComments);
            $result['userPostMainPhoto'] = $dataUserPostMainPhoto;
            $result['shareUrl']['notAbsolute'] = \Yii::$app->urlManager->createUrl(['page/review', 'id' => $modelUserPostMain->id, 'uniqueName' => $modelBusinessDetail->business->unique_name]);
            $result['shareUrl']['absolute'] = \Yii::$app->urlManager->createAbsoluteUrl($result['shareUrl']['notAbsolute']);
            $result['deleteUrlReview'] = \Yii::$app->urlManager->createUrl(['user-action/delete-user-post', 'id' => $modelUserPostMain->id]);
            $result['deletedPhotoId'] = !empty($post['ImageReviewDelete']) ? $post['ImageReviewDelete'] : null;
        } else {

            $transaction->rollBack();

            $result['success'] = false;
        }

        return $result;
    }
}