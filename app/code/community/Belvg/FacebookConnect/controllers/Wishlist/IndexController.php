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

include_once("Mage/Wishlist/controllers/IndexController.php");

class Belvg_FacebookConnect_Wishlist_IndexController extends Mage_Wishlist_IndexController
{
    /**
     * Adding new item
     */
    public function addAction()
    {
        $session = Mage::getSingleton('customer/session');
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $this->_redirect('*/');
            return;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $wishlist->addNewItem($product->getId());
            $wishlist->save();

            Mage::dispatchEvent('wishlist_add_product', array('wishlist'=>$wishlist, 'product'=>$product));

            if ($referer = $session->getBeforeWishlistUrl()) {
                $session->setBeforeWishlistUrl(null);
            }
            else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping', $product->getName(), $referer);
            $session->addSuccess($message);

	/***/
            if (Mage::getStoreConfig('facebookconnect/wishlist/enabled')) {
                    $facebook = new Facebook_Api(array(
                      'appId'  => Mage::getStoreConfig('facebookconnect/settings/appid'),
                      'secret' => Mage::getStoreConfig('facebookconnect/settings/secret'),
                      'cookie' => true,
                    ));
                    $fb_session = $facebook->getSession();

                    $me = null;
                    if ($fb_session) {
                      try {
                        $uid = $facebook->getUser();
			    $message = str_replace('{product}', $product->getName(), Mage::getStoreConfig('facebookconnect/wishlist/note'));
                            $feed_data = array('message'=>$message,
                                                               'link'=>$product->getProductUrl(),
                                                               'picture'=>	$product->getImageUrl(),
                                                               'name'=>$product->getName(),
                                                               'caption'=>'',
                                                               'description'=>$product->getShortDescription(),
                                                               'access_token'=>$facebook->getAccessToken);

                        $me = $facebook->api('/me/feed/','post', $feed_data);
                      } catch (Facebook_Exception $e) {
                        error_log($e);
                      }
                    }
            }
	/****/
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist.'));
        }
        $this->_redirect('*');
    }
}
