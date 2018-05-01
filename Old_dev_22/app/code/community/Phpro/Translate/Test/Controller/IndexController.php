<?php
/**
 * @group Translate
 * @group IndexController
 */
class Phpro_Translate_Test_Controller_IndexController extends EcomDev_PHPUnit_Test_Case_Controller
{
	
	/**
	 * @test
	 * @doNotIndexAll 
	 */
	public function stringsAreSavedWhentranslationLoggingIsEnabled()
	{
		$helper = $this->getHelperMock('translate/data',array('getAdvancedLoggingStatus','getLocales' , 'translation_interfaces'));
		$helper->expects($this->any())
					 ->method("getAdvancedLoggingStatus")
			         ->will($this->returnValue(0));
		$helper->expects($this->any())
					 ->method("translation_interfaces")
			         ->will($this->returnValue("frontend,adminhtml"));	
		$helper->expects($this->any())
					 ->method("getLocales")
			         ->will($this->returnValue("en_US"));		
		$this->replaceByMock('helper', 'translate/data', $helper); 
		
		$translateMock = $this->getModelMock('translate/translator', array('_saveUnTranslatedString'));
        $translateMock->expects($this->any())
             ->method('_saveUnTranslatedString')
             ->will($this->returnValue(true));
        $this->replaceByMock('model', 'translate/translator', $translateMock);
		$this->dispatch('translate/index/index');
		$this->assertLayoutHandleLoaded('translate_index_index');
		$this->assertLayoutBlockCreated('left');
		$this->assertLayoutBlockCreated('right');
		$this->assertLayoutBlockRendered('content');
		$this->assertLayoutBlockTypeOf('left', 'core/text_list');
		$this->assertLayoutBlockNotTypeOf('left', 'core/links');
		$this->assertResponseBodyContains("hello world");
	}
	
	/**
	 * @test
	 * @doNotIndexAll
	 */
	public function NoStringsAreSavedWhentranslationLoggingIsDisabled()
	{
		$helper = $this->getHelperMock('translate/data',array('getAdvancedLoggingStatus'));
		$helper->expects($this->any())
					 ->method("getAdvancedLoggingStatus")
			         ->will($this->returnValue(0));		
		$this->replaceByMock('helper', 'translate/data', $helper); 
		
		$translateMock = $this->getModelMock('translate/translator', array('_saveUnTranslatedString'));
        $translateMock->expects($this->never())
             ->method('_saveUnTranslatedString')
             ->will($this->returnValue(null));
        $this->replaceByMock('model', 'translate/translator', $translateMock);
		$this->dispatch('translate/index/index');
		$this->assertLayoutHandleLoaded('translate_index_index');
		$this->assertLayoutBlockCreated('left');
		$this->assertLayoutBlockCreated('right');
		$this->assertLayoutBlockRendered('content');
		$this->assertLayoutBlockTypeOf('left', 'core/text_list');
		$this->assertLayoutBlockNotTypeOf('left', 'core/links');
		$this->assertResponseBodyContains("hello world");
	}
	
}