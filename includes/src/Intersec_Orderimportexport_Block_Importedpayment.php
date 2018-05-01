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
class Intersec_Orderimportexport_Block_Importedpayment extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('orderimportexport/importedpayment.phtml');
    }

    public function toPdf()
    {
        $this->setTemplate('orderimportexport/pdf/importedpayment.phtml');
        return $this->toHtml();
    }
}