<?php
/**
 * 
 * @group Translate
 * @group Data
 *
 */
class Phpro_Translate_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case {
	
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 */
	public function helloworld()
	{
		$this->assertEquals(1, Mage::getStoreConfig('translate/general/translation_logging'));
		$this->assertTrue(true);
	}
}