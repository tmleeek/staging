<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailReport_Block_Adminhtml_Recipient_Trend extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $this->setTemplate('mirasvit/emailreport/recipient/trend.phtml');
    }

    public function getCollection()
    {
        return Mage::getResourceModel('emailreport/recipient_collection')->getTrendReport();
    }
}