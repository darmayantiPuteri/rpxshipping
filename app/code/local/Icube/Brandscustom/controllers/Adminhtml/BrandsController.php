<?php
require_once Mage::getModuleDir('controllers','Belvg_Brands').DS.'Adminhtml'.DS.'BrandsController.php';
class Icube_Brandscustom_Adminhtml_BrandsController extends Belvg_Brands_Adminhtml_BrandsController
{   
    public function saveAction()
    {
        $store_id = $this->getStoreId();

        $brand_id = (int) $this->getRequest()->getParam('id', 0);

        $redirectBack = $this->getRequest()->getParam('back', FALSE);

        try {
            $post_data = $this->getRequest()->getPost();

            $post_data['id'] = $brand_id;
            $post_data['store_id'] = $store_id;

            $brand = Mage::getModel('brands/brands')->setData($post_data);
            /* @var $brand Belvg_Brands_Model_Brands */

            $validate = $brand->validate();

            if ($validate === TRUE) {
                $brand->prepareBrand();
                $brand->setId($brand_id)->save();
                
                //save attribute
                $attribute_model = Mage::getModel('eav/entity_attribute');
                $attribute_code = $attribute_model->getIdByCode('catalog_product', 'brands');
                $attribute = $attribute_model->load($attribute_code);
                
                $value['option'] = array($post_data['title']);
                $order['option'] = NULL;
                $result = array('value' => $value,'order' => $order);
                $attribute->setData('option',$result);
                $attribute->save();
                
                $productModel = Mage::getModel('catalog/product');
                $attr = $productModel->getResource()->getAttribute("brands");
                if ($attr->usesSource()) {
                    $id = $attr->getSource()->getOptionId($post_data['title']);
                }
                //save attribute

                $success_message = Mage::helper('adminhtml')->__('Item was successfully saved' . $id);

                $this->_getSession()
                        ->addSuccess($success_message)
                        ->setBrandsData(FALSE);

                Mage::dispatchEvent('brands_controller_brand_save', array('brand' => $brand, 'store' => $store_id));

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array('id' => $brand->getID(), 'store' => $store_id, '_current' => TRUE));
                } else {
                    $this->_redirect('*/*/');
                }
                
            } else {
                Mage::throwException(implode('<br />', $validate));
            }
        } catch (Exception $e) {
            $this->_getSession()
                    ->addError($e->getMessage())
                    ->setBrandsData($this->getRequest()->getPost());
            $this->_redirect('*/*/edit', array('id' => $brand_id, 'store' => $store_id));
        }
    }
    
    private function getStoreId()
    {
        return (int) $this->getRequest()->getParam('store', Mage_Core_Model_App::ADMIN_STORE_ID);
    }

}
