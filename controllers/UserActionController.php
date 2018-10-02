<?php
namespace frontend\controllers;

use Yii;
use core\models\UserPostMain;
use core\models\UserVote;
use core\models\BusinessDetail;
use core\models\BusinessDetailVote;
use yii\filters\VerbFilter;
use yii\web\Response;
use core\models\UserPostLove;

/**
 * User Action Controller
 */
class UserActionController extends base\BaseController
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

                $result['success'] = true;
                $result['icon'] = 'aicon aicon-icon-tick-in-circle';
                $result['title'] = 'Sukses.';
                $result['message'] = 'Foto berhasil di hapus.';
                $result['type'] = 'success';
                $result['id'] = $modelUserPostMain->id;
            } else {

                $result['success'] = false;
                $result['icon'] = 'aicon aicon-icon-info';
                $result['title'] = 'Gagal';
                $result['message'] = 'Foto gagal di hapus.';
                $result['type'] = 'danger';
            }
        } else {

            $result['success'] = false;
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
        
        $prevData = [];
        
        $modelUserPostMain = UserPostMain::find()
            ->andWhere(['id' => $id])
            ->one();
        
        $prevData['prevLoveValue'] = $modelUserPostMain->love_value;

        $modelUserPostMain->is_publish = false;
        $modelUserPostMain->love_value = 0;
        
        $flag = $modelUserPostMain->save();
        
        if ($flag) {
            
            $modelUserPostMainPhotos = UserPostMain::find()
                ->andWhere(['parent_id' => $id])
                ->all();
            
            foreach ($modelUserPostMainPhotos as $modelUserPostMainPhoto) {
                
                $modelUserPostMainPhoto->is_publish = false;

                if (!($flag = $modelUserPostMainPhoto->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {
            
            $modelUserPostLoves = UserPostLove::find()
                ->andWhere(['user_post_main_id' => $id])
                ->all();
            
            foreach ($modelUserPostLoves as $modelUserPostLove) {
                
                $modelUserPostLove->is_active = false;
                
                if (!($flag = $modelUserPostLove->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {
            
            $prevData['prevUserVote'] = [];
            $prevUserVoteTotal = 0;
            
            $modelUserVotes = UserVote::find()
                ->andWhere(['user_post_main_id' => $id])
                ->all();
            
            foreach ($modelUserVotes as $modelUserVote) {
                
                $prevData['prevUserVote'][$modelUserVote->rating_component_id] = $modelUserVote->vote_value;
                $prevUserVoteTotal += $modelUserVote->vote_value;
                
                $modelUserVote->vote_value = 0;
                
                if (!($flag = $modelUserVote->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {

            $modelBusinessDetail = BusinessDetail::find()
                ->andWhere(['business_id' => $modelUserPostMain->business_id])
                ->one();

            $modelBusinessDetail->total_vote_points -= $prevUserVoteTotal;
            $modelBusinessDetail->voters -= 1;
            $modelBusinessDetail->vote_points = $modelBusinessDetail->total_vote_points / count($prevData['prevUserVote']);
            $modelBusinessDetail->vote_value = !empty($modelBusinessDetail->voters) ? $modelBusinessDetail->vote_points / $modelBusinessDetail->voters : 0;
            
            $flag = $modelBusinessDetail->save();
        }
        
        if ($flag) {
            
            foreach ($prevData['prevUserVote'] as $ratingComponentId => $votePoint) {
                
                $modelBusinessDetailVote = BusinessDetailVote::find()
                    ->andWhere(['business_id' => $modelUserPostMain->business_id])
                    ->andWhere(['rating_component_id' => $ratingComponentId])
                    ->one();
                
                $modelBusinessDetailVote->total_vote_points -= $votePoint;
                $modelBusinessDetailVote->vote_value = !empty($modelBusinessDetail->voters) ? $modelBusinessDetailVote->total_vote_points / $modelBusinessDetail->voters : 0;
                
                if (!($flag = $modelBusinessDetailVote->save())) {
                    break;
                }
            }
        }
        
        Yii::$app->session->setFlash('prevData' . $id, $prevData);
        
        $result = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $result['success'] = true;
            $result['publish'] = $modelUserPostMain->is_publish;
            $result['undoUrlReview'] = Yii::$app->urlManager->createUrl(['user-action/undo-user-post', 'id' => $modelUserPostMain->id]);
        } else {
            
            $transaction->rollBack();
            
            $result['success'] = false;
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
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        
        $prevData = Yii::$app->session->getFlash('prevData' . $id);
        
        $modelUserPostMain = UserPostMain::find()
            ->andWhere(['id' => $id])
            ->one();
        
        $modelUserPostMain->is_publish = true;
        $modelUserPostMain->love_value = !empty($prevData['prevLoveValue']) ? $prevData['prevLoveValue'] : 0;
        
        $flag = $modelUserPostMain->save();
        
        if ($flag) {
            
            $modelUserPostMainPhotos = UserPostMain::find()
                ->andWhere(['parent_id' => $id])
                ->all();
            
            foreach ($modelUserPostMainPhotos as $modelUserPostMainPhoto) {
                
                $modelUserPostMainPhoto->is_publish = true;
                
                if (!($flag = $modelUserPostMainPhoto->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {
            
            $modelUserPostLoves = UserPostLove::find()
                ->andWhere(['user_post_main_id' => $id])
                ->all();
            
            foreach ($modelUserPostLoves as $modelUserPostLove) {
                
                $modelUserPostLove->is_active = true;
                
                if (!($flag = $modelUserPostLove->save())) {
                    break;
                }
            }
        }
        
        if ($flag) {
            
            $flag = false;
            $prevUserVoteTotal = 0;
            
            if (!empty($prevData['prevUserVote'])) {
                
                foreach ($prevData['prevUserVote'] as $ratingComponentId => $voteValue) {
                    
                    $modelUserVote = UserVote::find()
                        ->andWhere(['user_post_main_id' => $id])
                        ->andWhere(['rating_component_id' => $ratingComponentId])
                        ->one();
                    
                    $prevUserVoteTotal += $voteValue;
                    
                    $modelUserVote->vote_value = $voteValue;
                    
                    if (!($flag = $modelUserVote->save())) {
                        break;
                    }
                }
            }
        }
        
        if ($flag) {

            $modelBusinessDetail = BusinessDetail::find()
                ->andWhere(['business_id' => $modelUserPostMain->business_id])
                ->one();
            
            $modelBusinessDetail->total_vote_points += $prevUserVoteTotal;
            $modelBusinessDetail->voters += 1;
            $modelBusinessDetail->vote_points = $modelBusinessDetail->total_vote_points / count($prevData['prevUserVote']);
            $modelBusinessDetail->vote_value = $modelBusinessDetail->vote_points / $modelBusinessDetail->voters;

            $flag = $modelBusinessDetail->save();
        }
        
        if ($flag) {

            foreach ($prevData['prevUserVote'] as $ratingComponentId => $votePoint) {
                
                $modelBusinessDetailVote = BusinessDetailVote::find()
                    ->andWhere(['business_id' => $modelUserPostMain->business_id])
                    ->andWhere(['rating_component_id' => $ratingComponentId])
                    ->one();
                
                $modelBusinessDetailVote->total_vote_points += $votePoint;
                $modelBusinessDetailVote->vote_value = $modelBusinessDetailVote->total_vote_points / $modelBusinessDetail->voters;
                
                if (!($flag = $modelBusinessDetailVote->save())) {
                    break;
                }
            }
        }
        
        $result = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $result['success'] = true;
            $result['publish'] = $modelUserPostMain->is_publish;
            $result['deleteUrlReview'] = Yii::$app->urlManager->createUrl(['user-action/delete-user-post', 'id' => $modelUserPostMain->id]);
        } else {
            
            $transaction->rollBack();
            
            $result['success'] = false;
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Gagal';
            $result['message'] = 'Review gagal dikembalikan';
            $result['type'] = 'danger';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}