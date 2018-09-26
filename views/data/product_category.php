<?php

use yii\helpers\Html;

/* @var $productCategory core\models\ProductCategory */ ?>

<?php
if (!empty($productCategory['parent'])) {
    
    echo '<div class="col-md-12 col-sm-12 col-xs-12 mb-20">';
    
        echo Html::label('Kategori Umum');
        
        echo '<div class="row">';

            foreach ($productCategory['parent'] as $dataProductCategoryParent) {
                
                echo '
                    <div class="col-lg-3 col-md-3 col-sm-4 col-tab-6 col-xs-12 product-list">' .
                        Html::a($dataProductCategoryParent['name'], '', [
                            'class' => 'product-category-name',
                            'data-id' => $dataProductCategoryParent['id']
                        ]) .
                    '</div>';
            }

        echo '</div>';
        
    echo '</div>';

}

if (!empty($productCategory['child'])) {
    
    echo '<div class="col-md-12 col-sm-12 col-xs-12">';
    
        echo Html::label('Kategori Spesifik');
        
        echo '<div class="row">';

            foreach ($productCategory['child'] as $dataProductCategoryChild) {
                
                echo '
                    <div class="col-lg-3 col-md-3 col-sm-4 col-tab-6 col-xs-12 product-list">' .
                        Html::a($dataProductCategoryChild['name'], '', [
                            'class' => 'product-category-name',
                            'data-id' => $dataProductCategoryChild['id']
                        ]) .
                    '</div>';
            }
        
        echo '</div>';

    echo '</div>';

} ?>