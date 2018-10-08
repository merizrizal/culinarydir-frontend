<?php

namespace frontend\components;

use yii\base\Widget;
use yii\helpers\Html;

class AddressType extends Widget {
    
    public $address;
    public $addressType;
    private $addressTypeShort;

    public function init() {
        parent::init();        
        
        if ($this->addressType == "Jalan") {

            $this->addressTypeShort = "Jl";
        } else if ($this->addressType == "Komplek") {

            $this->addressTypeShort = "Komp";
        } else if ($this->addressType == "Gang") {

            $this->addressTypeShort = "Gg";
        }
    }
    
    public function run() {
        
        return Html::encode($this->addressTypeShort . '. ' . $this->address);
    }
    
}