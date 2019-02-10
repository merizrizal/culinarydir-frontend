<?php
namespace frontend\models;

use yii\base\Model;
use yii\base\InvalidArgumentException;
use core\models\User;

/**
 * Password reset form
 */
class ResetPassword extends Model
{
    public $email;
    public $username;
    public $token;
    public $password;

    /**
     * @var \core\models\User
     */
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'token'], 'trim'],
            ['password', 'required'],
            ['password', 'string', 'max' => 64],
            ['password', 'validateToken'],
        ];
    }
    
    public function validateToken($attribute, $params) {
        
        if (empty($this->token) || !is_string($this->token)) {
            
            $this->addError($attribute, 'Token reset password tidak boleh kosong.');
        }
        
        $this->_user = User::findByEmailAndPasswordResetToken($this->email, $this->token);
        
        if (empty($this->_user)) {
            
            $this->addError($attribute, 'Token reset password salah.');
        } else {
            
            $this->username = $this->_user->username;
        }
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}

