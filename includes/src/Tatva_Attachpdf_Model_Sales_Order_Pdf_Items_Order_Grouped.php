<?php
/**
 * Magento Module developed by NoStress Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@nostresscommerce.cz so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2008 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */
/**
 * Sales Order Invoice Pdf grouped items renderer
 *
 * @category   Nostress
 * @package    Nostress_Invoicetemplates
 * @author     NoStress Commerce Team <info@nostresscommerce.cz>
 */

class Tatva_Attachpdf_Model_Sales_Order_Pdf_Items_Order_Default extends Mage_Sales_Model_Order_Pdf_Items_Order_Default
{
    public function draw()
    {
        $type = $this->getItem()->getRealProductType();
        $renderer = $this->getRenderedModel()->getRenderer($type);
        $renderer->setOrder($this->getOrder());
        $renderer->setItem($this->getItem());
        $renderer->setPdf($this->getPdf());
        $renderer->setPage($this->getPage());

        $renderer->draw();
    }
}