<?php
namespace frontend\models;

/**
 * User Post
 */
class Post extends \sybase\SybaseModel
{
    public $rating;
    public $text;
    public $image;

    public function scenarios() {

        $scenarios = parent::scenarios();
        $scenarios['postPhoto'] = [
            'rating', 'text', 'image'
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['rating'], 'required'],
            [['rating'], 'safe'],
            [['image'], 'required', 'on' => 'postPhoto'],
            [['image'], 'file', 'maxSize' => 1024 * 1024 * 7, 'maxFiles' => 10],
        ];
    }

}
