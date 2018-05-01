<?php
/**
 * 
 * @group Translate
 * @group Block
 * @group Grid
 */
class Phpro_Translate_Test_Block_Adminhtml_Grid extends EcomDev_PHPUnit_Test_Case 
{
	/**
	 * @test
	 * @loadFixture defaultsetup.yaml
	 */
	public function getGridUrl()
	{
		$className	= Mage::getConfig()->getBlockClassName('translate/adminhtml_grid');
		$block		= new $className(); 
		$this->assertEquals(Mage::getBaseUrl(), $block->getGridUrl());
	}

	/**
	 * @test
	 * @loadFixture defaultsetup.yaml
	 */
	public function getRowUrl()
	{
		$className	= Mage::getConfig()->getBlockClassName('translate/adminhtml_grid');
		$block		= new $className();
		// TODO: find out which model the row actually is
		$row = $this->getMock('Row');
        $row->expects($this->any())
             ->method('getTranslateId')
             ->will($this->returnValue(10));
		$this->assertEquals(Mage::getBaseUrl() . 'id/10/', $block->getRowUrl($row));
	}
}

class Row
{
	
	public function getTranslateId(){
		
	}
	
}