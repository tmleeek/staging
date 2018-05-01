<?php

class Tatva_Attachpdf_Helper_Data extends Mage_Sales_Helper_Data
{
	public function getCurrentStoreId()
	{
		return Mage::registry('current_store_id');
	}
	public function addAttachment($mailTemplate,$pdf, $name = "order.pdf"){
		$file = $pdf->render();
		$mailTemplate->getMail()->createAttachment($file,'application/pdf',Zend_Mime::DISPOSITION_ATTACHMENT,Zend_Mime::ENCODING_BASE64,$name.'.pdf');
	}
}