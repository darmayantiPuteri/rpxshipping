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

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('belvg_facebook_customer')} (
  `customer_id` int(10) NOT NULL,
  `fb_id` bigint(20) NOT NULL,
  UNIQUE KEY `FB_CUSTOMER` (`customer_id`,`fb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT IGNORE INTO {$installer->getTable('core/config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES
('default', 0, 'facebookconnect/like/enabled', '0'),
('default', 0, 'facebookconnect/like/faces', '0'),
('default', 0, 'facebookconnect/like/width', '450'),
('default', 0, 'facebookconnect/like/color', 'light'),
('default', 0, 'facebookconnect/like/layout', 'standart'),
('default', 0, 'facebookconnect/wishlist/enabled', 'standart');
");
$installer->endSetup();
