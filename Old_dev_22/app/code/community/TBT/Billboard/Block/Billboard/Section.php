<?php

class TBT_Billboard_Block_Billboard_Section extends Mage_Adminhtml_Block_Template
{
	protected $_divClass = '';
	
    public function __construct()
    {
        parent::__construct();
        
        $this->_controller = 'tbtbillboard';
        $this->_blockGroup = 'tbtbillboard';
        $this->setTemplate("tbtbillboard/billboard/section.phtml");
    }
    
    protected function getHeading()
    {
        return $this->getData('heading');
    }
    
    protected function getContent()
    {
        return $this->getData('content');
    }
    
    protected function getImagePath()
    {
        return $this->getData('imagePath');
    }
	
	public function setDivClass($divClass)
	{
		$this->_divClass = $divClass;
		return $this;
	}
	
	public function addDivClass($divClass)
	{
		if (!is_array($this->_divClass)) {
			$this->_divClass = array($this->_divClass);
		}
		
		$this->_divClass[] = $divClass;
		
		return $this;
	}
	
	public function getDivClass()
	{
		$divClass = $this->_divClass;
		if (is_array($divClass)) {
			$divClass = implode(' ', $divClass);
		}
		
		return $divClass;
	}
}
