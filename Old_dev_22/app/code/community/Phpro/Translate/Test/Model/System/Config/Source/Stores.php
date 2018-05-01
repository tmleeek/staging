<?php
/**
 * @group Translate
 * @group ConfigStores
 */
class Phpro_Translate_Test_Model_System_Config_Source_Stores extends EcomDev_PHPUnit_Test_Case
{
	/**
	 * @test
	 * @loadFixture stores.yaml
	 * @loadFixture core_config_data.yaml
	 * @loadFixture phpro_translate.yaml
	 */
    public function toArray() {
    	$storesArray = Mage::getModel('translate/System_Config_Source_Stores')->toArray();
		$this->assertContains("Main Website", $storesArray);     
		$this->assertContains("Default Store View", $storesArray);
		$this->assertContains("French Store", $storesArray);
		$this->assertContains("German Store", $storesArray);  
    }
}