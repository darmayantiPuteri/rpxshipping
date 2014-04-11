<?php
/**
 * @author mhistihori@gmail.com
 */
class Icube_Rpx_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getUaeCities(){
        $helper = Mage::helper('directory');
        $cities = array(
            $helper->__('Abu Dhabi'),
            $helper->__('Ajman'),
            $helper->__('Al Ain'),
            $helper->__('Dubai'),
            $helper->__('Fujairah'),
            $helper->__('Ras al Khaimah'),
            $helper->__('Sharjah'),
            $helper->__('Jawa Barat'),
                        );
        return $cities;
    }
    
    public function getUaeCitiesAsDropdown($selectedCity = '')
    {
        $cities = $this->getUaeCities();
        $options = '';
        foreach($cities as $city){
            $isSelected = $selectedCity == $city ? ' selected="selected"' : null;
            $options .= '<option value="' . $city . '"' . $isSelected . '>' . $city . '</option>';
        }
        return $options;
    }
}