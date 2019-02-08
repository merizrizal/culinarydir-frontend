<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use core\models\User;

class RequestResetPassword extends Model
{
    public $email;
    public $verificationCode;
    public $token;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\core\models\User',
                'filter' => ['not_active' => false],
                'message' => 'Tidak ada pengguna dengan alamat email ini.'
            ],
            ['verificationCode', 'trim'],
            ['verificationCode', 'required'],
            ['verificationCode', 'validateVerificationCode']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'verificationCode' => Yii::t('app', 'Verification Code'),
        ];
    }
    
    public function validateVerificationCode($attribute, $params) {
        
        $modelUser = User::find()
            ->andWhere(['email' => $this->email])
            ->andWhere(['ilike', 'password_reset_token', $this->verificationCode . '_'])
            ->andWhere(['not_active' => false])
            ->asArray()->one();
        
        if (empty($modelUser)) {
            
            $this->addError($attribute, Yii::t('app', 'Wrong verification code'));
        } else {
            
            $this->token = $modelUser['password_reset_token'];
        }
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'not_active' => false,
            'email' => $this->email,
        ]);

        if (empty($user)) {
            
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            
            $user->generatePasswordResetToken();
            
            if (!$user->save()) {
                
                return false;
            }
        }

        return Yii::$app->mailer
            ->compose(
                ['html' => 'password_reset_token'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Support'])
            ->setTo($this->email)
            ->setSubject('Reset password untuk akun ' . Yii::$app->name)
            ->send();
    }
}

