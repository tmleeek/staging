<?php
class Extendware_EWImageOpt_Helper_Override_Mage_Image extends Extendware_EWImageOpt_Helper_Override_Mage_Image_Bridge
{
	public function getProductForEwimageopt() {
		return $this->getProduct();
	}
}