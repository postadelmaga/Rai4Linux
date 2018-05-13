<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('video/rai'))
    ->addColumn('video_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Video ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
        'nullable' => true,
        'default' => null,
    ), 'Title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 512, array(
        'nullable' => true,
        'default' => null,
    ), 'Description')
    ->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable' => false,
    ), 'Program Date')
    ->addColumn('time', Varien_Db_Ddl_Table::TYPE_TIME, null, array(
        'nullable' => false,
    ), 'Program Time')
    ->addColumn('video_urls_json', Varien_Db_Ddl_Table::TYPE_TEXT, 512, array(
        'nullable' => false,
        'default' => null,
    ), 'Video urls json')
    ->addColumn('image_url', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
        'nullable' => true,
        'default' => null,
    ), 'image url')
    ->addColumn('image_big_url', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
        'nullable' => true,
        'default' => null,
    ), 'image big')
    ->setComment('Rai Video Table');
$installer->getConnection()->createTable($table);