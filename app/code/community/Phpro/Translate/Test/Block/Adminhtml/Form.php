<?php
/**
 * 
 * @group Translate
 * @group Block
 * @group Form
 */
class Phpro_Translate_Test_Block_Adminhtml_Form extends EcomDev_PHPUnit_Test_Case 
{

	/**
	 * @test
	 * @loadFixture defaultsetup.yaml
	 */
	public function getAvailableLangs()
	{
		$className	= Mage::getConfig()->getBlockClassName('translate/adminhtml_form');
		$block		= new $className(); 
		$expectedAvailableLangs = array( 
									"0" => array("value" => "-1", "label" => "Default"),
									"1" => array("value" => "en", "label" => "en"),
									"2" => array("value" => "nl", "label" => "nl"),
									"3" => array("value" => "fr", "label" => "fr")
								  );
		$this->assertEquals($expectedAvailableLangs, $block->getAvailableLangs());
	}
}