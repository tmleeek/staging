<?php
/**
 * @group Translate
 * @group ConfigStoreLocales
 */
class Phpro_Translate_Test_Model_System_Config_Source_Store_Locales extends EcomDev_PHPUnit_Test_Case
{
	/**
	 * @test
	 * @loadFixture stores.yaml
	 * @loadFixture core_config_data.yaml
	 */
    public function toArray() {
        $locales = Mage::getModel('translate/System_Config_Source_Store_Locales')->toArray();
        $expectedLocales = array('en_US', 'fr_FR', 'de_DE');
        $this->assertEquals($expectedLocales, $locales);
    }
    
	/**
	 * @test
	 * @loadFixture stores.yaml
	 * @loadFixture core_config_data.yaml
	 */
    public function toOptionArray() {
        $locales = Mage::getModel('translate/System_Config_Source_Store_Locales')->toOptionArray();
        $expectedLocales = array(
        						array('value' => 'en_US', 'label' => 'English (United States)'),
        						array('value' => 'fr_FR', 'label' => 'French (France)'),
        						array('value' => 'de_DE', 'label' => 'German (Germany)'),
        				   );
        $this->assertEquals($expectedLocales, $locales);
    }
}