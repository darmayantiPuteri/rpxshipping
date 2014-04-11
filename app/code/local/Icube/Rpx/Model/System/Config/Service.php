<?php
class Icube_Rpx_Model_System_Config_Service
{
    public function toOptionArray()
    {
        $username = Mage::getStoreConfig('carriers/icube_rpx/username');
        $password = Mage::getStoreConfig('carriers/icube_rpx/password');
        
        require_once(Mage::getBaseDir('lib') . '/nusoap/nusoap.php');
        $wsdl="http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl";
        $client =new nusoap_client($wsdl,true);
        $result = $client->call('getService', array('user' => $username, 'password' => $password));
        $xml = simplexml_load_string($result);
        
        $service[] = array(
            'value' => 'all',
            'label'=> 'All Services'
                           );
        foreach($xml->DATA as $data){
            $service[] = array(
                             'value' => substr($data->SERVICE,0,3),
                             'label'=> $data->SERVICE
                             );
        }
        
        return $service;
    }
}