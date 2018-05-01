<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if ((string) Mage::getConfig()->getModuleConfig('Idev_OneStepCheckout')->active != 'true') {

    /**
     * OneStepCheckout
     * 
     * @category    Addonline
     * @package     Addonline_SoColissimo
     * @copyright   Copyright (c) 2014 Addonline
     * @author 	    Addonline (http://www.addonline.fr)
     */
    class Idev_OneStepCheckout_Helper_Checkout extends Mage_Core_Helper_Abstract
    {
    }
}

/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Helper_OneStepCheckout_Checkout extends Idev_OneStepCheckout_Helper_Checkout
{

    /* (non-PHPdoc)
     * @see Idev_OneStepCheckout_Helper_Checkout::saveShipping()
     */
    public function saveShipping($data, $customerAddressId)
    {
        $shipping_data = Mage::getSingleton('checkout/session')->getData('socolissimo_livraison_relais');
        if ($shipping_data)
            return array();
        else
            return parent::saveShipping($data, $customerAddressId);
    }
}
