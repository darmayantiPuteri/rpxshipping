<?php
class Icube_Fee_Model_Fee extends Varien_Object{
	const FEE_AMOUNT = 10;

	public static function getFee(){
		$fee = rand(10,50);
		return $fee;
	}
	public static function canApply($address){
		//put here your business logic to check if fee should be applied or not
		//if($address->getAddressType() == 'billing'){
		return true;
		//}
	}
}