<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Tabs
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct()
    {
        parent::__construct();
        $this->setId('mpm_rules_edit_tab');
        $this->setDestElementId('mpm_rules_edit_tab_content');
        $this->setTitle($this->__('Parameters'));
    }

    /**
     * @return mixed
     */
    protected function _beforeToHtml()
    {

        $tabs = array(
            'details' => array(
                'label' => $this->__('Details'),
                'active' => true
            ),
            'variables' => array(
                'label' => $this->__('Variables'),
                'active' => false
            ),
            'conditions' => array(
                'label' => $this->__('Conditions on offers'),
                'active' => false
            ),
            'perimeters' => array(
                'label' => $this->__('Conditions for products'),
                'active' => false
            )
        );

        foreach($tabs as $tabId => $tab) {
            $this->addTab(
                $tabId,
                array(
                    'label' => $tab['label'],
                    'content' => $this->getLayout()->createBlock('Mpm/Rules_Edit_Tab_'.ucfirst($tabId))->toHtml(),
                    'active' => $tab['active']
                )
            );
        }

        $this->addTab(
            'products',
            array(
                'label'   => $this->__('Product matching to condition'),
                'class' => 'ajax',
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/Mpm_Rules/productsBlock', array('rule_id' => Mage::registry('current_rule')->getId()))
            )
        );

        return parent::_beforeToHtml();
    }

}