<?php
namespace frontend\components;

use yii\base\Widget;
use yii\helpers\Html;

class AddressType extends Widget
{
    public $businessLocation;
    
    public $showDetail = false;

    public $addressType;

    private $addressTypeShort;

    public function init()
    {
        parent::init();

        if ($this->businessLocation['address_type'] == "Jalan") {

            $this->addressTypeShort = "Jl";
        } else if ($this->businessLocation['address_type'] == "Komplek") {

            $this->addressTypeShort = "Komp";
        } else if ($this->businessLocation['address_type'] == "Gang") {

            $this->addressTypeShort = "Gg";
        }
    }

    public function run()
    {
        $detail = $this->showDetail && !empty($this->businessLocation['village']) && !empty($this->businessLocation['district']) ? ', ' . $this->businessLocation['village']['name'] . ', ' . $this->businessLocation['district']['name'] : '';
        
        return Html::encode($this->addressTypeShort . '. ' . trim($this->businessLocation['address']) . $detail);
    }
    
}