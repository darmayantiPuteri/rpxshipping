<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_FacebookConnect
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('belvg_facebook_customer')}`
CHANGE `customer_id` `customer_id` INT( 10 ) UNSIGNED NOT NULL;

ALTER TABLE `{$this->getTable('belvg_facebook_customer')}`
ADD FOREIGN KEY ( `customer_id` ) REFERENCES `customer_entity` (
`entity_id`
) ON DELETE CASCADE ;
");

$installer->endSetup();
