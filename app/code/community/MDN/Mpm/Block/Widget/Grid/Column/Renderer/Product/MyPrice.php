<?php

/**
 * Class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_MyPrice
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_MyPrice extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * @param \Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {

        $debug = json_decode($row->getdebug(), true);
        $currency = Mage::helper('Mpm/Pricing')->getCurrency($row->channel);
        return Mage::Helper('Mpm/FinalPrice')->getValue($debug, $currency);

    }

}