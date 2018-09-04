<?php
namespace frontend\models;

use Yii;
use backend\models\User;

/**
 * Change Password
 */
class ChangePassword extends \sybase\SybaseModel
{
    public $id;
    public $current_pass;
    public $new_pass;
    public $confirm_pass;

    /**
     * @var \backend\models\User
     */
    private $_user;
    
    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($id, $config = [])
    {
        $this->_user = User::findIdentity($id);
        
        if (!$this->_user) {
            throw new \yii\web\NotFoundHttpException();
        }
        
        $this->id = $this->_user->id;
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['current_pass','new_pass','confirm_pass'], 'required'],
            ['confirm_pass', 'compare', 'compareAttribute' => 'new_pass'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'current_pass' => 'Password',
            'new_pass' => 'Password Baru',
            'confirm_pass' => 'Konfirmasi Password Baru',
        ];
    }
    
    /**
     * Changes password.
     *
     * @return boolean if password was changed.
     */
    public function changePassword()
    {
        $user = $this->_user;
        
        if ($user->validatePassword($this->current_pass)) {
            
            $user->setPassword($this->new_pass);
            
            $this->current_pass = '';
            $this->new_pass = '';
            $this->confirm_pass = '';
            
            return $user->save();
        } else {
            
            $this->addError('current_pass', 'Password tidak sama');
                        
            return false;
        }
    }

}
