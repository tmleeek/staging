<?php

class TBT_Billboard_Block_Billboard_Section_Continuelink extends TBT_Billboard_Block_Billboard_Section
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_controller = 'tbtbillboard';
        $this->_blockGroup = 'tbtbillboard';
        $this->setTemplate("tbtbillboard/billboard/section/continuelink.phtml");
    }
    
    protected function _beforeToHtml()
    {
        $this->setData('heading', "<a href='{$this->getLayout()->getBlock('billboard')->getContinueUrl()}'>" .
            $this->__("Click here to continue") . "</a>");
        $this->setData('content', null);
        $this->setData('imagePath', null);
    }
}
