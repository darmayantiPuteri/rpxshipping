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
            $attribute_model = Mage::getModel('eav/entity_attribute');
            $attribute_code = $attribute_model->getIdByCode('catalog_product', 'brands');
            
            if(!$brand_id){
		        //save attribute
                $attribute = $attribute_model->load($attribute_code);
                
                $value['option'] = array($post_data['title']);
                $order['option'] = NULL;
                $result = array('value' => $value,'order' => $order);
                $attribute->setData('option',$result);
                $attribute->save();
                
                // get attribute option by title
                $attId = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->addFieldToFilter('attribute_id', $attribute_code)
                ->addFieldToFilter('value', $post_data['title'])
                ->setStoreFilter($store_id, false)
                ->load()->getFirstItem();
                $brand_id = $attId->getOptionId();
                $post_data['created_at'] = date('m/d/y h:i:s', Mage::getModel('core/date')->timestamp(time()));
	        }
	        else{
	        	$data = array();
		        $attr_model = Mage::getModel('catalog/resource_eav_attribute')->load($attribute_code); 
		        $data['option']['value'] = array( $brand_id => array( $store_id => $post_data['title']));
		        $attr_model->addData($data);
		        //Save the updated model
				$attr_model->save();
				Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
	        }
	        
            $post_data['id'] = $brand_id;
            $post_data['store_id'] = $store_id;

            $brand = Mage::getModel('brands/brands')->setData($post_data);
            /* @var $brand Belvg_Brands_Model_Brands */

            $validate = $brand->validate();

            if ($validate === TRUE) {
                $brand->prepareBrand();
                $brand->setId($brand_id)->save();
                
                $productModel = Mage::getModel('catalog/product');
                $attr = $productModel->getResource()->getAttribute("brands");
                if ($attr->usesSource()) {
                    $id = $attr->getSource()->getOptionId($post_data['title']);
                }

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
    
    public function deleteAction()
    {
        if ($brand_id = (int) $this->getRequest()->getParam('id', 0)) {
            try {
                $brand = Mage::getModel('brands/brands');
                /* @var $brands_model Belvg_Brands_Model_Brands */

                Mage::dispatchEvent('brands_controller_brand_delete', array('brand' => $brand));
                $brand->setId($brand_id)->delete();
                
               // Delete attribute option
                
				$attribute_model = Mage::getModel('eav/entity_attribute'); 
				$attribute_code = $attribute_model->getIdByCode('catalog_product', 'brands');
				$attribute = $attribute_model->load($attribute_code);
				
				$options= Mage::getModel('eav/entity_attribute_source_table')->setAttribute($attribute)->getAllOptions(false);
				
				foreach ($options as $option) {
				    if ($option['label'] != '' && $option['value'] == $brand_id) {  
				        $newOptions['value'][$option['value']][]  = $option['label'];
				        $newOptions['delete'][$option['value']]   = $option['value'];// delete it
				    }
				}
				$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
				$setup->addAttributeOption($newOptions);
               
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }

        $this->_redirect('*/*/');
    }
    
    public function massDeleteAction()
    {
        $brands_ids = $this->getRequest()->getParam('brand', NULL);
        if (!is_array($brands_ids)) {
            $this->_getSession()->addError($this->__('Please select brands(s).'));
        } else {
            if (!empty($brands_ids)) {
                try {
                	$attribute_model = Mage::getModel('eav/entity_attribute'); 
					$attribute_code = $attribute_model->getIdByCode('catalog_product', 'brands');
					$attribute = $attribute_model->load($attribute_code);
					
					$options= Mage::getModel('eav/entity_attribute_source_table')->setAttribute($attribute)->getAllOptions(false);

                    foreach ($brands_ids as $brands_id) {
                        $brand = Mage::getSingleton('brands/brands')->load($brands_id);
                        /* @var $brand Belvg_Brands_Model_Brands */

                        Mage::dispatchEvent('brands_controller_brand_delete', array('brand' => $brand));
                        $brand->delete();
                        
                        // Delete attribute option				
						foreach ($options as $option) {
						    if ($option['label'] != '' && $option['value'] == $brands_id) {  
						        $newOptions['value'][$option['value']][]  = $option['label'];
						        $newOptions['delete'][$option['value']]   = $option['value'];// delete it
						    }
						}
						$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
						$setup->addAttributeOption($newOptions);
  
                    }

                    $this->_getSession()->addSuccess(
                            $this->__('Total of %d record(s) have been deleted.', count($brands_ids))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/*/index');
    }
    
    private function getStoreId()
    {
        return (int) $this->getRequest()->getParam('store', Mage_Core_Model_App::ADMIN_STORE_ID);
    }

}
