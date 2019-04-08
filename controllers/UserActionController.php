<?php
namespace frontend\controllers;

use Yii;
use core\models\UserPostMain;
use core\models\UserVote;
use core\models\BusinessDetail;
use core\models\BusinessDetailVote;
use core\models\UserPostLove;
use core\models\TransactionSession;
use core\models\TransactionItem;
use yii\filters\VerbFilter;
use yii\web\Response;

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
                        'reorder' => ['POST']
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
        
        $modelUserPostMain = UserPostMain::find()
            ->andWhere(['id' => $id])
            ->one();

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
        
        $userVoteTotal = 0;
        $userVote = [];
        
        if ($flag) {                      
            
            $modelUserVotes = UserVote::find()
                ->andWhere(['user_post_main_id' => $id])
                ->all();
            
            foreach ($modelUserVotes as $modelUserVote) {
                
                $userVoteTotal += $modelUserVote->vote_value;
                $userVote[$modelUserVote->rating_component_id] = $modelUserVote->vote_value;
                
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

            $modelBusinessDetail->total_vote_points -= $userVoteTotal;
            $modelBusinessDetail->voters -= 1;
            $modelBusinessDetail->vote_points = $modelBusinessDetail->total_vote_points / count($modelUserVotes);
            $modelBusinessDetail->vote_value = !empty($modelBusinessDetail->voters) ? $modelBusinessDetail->vote_points / $modelBusinessDetail->voters : 0;
            
            $flag = $modelBusinessDetail->save();
        }
        
        if ($flag) {
            
            foreach ($userVote as $ratingComponentId => $votePoint) {
                
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
        
        $result = [];
        
        if ($flag) {
            
            $transaction->commit();
            
            $result['success'] = true;
            $result['publish'] = $modelUserPostMain->is_publish;
            $result['icon'] = 'aicon aicon-icon-tick-in-circle';
            $result['title'] = 'Sukses.';
            $result['message'] = 'Review berhasil dihapus.';
            $result['type'] = 'success';
        } else {
            
            $transaction->rollBack();
            
            $result['success'] = false;
            $result['icon'] = 'aicon aicon-icon-info';
            $result['title'] = 'Gagal';
            $result['message'] = 'Review gagal dihapus.';
            $result['type'] = 'danger';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    public function actionReorder()
    {
        $return = [];
        
        $modelTransactionSession = TransactionSession::find()
            ->andWhere(['user_ordered' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['is_closed' => false])
            ->asArray()->one();
        
        if (!empty($modelTransactionSession)) {
            
            if ($modelTransactionSession['id'] == Yii::$app->request->post('id')) {
                
                return $this->redirect(['order/checkout']);
            }
            
            $return['success'] = false;
            $return['type'] = 'danger';
            $return['icon'] = 'aicon aicon-icon-info';
            $return['title'] = 'Pesan Ulang Gagal';
            $return['text'] = 'Silahkan selesaikan pesanan anda terlebih dahulu.';
        } else {
            
            $transaction = Yii::$app->db->beginTransaction();
            
            $flag = false;
        
            $oldModelTransaction = TransactionSession::find()
                ->joinWith(['transactionItems'])
                ->andWhere(['transaction_session.id' => Yii::$app->request->post('id')])
                ->one();
            
            $newModelTransactionSession = new TransactionSession();
            $newModelTransactionSession->user_ordered = $oldModelTransaction->user_ordered;
            $newModelTransactionSession->business_id = $oldModelTransaction->business_id;
            $newModelTransactionSession->note = !empty($oldModelTransaction->note) ? $oldModelTransaction->note : null;
            $newModelTransactionSession->total_price = $oldModelTransaction->total_price;
            $newModelTransactionSession->total_amount = $oldModelTransaction->total_amount;
            $newModelTransactionSession->is_closed = false;
            $newModelTransactionSession->order_id = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6) . '_' . time();
            $newModelTransactionSession->order_status = 'New';
            
            if (($flag = $newModelTransactionSession->save())) {
                
                foreach ($oldModelTransaction->transactionItems as $dataTransactionItem) {
                    
                    $newModelTransactionItem = new TransactionItem();
                    $newModelTransactionItem->transaction_session_id = $newModelTransactionSession->id;
                    $newModelTransactionItem->business_product_id = $dataTransactionItem->business_product_id;
                    $newModelTransactionItem->note = !empty($dataTransactionItem->note) ? $dataTransactionItem->note : null;
                    $newModelTransactionItem->price = $dataTransactionItem->price;
                    $newModelTransactionItem->amount = $dataTransactionItem->amount;
                    
                    if (!($flag = $newModelTransactionItem->save())) {
                        
                        break;
                    }
                }
            }
            
            if ($flag) {
                
                $transaction->commit();
                
                return $this->redirect(['order/checkout']);
            } else {
                
                $transaction->rollBack();
                
                $return['success'] = false;
                $return['type'] = 'danger';
                $return['icon'] = 'aicon aicon-icon-info';
                $return['title'] = 'Pesan Ulang Gagal';
                $return['text'] = 'Silahkan ulangi kembali proses pemesanan anda.';
            }
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
}