<?php

class TBT_Billboard_Block_Billboard extends Mage_Adminhtml_Block_Template
{
    protected $_sections = array();
    
    public function __construct()
    {
        parent::__construct();
        
        $this->_controller = 'tbtbillboard';
        $this->_blockGroup = 'tbtbillboard';
        $this->setTemplate("tbtbillboard/billboard.phtml");
    }
    
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        
        if ($this->hasData('sections')) {
            foreach ($this->getData('sections') as $section) {
                $block = $this->getLayout()->createBlock('tbtbillboard/billboard_section')
                    ->setData('heading', array_key_exists('heading', $section) ? $section['heading'] : null)
                    ->setData('content', array_key_exists('content', $section) ? $section['content'] : null)
                    ->setData('imagePath', array_key_exists('imagePath', $section) ? $section['imagePath'] : null);
                $this->_sections[] = $block;
            }
        }
        
        if ($this->getDisplayContinueLink()) {
            $this->_sections[] = $this->getLayout()->createBlock('tbtbillboard/billboard_section_continuelink');
        }
        
        return $this;
    }
    
    public function getContinueUrl()
    {
        $beforeForwardInfo = $this->getRequest()->getBeforeForwardInfo();
        $module = array_key_exists('module_name', $beforeForwardInfo) ?
            $beforeForwardInfo['module_name'] :
            $this->getRequest()->getRouteName();
        $controller = array_key_exists('controller_name', $beforeForwardInfo) ?
            $beforeForwardInfo['controller_name'] :
            $this->getRequest()->getControllerName();
        $action = array_key_exists('action_name', $beforeForwardInfo) ?
            $beforeForwardInfo['action_name'] :
            $this->getRequest()->getActionName();
        $params = array_key_exists('params', $beforeForwardInfo) ?
            $beforeForwardInfo['params'] : array();
        
        return $this->getUrl("{$module}/{$controller}/{$action}", $params);
    }
    
    protected function getTitle()
    {
        return $this->hasData('title') ? $this->getData('title') : "An error has occurred.";
    }
    
    protected function getLogoPath()
    {
        return $this->hasData('logoPath') ? $this->getData('logoPath') : 'images/tbtbillboard/sweet_tooth_logo_alpha.png';
    }
    
    protected function getSections()
    {
        return $this->_sections;
    }
    
    protected function getDisplayContinueLink()
    {
        return $this->hasData('displayContinueLink') ? $this->getData('displayContinueLink') : false;
    }
}
