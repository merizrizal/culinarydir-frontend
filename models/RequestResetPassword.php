<?php
namespace frontend\models;

use core\models\User;
use yii\base\Model;

class RequestResetPassword extends Model
{
    public $email;
    public $verificationCode;
    public $token;

    public $isRequestToken;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['verificationCode', 'trim'],
            ['verificationCode', 'required', 'when' => function($model) {

                return !$model->isRequestToken;
            }],
            ['verificationCode', 'validateVerificationCode', 'when' => function($model) {

                return !$model->isRequestToken;
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('app', 'Email'),
            'verificationCode' => \Yii::t('app', 'Verification Code'),
        ];
    }

    public function validateVerificationCode($attribute, $params) {

        $modelUser = User::find()
            ->andWhere(['email' => $this->email])
            ->andWhere(['ilike', 'password_reset_token', $this->verificationCode . '_'])
            ->andWhere(['not_active' => false])
            ->asArray()->one();

        if (empty($modelUser)) {

            $this->addError($attribute, \Yii::t('app', 'Wrong verification code'));
        } else {

            $this->token = $modelUser['password_reset_token'];
        }
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail($isFromApi = false)
    {
        $mailer = \Yii::$app->mailer;

        $user = User::findOne([
            'not_active' => false,
            'email' => $this->email,
        ]);

        if (empty($user)) {

            $mailer = $mailer->compose(['html' => 'password_reset_token_unavailable'], ['isFromApi' => $isFromApi]);
        } else {

            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {

                $user->generatePasswordResetToken();

                if (!$user->save()) {

                    return false;
                }
            }

            $mailer = $mailer->compose(
                ['html' => 'password_reset_token'],
                ['user' => $user, 'isFromApi' => $isFromApi]);
        }

        return $mailer->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' Support'])
            ->setTo($this->email)
            ->setSubject('Reset password untuk akun ' . \Yii::$app->name)
            ->send();
    }
}

