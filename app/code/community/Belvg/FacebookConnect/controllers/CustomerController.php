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

class Belvg_FacebookConnect_CustomerController extends Mage_Core_Controller_Front_Action{
    public function LoginAction() {

		$facebook = new Facebook_Api(array(
		  'appId'  => Mage::getStoreConfig('facebookconnect/settings/appid'),
		  'secret' => Mage::getStoreConfig('facebookconnect/settings/secret'),
		  'cookie' => true,
		));
		$fb_session = $facebook->getSession();

		$me = null;
		// Session based API call.
		if ($fb_session) {
		  try {
		    $uid = $facebook->getUser();
		    $me = $facebook->api('/me');
		  } catch (Facebook_Exception $e) {
		    error_log($e);
		  }
		}

		if (is_array($me)) {
			$session = Mage::getSingleton('customer/session');

			$db_read = Mage::getSingleton('core/resource')->getConnection('facebookconnect_read');
			$tablePrefix = (string)Mage::getConfig()->getTablePrefix();
			$sql = 'SELECT `customer_id`
					FROM `'.$tablePrefix.'belvg_facebook_customer`
					WHERE `fb_id` = '.$me['id'].'
					LIMIT 1';
			$data = $db_read->fetchRow($sql);

			if ($data) {
				$session->loginById($data['customer_id']);
			} else {
                                $sql = 'SELECT `entity_id`
                                        FROM `'.$tablePrefix.'customer_entity`
                                        WHERE email = "'.$me['email'].'"
                                        LIMIT 1';
                                $r = $db_read->fetchRow($sql);

                                if ($r) {
                                    $db_write = Mage::getSingleton('core/resource')->getConnection('facebookconnect_write');
                                    $sql = 'INSERT INTO `'.$tablePrefix.'belvg_facebook_customer`
                                                    VALUES ('.$r['entity_id'].', '.$me['id'].')';
                                    $db_write->query($sql);
                                    $session->loginById($r['entity_id']);
                                } else {
                                    $this->_registerCustomer($me, $session);
                                }
			}
			$this->_loginPostRedirect($session);
		}
    }

	public function LogoutAction() {
		$session = Mage::getSingleton('customer/session');
        $session->logout()
            ->setBeforeAuthUrl(Mage::getUrl());

        $this->_redirect('customer/account/logoutSuccess');
	}

	private function _registerCustomer($data, &$session)
	{
		$customer = Mage::getModel('customer/customer')->setId(null);
                $customer->setData('firstname', $data['first_name']);
                $customer->setData('lastname', $data['last_name']);
                $customer->setData('email', $data['email']);
                $customer->setData('password', md5(time().$data['id'].$data['locale']));
		$customer->getGroupId();
		$customer->save();
		$session->setCustomerAsLoggedIn($customer);
		$customer_id = $session->getCustomerId();
		$db_write = Mage::getSingleton('core/resource')->getConnection('facebookconnect_write');
		$tablePrefix = (string)Mage::getConfig()->getTablePrefix();
		$sql = 'INSERT INTO `'.$tablePrefix.'belvg_facebook_customer`
				VALUES ('.$customer_id.', '.$data['id'].')';
		$db_write->query($sql);
	}

	private function _loginPostRedirect(&$session)
	{

		if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
			$referer = Mage::helper('core')->urlDecode($referer);
			if ((strpos($referer, Mage::app()->getStore()->getBaseUrl()) === 0)
                || (strpos($referer, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)) {
				$session->setBeforeAuthUrl($referer);
			} else {
				$session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
			}
		} else {
			$session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
		}
		$this->_redirectUrl($session->getBeforeAuthUrl(true));
	}
}