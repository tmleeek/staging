<?php
/**
 * @group Translate
 * @group Translate
 */
class Phpro_Translate_Test_Model_Translator extends EcomDev_PHPUnit_Test_Case
{
	CONST MODULE_TRANSLATION_FILENAME = "Phpro_Translate.csv";
	CONST MODULE_TRANSLATION_PATH = "/phpro_TEST/";
	CONST MODULE_TRANSLATION_LOCALE = "en_US";
   
	/**
	 * @test
	 * @loadFixture core_translate.yaml
	 */
	public function searchInDatabaseWithKeysSetToTrue()
	{
		$this->_deleteModuleTranslationFile();
		$model = $this->getModelMock("translate/translator", array("_loadAdvancedThemeTranslation", "_getModuleConfig", "_getModuleFilePath", "_getStoreConfigLocale"));
        $model->expects($this->any())
             ->method('_getModuleConfig')
             ->will($this->returnValue(array()));
        $model->expects($this->any())
             ->method('_loadAdvancedThemeTranslation')
             ->will($this->returnValue(null));
        $model->expects($this->any())
             ->method('_getModuleFilePath')
             ->will($this->returnValue($this->_getModuleTranslationFilePath()));
        $model->expects($this->any())
             ->method('_getStoreConfigLocale')
             ->will($this->returnValue($this::MODULE_TRANSLATION_LOCALE));
             
		$expectedResult = array("Size" => array('translate' => "differentString" , 'source' => "Database ()", 'store_name' => 'Default Store View'));
		$this->assertEquals($expectedResult, $model->search('Size', false, 'all', null,0, true));
	}
	/**
	 * @test
	 * @loadFixture core_translate.yaml
	 */
	public function searchInDatabaseWithKeysSetToFalse()
	{
		$this->_deleteModuleTranslationFile();
		$model = $this->getModelMock("translate/translator", array("_loadAdvancedThemeTranslation", "_getModuleConfig", "_getModuleFilePath", "_getStoreConfigLocale"));
        $model->expects($this->any())
             ->method('_getModuleConfig')
             ->will($this->returnValue(array()));
        $model->expects($this->any())
             ->method('_loadAdvancedThemeTranslation')
             ->will($this->returnValue(null));
        $model->expects($this->any())
             ->method('_getModuleFilePath')
             ->will($this->returnValue($this->_getModuleTranslationFilePath()));
        $model->expects($this->any())
             ->method('_getStoreConfigLocale')
             ->will($this->returnValue($this::MODULE_TRANSLATION_LOCALE));
             
		$expectedResult = array("Size" => array('translate' => "differentString", 'source' => "Database ()", 'store_name' => 'Default Store View'));
		$this->assertEquals($expectedResult, $model->search('differentString', false, 'all', null,0, false));
	}
	/**
	 * @test
	 */
	public function searchKeyInModuleTranslationsWithKeysSetToTrue()
	{
		$model 			= $this->_getTranslateModelMock();
        $expectedResult = array('Phpro' => array('translate' => 'theverybest', 'source' => 'Module (Phpro_Translate)', 'store_name' => 'Default Store View'));
	    $this->assertEquals($expectedResult, $model->search("Phpro", false, 'all', null, 0, true));
	}
	/**
	 * @test
	 */
	public function searchKeyInModuleTranslationsWithKeysSetToFalse()
	{
		$model 			= $this->_getTranslateModelMock();
	    $expectedResult = array('Phpro' => array('translate' => 'theverybest', 'source' => 'Module (Phpro_Translate)', 'store_name' => 'Default Store View'));
	    $this->assertEquals($expectedResult, $model->search('theverybest', false, 'all', null, 0, false));

	}
	/**
	 * @test
	 */
	public function searchValueInModuleTranslationsWithKeysSetToTrue()
	{
		$model 			= $this->_getTranslateModelMock();
	    $expectedResult = array('Phpro' => array('translate' => 'theverybest', 'source' => 'Module (Phpro_Translate)', 'store_name' => 'Default Store View'));
	    $this->assertEquals($expectedResult, $model->search("Phpro", false, 'all', null, 0, true));
	    $expectedResult = array("Phpro_Translate::Phpro" => array('translate' => 'theverybest' , 'source' => 'Module (Phpro_Translate)', 'store_name' => 'Default Store View'));
	    $this->assertEquals(array(), $model->search('theverybest', false, 'all', null, 0, true));
	}
	
	/**
	 * @test
	 */
	public function searchValueInModuleTranslationsWithKeysSetToFalse()
	{
		$model 			= $this->_getTranslateModelMock();
	    $expectedResult = array('Phpro' => array('translate' => 'theverybest', 'source' => 'Module (Phpro_Translate)', 'store_name' => 'Default Store View'));
	    $this->assertEquals($expectedResult, $model->search('theverybest',false, 'all', null, 0, false));
	    $expectedResult = array();
	    $this->assertEquals($expectedResult, $model->search('Phpro', false, 'all', null, 0, false));
	}
	
