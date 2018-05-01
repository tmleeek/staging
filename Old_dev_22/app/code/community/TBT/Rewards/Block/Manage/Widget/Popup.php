<?php

class TBT_Rewards_Block_Manage_Widget_Popup extends Mage_Adminhtml_Block_Widget
{
    protected $_title = "";
    protected $_iconUri = "";
    protected $_iconCaption = "";
    protected $_popupContent = "";
    protected $_preJavaScript = array();
    protected $_postJavaScript = array();
    protected $_buttons = array();
    
    protected function _construct()
    {
        parent::_construct();
        
        $this->setTemplate('rewards/widget/popup.phtml')
            ->setTitle($this->__("Sweet Tooth"))
            ->setIconType('SEVERITY_NOTICE')
            ->setIconCaption($this->__("notice"));
        
        return $this;
    }
    
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }
    
    public function setIconType($iconType)
    {
        $this->setIconUri($this->_generateIconUri($iconType));
        return $this;
    }
    
    public function setIconUri($iconUri)
    {
        $this->_iconUri = $iconUri;
        return $this;
    }
    
    public function getIconUri()
    {
        return $this->_iconUri;
    }
    
    public function setIconCaption($iconCaption)
    {
        $this->_iconCaption = $iconCaption;
        return $this;
    }
    
    public function getIconCaption()
    {
        return $this->_iconCaption;
    }
    
    public function setPopupContent($content)
    {
        $this->_popupContent = $content;
        return $this;
    }
    
    public function getPopupContent()
    {
        return $this->_popupContent;
    }
    
    public function setPreJavaScript($preJavaScript)
    {
        $this->_preJavaScript = $preJavaScript;
        return $this;
    }
    
    public function addPreJavaScript($javascript)
    {
        if (!is_array($this->_preJavaScript)) {
            $this->_preJavaScript = array();
        }
        $this->_preJavaScript[] = $javascript;
        return $this;
    }
    
    public function getPreJavaScript()
    {
        if (!empty($this->_preJavaScript) && is_array($this->_preJavaScript)) {
            return "<script type='text/javascript'>" . implode("\n", $this->_preJavaScript) . "</script>";
        }
        return "";
    }
    
    public function setPostJavaScript($postJavaScript)
    {
        $this->_postJavaScript = $postJavaScript;
        return $this;
    }
    
    public function addPostJavaScript($javascript)
    {
        if (!is_array($this->_postJavaScript)) {
            $this->_postJavaScript = array();
        }
        $this->_postJavaScript[] = $javascript;
        return $this;
    }
    
    public function getPostJavaScript()
    {
        if (!empty($this->_postJavaScript) && is_array($this->_postJavaScript)) {
            return "<script type='text/javascript'>" . implode("\n", $this->_postJavaScript) . "</script>";
        }
        return "";
    }
    
    public function setButtons($buttons)
    {
        $this->_buttons = $buttons;
        return $this;
    }
    
    public function addButton($button)
    {
        $this->_buttons[] = $button;
        return $this;
    }
    
    public function getButtons()
    {
        return $this->_buttons;
    }
    
    public function getButtonBar()
    {
        $buttonBar = "";
        $buttonBar = implode(" ", $this->getButtons());
        return $buttonBar;
    }
    
    protected function _generateIconUri($iconType)
    {
        $uri = '';
        if (Mage::app()->getFrontController()->getRequest()->isSecure()) {
            $uri = 'https://';
        } else {
            $uri = 'http://';
        }
        
        $uri .= sprintf(
            Mage::getStoreConfig('system/adminnotification/severity_icons_url'),
            Mage::getVersion(),
            $iconType
        );
        
        return $uri;
    }
}
