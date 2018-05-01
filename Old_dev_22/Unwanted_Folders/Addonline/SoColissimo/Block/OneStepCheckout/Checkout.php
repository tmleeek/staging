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

/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_Socolissimo_Block_OneStepCheckout_Checkout extends Idev_OneStepCheckout_Block_Checkout
{

    /* (non-PHPdoc)
     * @see Idev_OneStepCheckout_Block_Checkout::differentShippingAvailable()
     */
    public function differentShippingAvailable()
    {
        
        // dans le cas où on livre dans un relais colis SoColissimo, on se comporte comme si la livraison
        // dans une adresse différente de l'adresse de facturation n'était pas possible pour éviter d'écraser
        // l'addresse du relais colis qui a été enregistrée auparavent
        $request = Mage::app()->getRequest();
        $shippingMethod = $request->getParam('shipping_method');
        if (strpos($shippingMethod, 'socolissimo_') === 0) {
            $idRelais = $request->getParam('relais_socolissimo');
            if ($idRelais) {
                return false;
            }
        }
        return parent::differentShippingAvailable();
    }
}
