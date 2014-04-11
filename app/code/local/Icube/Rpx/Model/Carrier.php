<?php
class Icube_Rpx_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface{
    
    protected $_code = 'icube_rpx';
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request){
        $username = $this->getConfigData('username');
        $password = $this->getConfigData('password');
        $account_number = $this->getConfigData('account_number');
        $origin = $this->getConfigData('origin');
        $service = $this->getConfigData('service');
        $handlingfee = $this->getConfigData('handlingfee');
        $disc = 0;
        
        if($service == 'all')
            $service = NULL;
        
        $result = Mage::getModel('shipping/rate_result');
        $dest = substr($request->getDestCity(),0,3);
        $weight = $request->getPackageWeight();
        
        require_once(Mage::getBaseDir('lib') . '/nusoap/nusoap.php');
        $wsdl="http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl";
        $client =new nusoap_client($wsdl,true);
        $err = $client->getError();
        $result1 = $client->call(
            'getRates',
            array(
                'user' => $username,
                'password' => $password,
                'account_number' => $account_number,
                'service_type' => $service,
                'origin' => $origin,
                'destination' => $dest,
                'weight' => $weight,
                'disc' => $disc
                  )
                                 );
        $xml = simplexml_load_string($result1);
        
        foreach($xml->DATA as $data){
            $rate = Mage::getModel('shipping/rate_result_method');
            
            $rate->setCarrier($this->_code);
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod('standand');
            $rate->setMethodTitle($data->SERVICE);
            $rate->setPrice($data->PRICE + $handlingfee);
            $rate->setCost(0);
            
            if($data->PRICE > 0)
                $result->append($rate);
        }
        
        return $result;
    }
    
    public function getAllowedMethods(){
        return array(
            'standard' => 'Standard',
        );
    }
    
    public function isTrackingAvailable()
    {
        return true;
    }
    
    public function getTrackingInfo($tracking_number){
	    $tracking_result = $this->getTracking($tracking_number);
	    if ($tracking_result instanceof Mage_Shipping_Model_Tracking_Result)
		{
			if ($trackings = $tracking_result->getAllTrackings())
			{
				return $trackings[0];
			}
		}
		elseif (is_string($tracking_result) && !empty($tracking_result))
		{
			return $tracking_result;
		}
		
		return false;
    }
    
    public function getTracking($trackings)
    {
    	$username = $this->getConfigData('username');
        $password = $this->getConfigData('password');
        
    	$result = Mage::getModel('shipping/tracking_result');
        
        require_once(Mage::getBaseDir('lib') . '/nusoap/nusoap.php');
        $wsdl="http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl";
        $client =new nusoap_client($wsdl,true);
        $status = $client->call(
            'getTrackingAWB',
            array(
                'user' => $username,
                'password' => $password,
                'awb' => $trackings,
                  )
                                 );
        $xml = simplexml_load_string($status);
        
        if($xml->DELIVERY_TO){
            foreach($xml->DATA as $data){
                $dataStatus .= "
                    <tr>
                        <td>$data->TRACKING_ID</td>
                        <td>$data->TRACKING_DESC</td>
                        <td>$data->LOCATION</td>
                        <td>$data->TRACKING_DATE</td>
                        <td>$data->TRACKING_TIME</td>
                    </tr>
                ";
            }
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier( $this->_code );
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackings);
            $tracking->addData(
            	array(
            		'status' => "
                        <table border='0' width='100%'>
                            <tr>
                                <td>
                                    Delivery To : <b>$xml->DELIVERY_TO</b><br>
                                    Delivery Location : <b>$xml->DELIVERY_LOC</b><br>
                                </td>
                                <td>
                                    <img src='$xml->IMAGE_FOTO'><br>
                                    Received by : <b>$xml->RECEIVED_BY</b> (<a href='$xml->IMAGE_SIGNATURE' target='_blank'>Signature</a>)<br>
                                    Delivery Date : <b>$xml->DELIVERY_DATE</b><br>
                                    Time : <b>$xml->DELIVERY_TIME</b><br>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table border='1' width='100%'>
                            $dataStatus
                        </table>
                        ",
            	)
            );
            $result->append($tracking);
        } else {
            $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier( $this->_code );
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackings);
            $error->addData(
            	array(
            		'status' => "Please Check Your Account or AWB",
            	)
            );
            $result->append($error);
        }
        
        return $result;
    }

}