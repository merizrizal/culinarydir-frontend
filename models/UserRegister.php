<?php
namespace frontend\models;

/**
 * @property string $password_repeat
 */

class UserRegister extends \backend\models\User
{
    public $password_repeat;

    public function rules() {
        return array_merge(parent::rules(), [
            [['password_repeat'], 'required'],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'message' => 'Password tidak sama'],
        ]);
    }
}