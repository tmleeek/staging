<?php

/**
 * Class MDN_Mpm_Block_Rules_Tabs
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct()
    {

        parent::__construct();
        $this->setId('mpm_rules_tab');
        $this->setDestElementId('smartprice_rules_tab_content');
        $this->setTitle($this->__('Types'));

    }

    /**
     * @return mixed
     */
    protected function _beforeToHtml()
    {

        $types = array('cost', 'cost_shipping', 'additional_cost', 'commission', 'tax_rate', 'behavior', 'margin', 'adjustment', 'enable', 'min_price', 'max_price', 'price_without_competitor', 'shipping_price', 'final_price');
        $rules = Mage::getSingleton('Mpm/System_Config_RuleTypes')->getAllOptions();

        foreach($types as $type){

            foreach($rules as $ruleType){

                if($ruleType['value'] == $type){

                    $block = $this->getLayout()->createBlock('Mpm/Rules_Grid');
                    $block->setRuleType($ruleType['value']);

                    $this->addTab(
                        $ruleType['value'],
                        array(
                            'label' => $ruleType['label'].' ('.count(Mage::helper('Mpm/Carl')->getClientRuleByType($ruleType['value'])).')',
                            'content' => $block->toHtml()
                        )
                    );

                }

            }

        }

        return parent::_beforeToHtml();
    }

}