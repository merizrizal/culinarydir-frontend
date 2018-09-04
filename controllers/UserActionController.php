<?php
namespace frontend\controllers;

use Yii;
use backend\models\UserPostMain;
use backend\models\UserPost;
use backend\models\UserVote;
use backend\models\BusinessDetail;
use backend\models\BusinessDetailVote;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * User Action Controller
 */
class UserActionController extends base\BaseController
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
                        'delete-photo' => ['POST'],
                        'delete-user-post' => ['POST'],
                    ],
                ],
            ]);
    }

    public function actionDeletePhoto($id)
    {
        $modelUserPostMain = UserPostMain::find()
                ->andWhere(['id' => $id])
                ->one();

        $result = [];

        if (!empty($modelUserPostMain)) {

            $modelUserPostMain->is_publish = false;

            if ($modelUserPostMain->save()) {

                $result['status'] = 'sukses';
                $result['icon'] = 'aicon aicon-icon-tick-in-circle';
                $result['title'] = 'Sukses.';
                $result['message'] = 'Foto berhasil di hapus.';
                $result['type'] = 'success';
                $result['id'] = $modelUserPostMain->id;
            } else {

                $result['status'] = 'gagal';
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal';
                $result['message'] = 'Foto gagal di hapus.';
                $result['type'] = 'danger';
            }
        } else {

            $result['status'] = 'gagal';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Gagal.';
            $result['message'] = 'Foto tidak ditemukan.';
            $result['type'] = 'danger';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionDeleteUserPost($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        
        $modelUserPostMain = UserPostMain::find()
                ->andWhere(['id' => $id])
                ->one();

        $modelUserPostMain->is_publish = false;
        
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
            
            $modelUserPostMainPhotos = UserPostMain::find()
                    ->andWhere(['parent_id' => $id])
                    ->all();
            
            foreach ($modelUserPostMainPhotos as $modelUserPostMainPhoto) {
                
                $modelUserPostMainPhoto->is_publish = false;
                
                if (!($flag = $modelUserPostMainPhoto->save())) {
                    break;
                }
                
                $modelUserPostPhoto = new UserPost();

                $modelUserPostPhoto->parent_id = $modelUserPost->id;
                $modelUserPostPhoto->business_id = $modelUserPostMainPhoto->business_id;
                $modelUserPostPhoto->type = $modelUserPostMainPhoto->type;
                $modelUserPostPhoto->user_id = $modelUserPostMainPhoto->user_id;
                $modelUserPostPhoto->is_publish = $modelUserPostMainPhoto->is_publish;
                $modelUserPostPhoto->image = $modelUserPostMainPhoto->image;

                if (!($flag = $modelUserPostPhoto->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {
            
            $prevUserVote = [];
            $prevTotalUserVote = null;
            
            $modelUserVotes = UserVote::find()
                    ->andWhere(['user_post_main_id' => $modelUserPostMain->id])
                    ->all();
            
            foreach ($modelUserVotes as $modelUserVote) {
                
                $prevUserVote[$modelUserVote->rating_component_id] = $modelUserVote->getOldAttribute('vote_value');
                $prevTotalUserVote += $modelUserVote->getOldAttribute('vote_value');
                    
                $modelUserVote->vote_value = $modelUserVote->vote_value - $modelUserVote->getOldAttribute('vote_value');
                
                if (!($flag = $modelUserVote->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {

            $modelBusinessDetail = BusinessDetail::find()
                    ->andWhere(['business_id' => $modelUserPostMain->business_id])
                    ->one();
            
            $modelBusinessDetail->voters = $modelBusinessDetail->voters - 1;
            $modelBusinessDetail->vote_points = $modelBusinessDetail->vote_points - $prevTotalUserVote;
            
            if (empty($modelBusinessDetail->voters) && empty($modelBusinessDetail->vote_points)) {
                
                $modelBusinessDetail->vote_value = 0;
            } else {
                
                $modelBusinessDetail->vote_value = ($modelBusinessDetail->vote_points / $modelBusinessDetail->voters) / count($prevUserVote);
            }
            
            $flag = $modelBusinessDetail->save();
        }
        
        if ($flag) {
            
            foreach ($prevUserVote as $ratingComponentId => $prevVoteValue) {
                
                $modelBusinessDetailVote = BusinessDetailVote::find()
                        ->andWhere(['business_id' => $modelUserPostMain->business_id])
                        ->andWhere(['rating_component_id' => $ratingComponentId])
                        ->one();
                
                $modelBusinessDetailVote->vote_value -= $prevVoteValue;
                
                if (!($flag = $modelBusinessDetailVote->save())) {
                    break;
                }
            }
        }
        
        $result = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $result['status'] = 'sukses';
            $result['userPostId'] = $modelUserPostMain->id;
            $result['publish'] = $modelUserPostMain->is_publish;
            $result['undoUrlReview'] = Yii::$app->urlManager->createUrl(['user-action/undo-user-post', 'id' => $modelUserPostMain->id]);
        } else {
            
            $transaction->rollBack();
            
            $result['status'] = 'gagal';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Gagal';
            $result['message'] = 'Review gagal dihapus';
            $result['type'] = 'danger';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    public function actionUndoUserPost($id)
    {        
        $post = Yii::$app->request->post();
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        
        $modelUserPostMain = UserPostMain::find()
                ->andWhere(['id' => $id])
                ->one();
        
        $modelUserPostMain->is_publish = true;
        
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
            
            $modelUserPostMainPhotos = UserPostMain::find()
                    ->andWhere(['parent_id' => $id])
                    ->all();
            
            foreach ($modelUserPostMainPhotos as $modelUserPostMainPhoto) {
                
                $modelUserPostMainPhoto->is_publish = true;
                
                if (!($flag = $modelUserPostMainPhoto->save())) {
                    break;
                }
                
                $modelUserPostPhoto = new UserPost();

                $modelUserPostPhoto->parent_id = $modelUserPost->id;
                $modelUserPostPhoto->business_id = $modelUserPostMainPhoto->business_id;
                $modelUserPostPhoto->type = $modelUserPostMainPhoto->type;
                $modelUserPostPhoto->user_id = $modelUserPostMainPhoto->user_id;
                $modelUserPostPhoto->is_publish = $modelUserPostMainPhoto->is_publish;
                $modelUserPostPhoto->image = $modelUserPostMainPhoto->image;

                if (!($flag = $modelUserPostPhoto->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {
            
            $prevUserVote = [];
            $prevTotalUserVote = null;
            
            foreach ($post['Post']['review']['rating'] as $ratingComponentId => $voteValue) {
                
                $modelUserVote = UserVote::find()
                        ->andWhere(['user_post_main_id' => $modelUserPostMain->id])
                        ->andWhere(['rating_component_id' => $ratingComponentId])
                        ->one();
                
                $prevUserVote[$ratingComponentId] = $voteValue;
                $prevTotalUserVote += $voteValue;

                $modelUserVote->vote_value = $voteValue;

                if (!($flag = $modelUserVote->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {

            $modelBusinessDetail = BusinessDetail::find()
                    ->andWhere(['business_id' => $modelUserPostMain->business_id])
                    ->one();
            
            $modelBusinessDetail->voters = $modelBusinessDetail->voters + 1;
            $modelBusinessDetail->vote_points = $modelBusinessDetail->vote_points + $prevTotalUserVote;
            $modelBusinessDetail->vote_value = ($modelBusinessDetail->vote_points / $modelBusinessDetail->voters) / count($prevUserVote);

            $flag = $modelBusinessDetail->save();
        }
        
        if ($flag) {

            foreach ($prevUserVote as $ratingComponentId => $prevVoteValue) {
                
                $modelBusinessDetailVote = BusinessDetailVote::find()
                        ->andWhere(['business_id' => $modelUserPostMain->business_id])
                        ->andWhere(['rating_component_id' => $ratingComponentId])
                        ->one();
                
                $modelBusinessDetailVote->vote_value += $prevVoteValue;
                
                if (!($flag = $modelBusinessDetailVote->save())) {
                    break;
                }
            }
        }
        
        $result = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $result['status'] = 'sukses';
            $result['userPostId'] = $modelUserPostMain->id;
            $result['publish'] = $modelUserPostMain->is_publish;
            $result['deleteUrlReview'] = Yii::$app->urlManager->createUrl(['user-action/delete-user-post', 'id' => $modelUserPostMain->id]);
        } else {
            
            $transaction->rollBack();
            
            $result['status'] = 'gagal';
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Gagal';
            $result['message'] = 'Review gagal dihapus';
            $result['type'] = 'danger';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}