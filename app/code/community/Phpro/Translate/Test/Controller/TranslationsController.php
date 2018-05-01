<?php
/**
 * @group Translate
 * @group TranslationsController
 */
class Phpro_Translate_Test_Controller_TranslationsController extends EcomDev_PHPUnit_Test_Case_Controller
{
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 * @loadFixture admin_user.yaml
	 * @doNotIndexAll
	 */
	public function index()
	{
		$this->_prepareAdminLogin();

		$this->dispatch('translate/translations/index', array("_store" => "admin"));

		$this->assertLayoutHandleLoaded('translate_translations_index');
		$this->assertLayoutBlockCreated('left');
		$this->assertLayoutBlockRendered('content');
		$this->assertRequestRoute('translate/translations/index');
		$this->assertRequestNotForwarded();
		$this->assertNotRedirect();
		$this->assertLayoutRendered();
		$html = $this->getResponse()->getOutputBody();
		
		$formMatcher = array (
						'tag' => 'form',
						'id'  => 'search_form' 
					   );
					   
		$messages = array(
						'0'  => 'link with id translate_search_string exists',
						'1'  => 'link with id translate_list_untranslated  exists',
						'2'  => 'Search form',
						'3'  => 'Search field',
						'4'  => 'Case Checkbox',
						'5'  => 'Modules select',
						'6'  => 'interface select',
						'7'  => 'store_id select',
						'8'  => 'submit button',
						'9'  => 'div #results',
						'10' => 'div #translate_list_untranslated_content',
						'11' => 'table #translateGrid_table'
		
					);
		
		$matchers = array(
			// 2 links on side menu
			'0'  => array(
						'tag' => 'a',
						'id' => 'translate_search_string'
					),
			'1'  => array(
						'tag' => 'a', 
						'id'  => 'translate_list_untranslated'
					),
			// Search form
			'2'  => $formMatcher,
			'3'	 => array(
						'tag' 		 => 'input',
						'id'  		 => 'q' ,
						'attributes' => array('type' => 'text'),
						'ancestor'   => $formMatcher
					),
			'4'  => array (
						'tag'		 => 'input',
						'id'  	     => 'case' ,
						'attributes' => array('type' => 'checkbox'),
						'ancestor'   => $formMatcher
					),
			'5'  => array (
						'tag'		 => 'select',
						'id'  	     => 'modules' ,
						'ancestor'   => $formMatcher
					),
			'6'  => array (
						'tag'	   => 'select',
						'id'  	   => 'interface' ,
					    'children' => array('count'  => 2),
						'ancestor' => $formMatcher
					),
			'7'  => array (
						'tag'		 => 'select',
						'id'  	     => 'store_id' ,
						'ancestor'   => $formMatcher
					),
			'8'  => array (
						'tag'		 => 'button',
						'id'  	     => 'form_search_submit' ,
						'attributes' => array('type' => 'button'),
						'ancestor'   => $formMatcher
					),
			'9'  => array(
						'tag' => 'div',
						'id'  => 'result'
					),
			'10' => array(
						'tag' => 'div',
						'id'  => 'translate_list_untranslated_content'
					),
			'11' => array(
						'tag' => 'table',
						'id'  => 'translateGrid_table',
						'child' => array('tag' => 'tbody')
					)
		);
		$this->_assertHtml($matchers, $html, $messages);
	}
	
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 * @loadFixture admin_user.yaml
	 * @doNotIndexAll
	 */
	public function search(){
		$this->_prepareAdminLogin();
		
		// prepare the request
		$request = $this->getRequest();
		$request->setParam("q", "findme");
		$request->setParam("modules", "all");
		$request->setParam("interface", "frontend");
		$request->setParam("store", "0");
		
		// Mock the translate object so we controll the output
		$translateMock = $this->getModelMock('translate/translator', array('search'));
        $translateMock->expects($this->once())
             ->method('search')
             ->will($this->returnValue(array("Size" => array("translate" => "differentString", "source" => "database"))));
        $this->replaceByMock('model', 'translate/translator', $translateMock);
		
		$this->dispatch('translate/translations/search', array("_store" => "admin"));
		$this->assertLayoutHandleLoaded('translate_translations_search');
		$this->assertRequestRoute('translate/translations/search');
		$this->assertRequestNotForwarded();
		$this->assertNotRedirect();
		
		$json = Zend_Json::decode($this->getResponse()->getOutputBody());
		$this->assertArrayHasKey('records', $json);
		$messages = array(
						'0' => 'div.grid',
						'1' => 'table.data has a tbody tag',
						'2' => 'td with content 1',
						'3' => 'td with content Size',
						'4' => 'td with content database',
						'5' => 'td with content differentString',
						'6' => 'link to edit'
					);
		
		$gridTable =  array(
								'tag' 		  => 'table',
								'attributes'  => array('class' => 'data'),
								'child' 	  => array("tag" => "tbody")
							);
		$matchers  = array(
					'0'  => array(
								'tag' => 'div',
								'attributes'  => array('class' => 'grid')
							),
					'1'  => $gridTable,
					'2'  => array(
								'tag' 		  => 'td',
								'ancestor'    => array('tag' => 'tbody', 'ancestor' => $gridTable),
								'content'	  => '1'
							),
					'3'  => array(
								'tag' 		  => 'td',
								'ancestor'    => array('tag' => 'tbody', 'ancestor' => $gridTable),
								'content'	  => 'Size'
							),
					'4'  => array(
								'tag' 		  => 'td',
								'ancestor'    => array('tag' => 'tbody', 'ancestor' => $gridTable),
								'content'	  => 'database'
							),
					'5'  => array(
								'tag' 		  => 'td',
								'ancestor'    => array('tag' => 'tbody', 'ancestor' => $gridTable),
								'content'	  => 'differentString'
							),
					'6'  => array(
								'tag'		  => 'a',
								'ancestor'    => array('tag' => 'tbody', 'ancestor' => $gridTable),
								'content'	  => 'Edit' 
							)
					);
		$this->_assertHtml($matchers, $json['records'], $messages);
		
	}
	
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 * @loadFixture admin_user.yaml
	 * @loadFixture phpro_translate.yaml
	 * @doNotIndexAll
	 */
	public function edit(){
		
		$this->_prepareAdminLogin();
		
		// prepare the request
		$request = $this->getRequest();
		$request->setParam("id", "1");
		
		$this->dispatch('translate/translations/edit', array("_store" => "admin"));
		$this->assertLayoutHandleLoaded('translate_translations_edit');
		$this->assertRequestRoute('translate/translations/edit');
		$this->assertRequestNotForwarded();
		$this->assertNotRedirect();
		$html = $this->getResponse()->getOutputBody();
		
		$messages = array(
						'0' => 'form',
						'1' => 'tr for Module',
						'2' => 'tr for original',
						'3' => 'tr for string',
						'4' => 'td with content database',
						'5' => 'td with content differentString',
						'6' => 'link to edit'
					);
		$matchers  = array(
					'0'  => array(
								'tag'		  => 'form',
								'id'		  => 'edit_form',
								'descendant'  => array('tag' => 'h4', 'content' => 'Item information')
							),
					'1'  => array(
								'tag'		  => 'td',
								'content'  	  =>  'Mage_Page::Empty',
							    'ancestor'	  => array('tag' => 'tr', 'descendant' => array('tag' => 'label', 'content' => 'Module'))
							),
					'2'  => array(
								'tag'		  => 'td',
								'content'  	  =>  'Empty',
								'ancestor'	  => array('tag' => 'tr', 'descendant' => array('tag' => 'label', 'content' => 'Original'))
							),
					'3'  => array(
								'tag'		  => 'input',
								'id'		  => 'string',
								'attributes'  => array('value' => 'Empty'),
								'ancestor'	  => array('tag' => 'tr', 'descendant' => array('tag' => 'label', 'content' => 'String *'))
							),
					'4'  => array(
								'tag'		  => 'select',
								'id'		  => 'locale',
//								'attributes'  => array('value' => 'en_US'),
								'ancestor'	  => array('tag' => 'tr', 'descendant' => array('tag' => 'label', 'content' => 'Locale *'))
							),
					'5'  => array(
								'tag'		  => 'td',
								'content'  	  => 'front_end',
								'ancestor'	  => array('tag' => 'tr', 'descendant' => array('tag' => 'label', 'content' => 'Interface'))
							),
					'6'  => array(
								'tag'		  => 'td',
								'content'  	  => 'Main Website',
								'ancestor'	  => array('tag' => 'tr', 'descendant' => array('tag' => 'label', 'content' => 'Store'))
							),
					'7'  => array(
								'tag'		  => 'input',
								'id'		  => 'storeview_specific',
								'attributes'  => array('type' => 'checkbox'),
								'ancestor'	  => array('tag' => 'tr', 'descendant' => array('tag' => 'label', 'content' => 'Store view specific'))
							),
					);

		$this->_assertHtml($matchers, $html, $messages);
	}
	
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 * @loadFixture admin_user.yaml
	 * @loadFixture phpro_translate.yaml
	 * @doNotIndexAll
	 */
	public function editRendersTextareaWhenStringLongerThen45Chars(){
		
		$this->_prepareAdminLogin();
		
		// prepare the request
		$request = $this->getRequest();
		$request->setParam("id", "2");
		
		$this->dispatch('translate/translations/edit', array("_store" => "admin"));
		$this->assertLayoutHandleLoaded('translate_translations_edit');
		$this->assertRequestRoute('translate/translations/edit');
		$this->assertRequestNotForwarded();
		$this->assertNotRedirect();
		$html = $this->getResponse()->getOutputBody();
		
		$messages = array(
						'0' => 'tr for string'
					);
		$matchers  = array(
					'0'  => array(
								'tag'		  => 'textarea',
								'id'		  => 'string',
								'content' 	  => 'this is a very long string to check if it renders a textarea for large strings',
								'ancestor'	  => array('tag' => 'tr', 'descendant' => array('tag' => 'label', 'content' => 'String *'))
							)
					);

		$this->_assertHtml($matchers, $html, $messages);
	}
	
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 * @loadFixture admin_user.yaml
	 * @loadFixture phpro_translate.yaml
	 * @doNotIndexAll
	 */
	public function saveWithStoreViewSpecificTrue(){
		
		$model = Mage::getModel('translate/translate')->load(1);
		if($model->getId() == ""){
			$this->markTestSkipped("Model should have been loaded by fixtures");
		}
		// prove the model still exsists
		$this->assertEquals(1, $model->getId());
		
		// prepare the request
		$request = $this->getRequest();
		$request->setParam("original_translation", "Empty");
		$request->setParam("string", "Full");
		$request->setParam("locale", "en_US");
		$request->setParam("storeid", "2");
		$request->setParam("id", "1");
		$request->setParam("storeview_specific", "1");
		
		// this mock will verify calling of resource model core/translate_string with correct parameters
		$core_translateString = $this->getModelMock('core/translate_string', array('saveTranslate'));
        $core_translateString->expects($this->once())
             ->method('saveTranslate')
             ->with($this->equalTo('Empty'), $this->equalTo('Full'), $this->equalTo('en_US'), $this->equalTo('2'))
             ->will($this->returnValue(true));
        $this->replaceByMock('resource_model', 'core/translate_string', $core_translateString); 
        
		$this->_prepareAdminLogin();
		$this->dispatch('translate/translations/save', array("_store" => "admin"));
		$this->assertRedirect();
		//$this->assertRedirectTo('translate/translations/index');
		
		// Check if the entry in phpro_translate is removed
		$model = Mage::getModel('translate/translate')->load(1);
		$this->assertEmpty($model->getId());
	}
	
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 * @loadFixture admin_user.yaml
	 * @loadFixture phpro_translate.yaml
	 * @doNotIndexAll
	 */
	public function saveWithStoreViewSpecificFalse(){
		
		$model = Mage::getModel('translate/translate')->load(1);
		if($model->getId() == ""){
			$this->markTestSkipped("Model should have been loaded by fixtures");
		}
		// prove the model still exsists
		$this->assertEquals(1, $model->getId());
		
		// prepare the request
		$request = $this->getRequest();
		$request->setParam("original_translation", "Empty");
		$request->setParam("string", "Full");
		$request->setParam("locale", "en_US");
		$request->setParam("storeid", "2");
		$request->setParam("id", "1");
		$request->setParam("storeview_specific", "0");
		// this mock will verify calling of resource model core/translate_string with correct parameters
		$core_translateString = $this->getModelMock('core/translate_string', array('saveTranslate'));
        $core_translateString->expects($this->once())
             ->method('saveTranslate')
             ->with($this->equalTo('Empty'), $this->equalTo('Full'), $this->equalTo('en_US'), $this->equalTo('0'))
             ->will($this->returnValue(true));
        $this->replaceByMock('resource_model', 'core/translate_string', $core_translateString); 
        
		$this->_prepareAdminLogin();
		$this->dispatch('translate/translations/save', array("_store" => "admin"));
		$this->assertRedirect();
		//$this->assertRedirectTo('translate/translations/index');
		
		// Check if the entry in phpro_translate is removed
		$model = Mage::getModel('translate/translate')->load(1);
		$this->assertEmpty($model->getId());
	}
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 * @loadFixture admin_user.yaml
	 * @loadFixture phpro_translate.yaml
	 * @doNotIndexAll
	 */
	public function grid()
	{
		$this->_prepareAdminLogin();
		$this->dispatch('translate/translations/grid', array("_store" => "admin"));
		$this->assertRequestRoute('translate/translations/grid');
		$this->assertRequestNotForwarded();
		$this->assertNotRedirect();
		$this->assertResponseHttpCode(200);
		
		$html = $this->getResponse()->getOutputBody();
		$messages = array(
						'0' => 'div.grid',
						'1' => 'table.data has a tbody tag',
						'2' => 'td with content 1',
						'3' => 'td with content Size',
						'4' => 'td with content database',
						'5' => 'td with content differentString',
						'6' => 'link to edit'
					);
		
		$gridTable =  array(
								'tag' 		  => 'table',
								'id'		  => 'translateGrid_table',
								'attributes'  => array('class' => 'data'),
								'child' 	  => array("tag" => "tbody")
							);
		$matchers  = array(
					'0'  => array(
								'tag' => 'div',
								'attributes'  => array('class' => 'grid')
							),
					'1'  => $gridTable,
					'2'  => array(
								'tag' 		  => 'td',
								'ancestor'    => array('tag' => 'tbody', 'ancestor' => $gridTable),
								'content'	  => '1'
							),
					'3'  => array(
								'tag' 		  => 'td',
								'ancestor'    => array('tag' => 'tbody', 'ancestor' => $gridTable),
								'content'	  => 'Mage_Page::Empty'
							),
					'4'  => array(
								'tag' 		  => 'td',
								'ancestor'    => array('tag' => 'tbody', 'ancestor' => $gridTable),
								'content'	  => 'English (United States)'
							),
					'5'  => array(
								'tag' 		  => 'a',
								'attributes'  => array('name' => 'string'),
								'ancestor' => $gridTable
							),
					'6'  => array(
								'tag' 		  => 'a',
								'attributes'  => array('name' => 'module'),
								'ancestor' => $gridTable
							),
					'7'  => array(
								'tag' 		  => 'a',
								'attributes'  => array('name' => 'store_id'),
								'ancestor' => $gridTable
							),
					'8'  => array(
								'tag' 		  => 'a',
								'attributes'  => array('name' => 'locale'),
								'ancestor' => $gridTable
							),
					'9'  => array(
								'tag' 		  => 'a',
								'attributes'  => array('name' => 'interface'),
								'ancestor' => $gridTable
							)
					);

		$this->_assertHtml($matchers, $html, $messages);
	}
	
	/**
	 * @test
	 * @loadFixture core_config_data.yaml
	 * @loadFixture admin_user.yaml
	 * @loadFixture phpro_translate.yaml
	 * @doNotIndexAll
	 */
	public function delete()
	{
		$this->_prepareAdminLogin();
		$this->dispatch('translate/translations/delete', array("_store" => "admin"));
		$this->assertResponseHttpCode(200);
		$this->markTestIncomplete('This action has no implementation yet.');
	}
	
	private function _prepareAdminLogin()
	{
		$user = Mage::getModel('admin/user');
		$user->loadByUsername("phpro");

		$adminSession = $this->getModelMock('admin/session', array('getUser'));
        $adminSession->expects($this->any())
             ->method('getUser')
             ->will($this->returnValue($user));
		$this->replaceByMock('singleton', 'admin/session', $adminSession);
	}
	
	private function _assertHtml($matchers, $html,$messages)
	{
		foreach($matchers as $key => $matcher){
			$this->assertTag($matcher, $html, "($key)" . $messages[$key]);
		}
	}
}