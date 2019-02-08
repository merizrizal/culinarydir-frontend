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
    public $password;

    /**
     * @var \core\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($email, $token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            
            throw new InvalidArgumentException('Token reset password tidak boleh kosong.');
        }
        
        $this->_user = User::findByEmailAndPasswordResetToken($email, $token);
        
        if (empty($this->_user)) {
            
            throw new InvalidArgumentException('Token reset password salah.');
        }
        
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'max' => 64],
        ];
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

