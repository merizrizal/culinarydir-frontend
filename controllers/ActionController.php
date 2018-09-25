<?php
namespace frontend\controllers;

use Yii;
use core\models\UserPostMain;
use core\models\UserPost;
use core\models\UserVote;
use core\models\UserLove;
use core\models\UserVisit;
use core\models\UserPostComment;
use core\models\UserPostLove;
use core\models\UserReport;
use core\models\BusinessDetail;
use core\models\BusinessDetailVote;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Action Controller
 */
class ActionController extends base\BaseController
{
    /**
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
                    ],
                ],
            ]);
    }

    public function actionSubmitReview()
    {
        if (!empty(($post = Yii::$app->request->post()))) {

            $modelUserPostMain = UserPostMain::find()
                    ->andWhere(['user_post_main.unique_id' => $post['business_id'] . '-' . Yii::$app->user->getIdentity()->id])
                    ->one();

            if (!empty($modelUserPostMain)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return $this->updateReview($post, $modelUserPostMain);
            } else {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return $this->createReview($post);
            }
        }
    }

    public function actionSubmitComment()
    {
        if (!empty(($post = Yii::$app->request->post()))) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            $modelUserPostComment = new UserPostComment();

            $modelUserPostComment->user_post_main_id = $post['user_post_main_id'];
            $modelUserPostComment->user_id = Yii::$app->user->getIdentity()->id;
            $modelUserPostComment->text = $post['text'];

            $flag = $modelUserPostComment->save();
            
            $result = [];

            if ($flag) {

                $transaction->commit();

                $result['status'] = 'sukses';
                $result['user_post_main_id'] = $modelUserPostComment->user_post_main_id;
            } else {

                $transaction->rollBack();

                $result['status'] = 'gagal';
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Comment.';
                $result['message'] = 'Komentar Anda gagal di simpan.';
                $result['type'] = 'danger';
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitLikes()
    {
        if (!empty(($post = Yii::$app->request->post()))) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            $modelUserPostLove = UserPostLove::find()
                    ->joinWith(['userPostMain'])
                    ->andWhere([
                        'user_post_love.user_post_main_id' => $post['user_post_main_id'],
                        'user_post_love.user_id' => Yii::$app->user->getIdentity()->id,
                    ])
                    ->one();

            if (!empty($modelUserPostLove)) {

                $modelUserPostLove->is_active = !$modelUserPostLove->is_active;

                $flag = $modelUserPostLove->save();
            } else {

                $modelUserPostLove = new UserPostLove();

                $modelUserPostLove->user_post_main_id = $post['user_post_main_id'];
                $modelUserPostLove->user_id = Yii::$app->user->getIdentity()->id;
                $modelUserPostLove->is_active = true;

                $flag = $modelUserPostLove->save();
            }

            if ($flag) {

                $modelUserPostMain = UserPostMain::find()
                        ->andWhere([
                            'user_post_main.id' => $post['user_post_main_id'],
                        ])
                        ->one();

                if ($modelUserPostLove->is_active) {

                    $modelUserPostMain->love_value = $modelUserPostMain->love_value + 1;

                } else {

                    $modelUserPostMain->love_value = $modelUserPostMain->love_value - 1;
                }

                $flag = $modelUserPostMain->save();
            }

            $result = [];

            if ($flag) {

                $transaction->commit();

                $result['status'] = 'sukses';
                $result['is_active'] = $modelUserPostLove->is_active;
            } else {

                $transaction->rollBack();

                $result['status'] = 'gagal';
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal';
                $result['message'] = 'Gagal menyukai komentar.';
                $result['type'] = 'danger';
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitPhoto()
    {
        if (!empty(($post = Yii::$app->request->post()))) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            $modelUserPostMainPhoto = new UserPostMain();

            $image = Tools::uploadFileWithoutModel('/img/user_post/', 'Post[image]', $modelUserPostMainPhoto->id, '', true);

            $modelUserPostMainPhoto->unique_id = $post['business_id'] . '-' . Yii::$app->user->getIdentity()->id . '-' . time();
            $modelUserPostMainPhoto->business_id = $post['business_id'];
            $modelUserPostMainPhoto->user_id = Yii::$app->user->getIdentity()->id;
            $modelUserPostMainPhoto->type = 'Photo';
            $modelUserPostMainPhoto->text = $post['Post']['text'];
            $modelUserPostMainPhoto->image = $image;
            $modelUserPostMainPhoto->is_publish = true;
            $modelUserPostMainPhoto->love_value = 0;

            $flag = $modelUserPostMainPhoto->save();

            $dataSocialShare = [];

            if (!empty($post['social_media_share'])) {

                foreach ($post['social_media_share'] as $socialShare) {

                    $dataSocialShare[$socialShare] = true;
                }
            }

            $result = [];

            if ($flag) {

                $transaction->commit();

                $result['userPostMainPhoto'] = $modelUserPostMainPhoto->toArray();
                $result['socialShare'] = $dataSocialShare;
                $result['status'] = 'sukses';
                $result['icon'] = 'aicon aicon-icon-tick-in-circle';
                $result['title'] = 'Sukses.';
                $result['message'] = 'Foto anda berhasil di simpan.';
                $result['type'] = 'success';
            } else {

                $transaction->rollBack();

                $result['status'] = 'gagal';
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal.';
                $result['message'] = '
                    Foto Anda gagal di simpan.<br>
                    <ol>
                        <li>Pastikan panjang caption Anda lebih dari 20 karakter.</li>
                        <li>Pastikan Anda memilih foto dengan ukuran max. 2Mb.</li>
                    </ol>
                ';
                $result['type'] = 'danger';
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitUserLove()
    {
        if (!empty(($post = Yii::$app->request->post()))) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            $userId = Yii::$app->user->getIdentity()->id;

            $modelUserLove = UserLove::find()
                ->where([
                    'business_id' => $post['business_id'],
                    'user_id' => $userId])
                ->one();

            if(!empty($modelUserLove)) {

                $modelUserLove->is_active = !$modelUserLove->is_active;

                $flag = $modelUserLove->save();

            } else {

                $modelUserLove = new UserLove();
                $modelUserLove->business_id = $post['business_id'];
                $modelUserLove->user_id = $userId;
                $modelUserLove->is_active = true;

                $flag = $modelUserLove->save();
            }

            if($flag) {

                $modelBusinessDetail = BusinessDetail::find()
                        ->where(['business_id' => $post['business_id']])
                        ->one();

                if (!empty($modelBusinessDetail)) {

                    if($modelUserLove->is_active == true) {

                        $modelBusinessDetail->love_value = $modelBusinessDetail->love_value + 1;

                    } else {
                        $modelBusinessDetail->love_value = $modelBusinessDetail->love_value - 1;
                    }

                    $modelBusinessDetail->business_id = $post['business_id'];

                    $flag = $modelBusinessDetail->save();

                } else {
                    $modelBusinessDetail = new BusinessDetail();

                    $modelBusinessDetail->business_id = $post['business_id'];
                    $modelBusinessDetail->love_value = $modelBusinessDetail->love_value + 1;

                    $flag = $modelBusinessDetail->save();
                }
            }

            $result = [];

            if($flag) {

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

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitUserVisit()
    {
        if (!empty(($post = Yii::$app->request->post()))) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            $userId = Yii::$app->user->getIdentity()->id;

            $modelUserVisit = UserVisit::find()
                ->where([
                    'business_id' => $post['business_id'],
                    'user_id' => $userId])
                ->one();

            if(!empty($modelUserVisit)) {

                $modelUserVisit->is_active = !$modelUserVisit->is_active;

                $flag = $modelUserVisit->save();

            } else {

                $modelUserVisit = new UserVisit();
                $modelUserVisit->business_id = $post['business_id'];
                $modelUserVisit->user_id = $userId;
                $modelUserVisit->is_active = true;

                $flag = $modelUserVisit->save();
            }

            if($flag) {

                $modelBusinessDetail = BusinessDetail::find()
                        ->where(['business_id' => $post['business_id']])
                        ->one();

                if (!empty($modelBusinessDetail)) {

                    if($modelUserVisit->is_active == true) {

                        $modelBusinessDetail->visit_value = $modelBusinessDetail->visit_value + 1;

                    } else {
                        $modelBusinessDetail->visit_value = $modelBusinessDetail->visit_value - 1;
                    }

                    $modelBusinessDetail->business_id = $post['business_id'];

                    $flag = $modelBusinessDetail->save();

                } else {
                    $modelBusinessDetail = new BusinessDetail();

                    $modelBusinessDetail->business_id = $post['business_id'];
                    $modelBusinessDetail->visit_value = $modelBusinessDetail->visit_value + 1;

                    $flag = $modelBusinessDetail->save();
                }
            }

            $result = [];

            if($flag) {

                $transaction->commit();

                $result['status'] = 'sukses';
                $result['is_active'] = $modelUserVisit->is_active;
            } else {

                $transaction->rollBack();

                $result['status'] = 'gagal';
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal';
                $result['message'] = 'Gagal menandai tempat ini.';
                $result['type'] = 'danger';
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubmitReport()
    {
        if (!empty(($post = Yii::$app->request->post()))) {

            $modelUserReport = new UserReport();

            $modelUserReport->business_id = $post['business_id'];
            $modelUserReport->user_id = Yii::$app->user->getIdentity()->id;
            $modelUserReport->report_status = $post['UserReport']['report_status'];
            $modelUserReport->text = $post['UserReport']['text'];

            $result = [];

            if ($modelUserReport->save()) {

                $result['status'] = 'sukses';
                $result['icon'] = 'aicon aicon-icon-tick-in-circle';
                $result['title'] = 'Report Tersimpan.';
                $result['message'] = 'Report Anda berhasil di simpan.';
                $result['type'] = 'success';
            } else {

                $result['status'] = 'gagal';
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Report Gagal.';
                $result['message'] = 'Report Anda gagal di simpan.';
                $result['type'] = 'danger';
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    private function createReview($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;

        $modelUserPostMain = new UserPostMain();

        $modelUserPostMain->unique_id = $post['business_id'] . '-' . Yii::$app->user->getIdentity()->id;
        $modelUserPostMain->business_id = $post['business_id'];
        $modelUserPostMain->user_id = Yii::$app->user->getIdentity()->id;
        $modelUserPostMain->type = 'Review';
        $modelUserPostMain->text = $post['Post']['review']['text'];
        $modelUserPostMain->is_publish = true;
        $modelUserPostMain->love_value = 0;

        $flag = $modelUserPostMain->save();

        if ($flag) {

            $modelUserPost = new UserPost();

            $modelUserPost->business_id = $post['business_id'];
            $modelUserPost->type = 'Review';
            $modelUserPost->user_id = Yii::$app->user->getIdentity()->id;
            $modelUserPost->text = $post['Post']['review']['text'];
            $modelUserPost->is_publish = true;
            $modelUserPost->love_value = 0;
            $modelUserPost->user_post_main_id = $modelUserPostMain->id;

            $flag = $modelUserPost->save();
        }

        if ($flag) {

            $images = Tools::uploadFilesWithoutModel('/img/user_post/', 'Post[photo][image]', $modelUserPostMain->id, '', true);

            $dataUserPostMainPhoto = [];

            foreach ($images as $photoIndex => $image) {

                $modelUserPostMainPhoto = new UserPostMain();

                $modelUserPostMainPhoto->parent_id = $modelUserPostMain->id;
                $modelUserPostMainPhoto->unique_id = Yii::$app->security->generateRandomString() . $photoIndex;
                $modelUserPostMainPhoto->business_id = $post['business_id'];
                $modelUserPostMainPhoto->user_id = Yii::$app->user->getIdentity()->id;
                $modelUserPostMainPhoto->type = 'Photo';
                $modelUserPostMainPhoto->image = $image;
                $modelUserPostMainPhoto->is_publish = true;
                $modelUserPostMainPhoto->love_value = 0;

                if (!($flag = $modelUserPostMainPhoto->save())) {
                    break;
                } else {

                    $modelUserPostMainPhoto->image = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $modelUserPostMainPhoto->image, 200, 200);

                    array_push($dataUserPostMainPhoto, $modelUserPostMainPhoto->toArray());
                }

                $modelUserPostPhoto = new UserPost();

                $modelUserPostPhoto->parent_id = $modelUserPost->id;
                $modelUserPostPhoto->business_id = $post['business_id'];
                $modelUserPostPhoto->type = 'Photo';
                $modelUserPostPhoto->user_id = Yii::$app->user->getIdentity()->id;
                $modelUserPostPhoto->image = $image;
                $modelUserPostPhoto->is_publish = true;
                $modelUserPostPhoto->love_value = 0;

                if (!($flag = $modelUserPostPhoto->save())) {
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
                    break;
                }
            }
        }

        if ($flag) {

            $modelBusinessDetail = BusinessDetail::find()
                    ->andWhere(['business_id' => $post['business_id']])
                    ->one();

            if (empty($modelBusinessDetail)) {

                $modelBusinessDetail = new BusinessDetail();

                $modelBusinessDetail->business_id = $post['business_id'];
            }

            foreach ($post['Post']['review']['rating'] as $voteValue) {

                $modelBusinessDetail->vote_points += $voteValue;
            }

            $modelBusinessDetail->voters = $modelBusinessDetail->voters + 1;
            $modelBusinessDetail->vote_value = ($modelBusinessDetail->vote_points / $modelBusinessDetail->voters) / count($post['Post']['review']['rating']);

            $flag = $modelBusinessDetail->save();
        }

        if ($flag) {

            foreach ($post['Post']['review']['rating'] as $ratingComponentId => $voteValue) {

                $modelBusinessDetailVote = BusinessDetailVote::find()
                        ->andWhere(['business_id' => $post['business_id']])
                        ->andWhere(['rating_component_id' => $ratingComponentId])
                        ->one();

                if (!empty($modelBusinessDetailVote)) {

                    $modelBusinessDetailVote->vote_value += $voteValue;

                    if (!($flag = $modelBusinessDetailVote->save())) {
                        break;
                    }
                } else {

                    $modelBusinessDetailVote = new BusinessDetailVote();

                    $modelBusinessDetailVote->business_id = $post['business_id'];
                    $modelBusinessDetailVote->rating_component_id = $ratingComponentId;
                    $modelBusinessDetailVote->vote_value = $voteValue;

                    if (!($flag = $modelBusinessDetailVote->save())) {
                        break;
                    }
                }
            }
        }

        if ($flag) {

            $dataSocialShare = [];

            if (!empty($post['social_media_share'])) {

                foreach ($post['social_media_share'] as $socialShare) {

                    $dataSocialShare[$socialShare] = true;
                }
            }
        }

        $result = [];

        if ($flag) {

            $transaction->commit();

            $result['status'] = 'sukses';
            $result['icon'] = 'aicon aicon-icon-tick-in-circle';
            $result['title'] = 'Review Tersimpan.';
            $result['message'] = 'Review anda berhasil di simpan.';
            $result['type'] = 'success';
            $result['updated'] = 0;
            $result['user'] = Yii::$app->user->getIdentity()->full_name;
            $result['userCreated'] = Yii::$app->formatter->asRelativeTime($modelUserPostMain->created_at);
            $result['userPostMain'] = $modelUserPostMain->toArray();
            $result['userPostMainPhoto'] = $dataUserPostMainPhoto;
            $result['socialShare'] = $dataSocialShare;
            $result['deleteUrlPhoto'] = Yii::$app->urlManager->createUrl(['user-action/delete-photo']);
            $result['deleteUrlReview'] = Yii::$app->urlManager->createUrl(['user-action/delete-user-post', 'id' => $modelUserPostMain->id]);
        } else {

            $transaction->rollBack();

            $result['status'] = 'gagal';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Review Gagal.';
            $result['message'] = '
                Review Anda gagal di simpan.<br>
                <ol>
                    <li>Pastikan Anda mengisi rating dan review.</li>
                    <li>Pastikan Anda memilih foto dengan ukuran max. 2Mb.</li>
                    <li>Pastikan Anda tidak mengupload lebih dari 10 foto.</li>
                    <li>Pastikan panjang review anda lebih dari 20 karakter.</li>
                </ol>
            ';
            $result['type'] = 'danger';
        }

        return $result;
    }

    private function updateReview($post, $modelUserPostMain = [])
    {
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;

        $isUpdate = $modelUserPostMain->getOldAttribute('is_publish');

        $modelUserPostMain->text = $post['Post']['review']['text'];
        $modelUserPostMain->is_publish = true;

        $flag = $modelUserPostMain->save();

        if ($flag) {

            $modelUserPost = new UserPost();

            $modelUserPost->business_id = $post['business_id'];
            $modelUserPost->type = 'Review';
            $modelUserPost->user_id = Yii::$app->user->getIdentity()->id;
            $modelUserPost->text = $post['Post']['review']['text'];
            $modelUserPost->is_publish = true;
            $modelUserPost->love_value = 0;
            $modelUserPost->user_post_main_id = $modelUserPostMain->id;

            $flag = $modelUserPost->save();
        }

        if ($flag) {

            $images = Tools::uploadFilesWithoutModel('/img/user_post/', 'Post[photo][image]', $modelUserPostMain->id, '', true);

            $dataUserPostMainPhoto = [];

            foreach ($images as $photoIndex => $image) {

                $modelUserPostMainPhoto = new UserPostMain();

                $modelUserPostMainPhoto->parent_id = $modelUserPostMain->id;
                $modelUserPostMainPhoto->unique_id = Yii::$app->security->generateRandomString() . $photoIndex;
                $modelUserPostMainPhoto->business_id = $post['business_id'];
                $modelUserPostMainPhoto->user_id = Yii::$app->user->getIdentity()->id;
                $modelUserPostMainPhoto->type = 'Photo';
                $modelUserPostMainPhoto->image = $image;
                $modelUserPostMainPhoto->is_publish = true;
                $modelUserPostMainPhoto->love_value = 0;

                if (!($flag = $modelUserPostMainPhoto->save())) {
                    break;
                } else {

                    $modelUserPostMainPhoto->image = Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/user_post/', $modelUserPostMainPhoto->image, 200, 200);

                    array_push($dataUserPostMainPhoto, $modelUserPostMainPhoto->toArray());
                }

                $modelUserPostPhoto = new UserPost();

                $modelUserPostPhoto->parent_id = $modelUserPost->id;
                $modelUserPostPhoto->business_id = $post['business_id'];
                $modelUserPostPhoto->type = 'Photo';
                $modelUserPostPhoto->user_id = Yii::$app->user->getIdentity()->id;
                $modelUserPostPhoto->image = $image;
                $modelUserPostPhoto->is_publish = true;
                $modelUserPostPhoto->love_value = 0;

                if (!($flag = $modelUserPostPhoto->save())) {
                    break;
                }
            }
        }

        if ($flag) {

            $oldUserVote = [];
            $oldTotalUserVote = null;

            foreach ($post['Post']['review']['rating'] as $ratingComponentId => $voteValue) {

                $modelUserVote = UserVote::find()
                        ->andWhere(['user_vote.user_post_main_id' => $modelUserPostMain->id])
                        ->andWhere(['user_vote.rating_component_id' => $ratingComponentId])
                        ->one();

                $oldUserVote[$ratingComponentId] = $modelUserVote->vote_value;
                $oldTotalUserVote += $modelUserVote->vote_value;

                $modelUserVote->vote_value = $voteValue;

                if (!($flag = $modelUserVote->save())) {
                    break;
                }
            }
        }

        if ($flag) {

            $modelBusinessDetail = BusinessDetail::find()
                    ->andWhere(['business_id' => $post['business_id']])
                    ->one();

            $modelBusinessDetail->vote_points = $modelBusinessDetail->vote_points - $oldTotalUserVote;

            foreach ($post['Post']['review']['rating'] as $voteValue) {

                $modelBusinessDetail->vote_points += $voteValue;
            }

            if (empty($modelBusinessDetail->voters)) {

                $modelBusinessDetail->voters = $modelBusinessDetail->voters + 1;
            }

            $modelBusinessDetail->vote_value = ($modelBusinessDetail->vote_points / $modelBusinessDetail->voters) / count($post['Post']['review']['rating']);

            $flag = $modelBusinessDetail->save();
        }

        if ($flag) {

            foreach ($post['Post']['review']['rating'] as $ratingComponentId => $voteValue) {

                $modelBusinessDetailVote = BusinessDetailVote::find()
                        ->andWhere(['business_id' => $post['business_id']])
                        ->andWhere(['rating_component_id' => $ratingComponentId])
                        ->one();

                $modelBusinessDetailVote->vote_value = $modelBusinessDetailVote->vote_value - $oldUserVote[$ratingComponentId];

                if (!empty($modelBusinessDetailVote)) {

                    $modelBusinessDetailVote->vote_value += $voteValue;

                    if (!($flag = $modelBusinessDetailVote->save())) {
                        break;
                    }
                }
            }
        }

        if ($flag) {

            $dataSocialShare = [];

            if (!empty($post['social_media_share'])) {

                foreach ($post['social_media_share'] as $socialShare) {

                    $dataSocialShare[$socialShare] = true;
                }
            }
        }

        $result = [];

        if ($flag) {

            $transaction->commit();

            $result['status'] = 'sukses';
            $result['icon'] = 'aicon aicon-icon-tick-in-circle';
            $result['title'] = 'Review Tersimpan.';
            $result['message'] = 'Review baru anda berhasil di simpan.';
            $result['type'] = 'success';
            $result['updated'] = $isUpdate;
            $result['user'] = Yii::$app->user->getIdentity()->full_name;
            $result['userCreated'] = Yii::$app->formatter->asRelativeTime($modelUserPostMain->created_at);
            $result['userPostMain'] = $modelUserPostMain->toArray();
            $result['userPostMainPhoto'] = $dataUserPostMainPhoto;
            $result['socialShare'] = $dataSocialShare;
            $result['deleteUrlPhoto'] = Yii::$app->urlManager->createUrl(['user-action/delete-photo']);
            $result['deleteUrlReview'] = Yii::$app->urlManager->createUrl(['user-action/delete-user-post', 'id' => $modelUserPostMain->id]);
        } else {

            $transaction->rollBack();

            $result['status'] = 'gagal';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Review Gagal.';
            $result['message'] = '
                Review baru Anda gagal di simpan.<br>
                <ol>
                    <li>Pastikan Anda mengisi rating dan review.</li>
                    <li>Pastikan Anda memilih foto dengan ukuran max. 2Mb.</li>
                    <li>Pastikan Anda tidak mengupload lebih dari 10 foto.</li>
                    <li>Pastikan panjang review anda lebih dari 20 karakter.</li>
                </ol>
            ';
            $result['type'] = 'danger';
        }

        return $result;
    }
}
