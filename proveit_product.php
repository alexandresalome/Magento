<?php
$product = Mage::getModel('catalog/product');
$product->setWebsiteIds(array(Mage::getModel('core/website')->load('base', 'code')));
$product->setSku('COURSE');
$product->setPrice(4000);
$product->setAttributeSetId(Mage::getModel('eav/entity_attribute_set')->load($product->getResource()->getTypeId(), 'entity_type_id')->getId());
$product->setCategoryIds(array(Mage::getModel('catalog/category')->loadByAttribute('name', 'Voitures')->getId()));
$product->setTypeId('simple');
$product->setName('Voiture de course');
$product->setDescription('Voiture qui va vite, très vite');
$product->setShortDescription('Voiture rapide');
$product->setStatus(1);
$product->setTaxClassId(Mage::getModel('tax/class')->load('Taxable Goods', 'class_name')->getId());
$product->setWeight(0);
$product->setCreatedAt(strtotime('now'));
$product->save();

$stockItem = Mage::getModel('cataloginventory/stock_item');
$stockItem->setData('is_in_stock', 1);
$stockItem->setData('product_id', $product->getId());
$stockItem->setData('stock_id', Mage::getModel('cataloginventory/stock')->load('Default', 'stock_name'));
$stockItem->save();
