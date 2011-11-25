<?php

$model = Mage::getModel('adminhtml/config_data')
      ->setSection('catalog')
      ->setGroups(array(
          'custom_options' => array(
              'fields' => array(
                  'use_calendar'      => array('value' => 1),
                  'date_fields_order' => array('value' => array('d', 'm', 'y')),
                  'time_format'       => array('value' => '24h'),
                  'year_range'        => array('value' => array(1900, 2100))
              )
          )
      ))
;
$model->save();

$model = Mage::getModel('adminhtml/config_data')
      ->setSection('cataloginventory')
      ->setGroups(array(
          'item_options' => array(
              'fields' => array(
                  'manage_stock'      => array('value' => 0),
              )
          )
      ))
;
$model->save();

// reinit configuration
Mage::getConfig()->reinit();
Mage::app()->reinitStores();