	/**
	 * @test
	 */
	public function scopeAddedToArrayKeyWhenCalledMultipleTimes()
	{
		$model 			= $this->_getTranslateModelMock();
        $expectedResult = array('Phpro' => array('translate' => 'theverybest' , 'source' => 'Module (Phpro_Translate)', 'store_name' => 'Default Store View'));
	    $this->assertEquals($expectedResult, $model->search("Phpro", false, 'all', null, 0, true));
	    $expectedResult = array("Phpro_Translate::Phpro" => array('translate' => 'theverybest' , 'source' => 'Module (Phpro_Translate)', 'store_name' => 'Default Store View'));
	    $this->assertEquals($expectedResult, $model->search("Phpro", false, 'all', null, 0, true));
	}
	
	/**
	 * @test
	 * @loadFixture stores.yaml
	 */
	public function searchValueInModuleInMultiStoreSetup()
	{
		$model 			= $this->_getTranslateModelMock();
	    $expectedResult = array(
	    					'Phpro' => array('translate' => 'theverybest', 'source' => 'Module (Phpro_Translate)', 'store_name' => 'Canada Store'),
						    "Phpro_Translate::Phpro" => array('translate' => 'theverybest', 'source' => 'Module (Phpro_Translate)', 'store_name' => 'USA Store')
	    					);
	    $this->assertEquals($expectedResult, $model->search('theverybest',false, 'all', null, 0, false));
	    $expectedResult = array();
	    $this->assertEquals($expectedResult, $model->search('Phpro', false, 'all', null, 0, false));
	}
	
	/**
	 * @test
	 * @loadFixture stores.yaml
	 */
	public function searchValueInModuleInMultiStoreSetupWhenStoreIdIsSpecified()
	{
		$model 			= $this->_getTranslateModelMock();
	    $expectedResult = array('Phpro' => array('translate' => 'theverybest', 'source' => 'Module (Phpro_Translate)', 'store_name' => ''));
	    $this->assertEquals($expectedResult, $model->search('theverybest',false, 'all', null, 2, false));
	    $expectedResult = array();
	    $this->assertEquals($expectedResult, $model->search('Phpro', false, 'all', null, 2, false));
	}
	
	private function _getTranslateModelMock()
	{
		$this->_createModuleTranslationFile();
		$ModuleConfig 	= array("Phpro_Translate" => new ModuleConfig());
		$model 			= $this->getModelMock('translate/translator', array('_loadAdvancedThemeTranslation', '_getModuleConfig', '_getModuleFilePath', '_fetchStoreConfigLocale'));
        $model->expects($this->any())
             ->method('_getModuleConfig')
             ->will($this->returnValue($ModuleConfig));
        $model->expects($this->any())
             ->method('_loadAdvancedThemeTranslation')
             ->will($this->returnValue(null));
        $model->expects($this->any())
             ->method('_getModuleFilePath')
             ->will($this->returnValue($this->_getModuleTranslationFilePath()));
        $model->expects($this->any())
             ->method('_fetchStoreConfigLocale')
             ->will($this->returnValue($this::MODULE_TRANSLATION_LOCALE));
             
        return $model;
	}
	
	private function _createModuleTranslationFile()
	{
		$this->_deleteModuleTranslationFile();
		if(!(file_exists($this->_getLocaleDirectoryPath()) && is_dir($this->_getLocaleDirectoryPath()))){
			if(!mkdir($this->_getLocaleDirectoryPath())){
			  $this->markTestSkipped("Insufficient right to remove directory [' . $this->_getLocaleDirectoryPath() . ']. Run tests as sudo or as user with sufficient rights");
			}
		}
		$file = fopen($this->_getModuleTranslationFilePath(), 'w');
		if(!$file){
			$this->markTestSkipped("Not sufficient right. Run tests as sudo or as user with sufficient rights");
		}
		$stringData = '"Phpro", "theverybest"';
		fwrite($file, $stringData);
		fclose($file);
	}
	
	private function _getLocaleDirectoryPath()
	{
		return Mage::getBaseDir('locale') . $this::MODULE_TRANSLATION_PATH;
	}
	
	private function _getModuleTranslationFilePath()
	{
		return $this->_getLocaleDirectoryPath() . $this::MODULE_TRANSLATION_FILENAME;
	}
	
	private function _deleteModuleTranslationFile()
	{
		if(file_exists($this->_getModuleTranslationFilePath())){
			unlink($this->_getModuleTranslationFilePath());
		}
		if(file_exists($this->_getLocaleDirectoryPath())){
			rmdir($this->_getLocaleDirectoryPath());
		}
	}
}
class  ModuleConfig{

	public function asArray(){
		return array('files' =>array('Phpro_Translate.csv'));
	}
	
}