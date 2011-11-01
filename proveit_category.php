<?php

$category = Mage::getModel('catalog/category');
$category->addData(array(
    'name'      => 'Voitures',
    'is_active' => 1,
    'url_key'   => 'voitures'
));

$parentCategory = Mage::getModel('catalog/category')->loadByAttribute('name', 'Default Category');
$category->setPath($parentCategory->getPath());

$category->save();
