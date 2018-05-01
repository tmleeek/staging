<?php

class TBT_Rewards_Block_Widget_Grid_Container extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_blockGroup = 'empty';

    /**
     * @var string
     */
    protected $_controller = 'empty';

    /**
     * @var string
     */
    protected $_headerText = 'Container Widget Header';

    protected function _construct()
    {
        parent::_construct();
        //$this->setTemplate('widget/grid/container.phtml');
        return $this;
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_grid',
                $this->_controller . '.grid')->setSaveParametersInSession(true)
        );
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return $this->_headerText;
    }
}
