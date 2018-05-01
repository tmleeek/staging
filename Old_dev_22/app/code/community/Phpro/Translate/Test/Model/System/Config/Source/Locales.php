<?php
/**
 * @group Translate
 * @group ConfigLocales
 */
class Phpro_Translate_Test_Model_System_Config_Source_Locales extends EcomDev_PHPUnit_Test_Case
{
	/**
	 * @test
	 * @loadFixture phpro_translate.yaml
	 */
    public function toArray() {
        $expectedLocales = array('en_US' => 'English (United States)','fr_FR' => 'French (France)');
		$this->assertEquals($expectedLocales, Mage::getModel('translate/System_Config_Source_Locales')->toArray())   ;     
    }
}