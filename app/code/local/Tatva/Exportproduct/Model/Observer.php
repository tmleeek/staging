<?php
class Tatva_Exportproduct_Model_Observer
{ 
	public function exportsimpleproduct()
    {
    	$profileId = 11;	     
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
		$profile = Mage::getModel('dataflow/profile');
		$userModel = Mage::getModel('admin/user');
		$userModel->setUserId(0);
		Mage::getSingleton('admin/session')->setUser($userModel);
		$profile->load($profileId);
		if (!$profile->getId()) {
		    Mage::getSingleton('adminhtml/session')->addError('ERROR: Incorrect profile id');
		}
		 
		Mage::register('current_convert_profile', $profile);
		$profile->run();
		//$recordCount = 0;
		//$batchModel = Mage::getSingleton('dataflow/batch');
		//echo "EXPORT COMPLETE. BATCHID: " . $batchModel->getId();
    }
	
	public function exportcategory()
    {
    	$profileId = 32;	     
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
		$profile = Mage::getModel('dataflow/profile');
		$userModel = Mage::getModel('admin/user');
		$userModel->setUserId(0);
		Mage::getSingleton('admin/session')->setUser($userModel);
		$profile->load($profileId);
		if (!$profile->getId()) {
		    Mage::getSingleton('adminhtml/session')->addError('ERROR: Incorrect profile id');
		}
		 
		Mage::register('current_convert_profile', $profile);
		$profile->run();
		//$recordCount = 0;
		//$batchModel = Mage::getSingleton('dataflow/batch');
		//echo "EXPORT COMPLETE. BATCHID: " . $batchModel->getId();
    }
	
	public function importorder()
    {
    	$profileId = 28;	     
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
		$profile = Mage::getModel('dataflow/profile');
		$userModel = Mage::getModel('admin/user');
		$userModel->setUserId(0);
		Mage::getSingleton('admin/session')->setUser($userModel);
		$profile->load($profileId);
		if (!$profile->getId()) {
		    Mage::getSingleton('adminhtml/session')->addError('ERROR: Incorrect profile id');
		}
		 
		//Mage::register('current_convert_profile', $profile);
		$profile->run();
		//$recordCount = 0;
		//$batchModel = Mage::getSingleton('dataflow/batch');
		//echo "EXPORT COMPLETE. BATCHID: " . $batchModel->getId();
    }
	
	public function exportorder()
    {
    	$profileId = 72;	     
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
		$profile = Mage::getModel('dataflow/profile');
		$userModel = Mage::getModel('admin/user');
		$userModel->setUserId(0);
		Mage::getSingleton('admin/session')->setUser($userModel);
		$profile->load($profileId);
		if (!$profile->getId()) {
		    Mage::getSingleton('adminhtml/session')->addError('ERROR: Incorrect profile id');
		}
		 
		//Mage::register('current_convert_profile', $profile);
		$profile->run();
		//$recordCount = 0;
		//$batchModel = Mage::getSingleton('dataflow/batch');
		//echo "EXPORT COMPLETE. BATCHID: " . $batchModel->getId();
    }
	
	
	
	public function exportbundleproduct()
    {
    	$profileId = 12;	     
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
		$profile = Mage::getModel('dataflow/profile');
		$userModel = Mage::getModel('admin/user');
		$userModel->setUserId(0);
		Mage::getSingleton('admin/session')->setUser($userModel);
		$profile->load($profileId);
		if (!$profile->getId()) {
		    Mage::getSingleton('adminhtml/session')->addError('ERROR: Incorrect profile id');
		}
		 
		Mage::register('current_convert_profile', $profile);
		$profile->run();
		//$recordCount = 0;
		//$batchModel = Mage::getSingleton('dataflow/batch');
		//echo "EXPORT COMPLETE. BATCHID: " . $batchModel->getId();
    }
	
	public function exportenstoreproduct()
    {
    	$profileId = 13;	     
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
		$profile = Mage::getModel('dataflow/profile');
		$userModel = Mage::getModel('admin/user');
		$userModel->setUserId(0);
		Mage::getSingleton('admin/session')->setUser($userModel);
		$profile->load($profileId);
		if (!$profile->getId()) {
		    Mage::getSingleton('adminhtml/session')->addError('ERROR: Incorrect profile id');
		}
		 
		Mage::register('current_convert_profile', $profile);
		$profile->run();
		//$recordCount = 0;
		//$batchModel = Mage::getSingleton('dataflow/batch');
		//echo "EXPORT COMPLETE. BATCHID: " . $batchModel->getId();
    }
	
	public function testMail()
    {
        $mailTemplate = Mage::getModel('core/email_template');
        $customer_name = 'nisha';
        $mailSubject = 'Ekomi Link';
        $customer_email = 'nisha.baksani@tatvasoft.com';
        $store_id = 1;
       

    	$mailTemplate->setTemplateSubject($mailSubject)
    				 ->sendTransactional(
    						Mage::getStoreConfig('catalog/productalert/email_price_template',$store_id),
                            Mage::getStoreConfig('catalog/productalert/email_identity'),
    						$customer_email,
                            $customer_name

                     );


    }

}
?>
