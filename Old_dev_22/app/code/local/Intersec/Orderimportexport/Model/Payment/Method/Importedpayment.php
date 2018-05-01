<?php
/**
 * Importedpayment.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *

 * @category   Orders
 * @package    Importedpayment
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 

class Intersec_Orderimportexport_Model_Payment_Method_Importedpayment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'imported';

    protected $_infoBlockType = 'intersec_orderimportexport/importedpayment';

    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $this->getInfoInstance()->setAdditionalInformation('method',$data['additional_information']);
        return $this;
    }
}