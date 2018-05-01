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


class Mirasvit_EmailReport_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $this->setTemplate('mirasvit/emailreport/dashboard.phtml');
    }

    public function getTotalSends()
    {
        return Mage::getModel('email/queue')->getCollection()
        	->addFieldToFilter('status', 'delivered')
        	->count() + 1;
    }

    public function getReadRate()
    {
        $open = Mage::getModel('emailreport/open')->getCollection();
        $open->getSelect()->group('remote_addr');
        $count = $open->count();

        return round($count / $this->getTotalSends() * 100, 2);
    }

    public function getClickRate()
    {
        $open = Mage::getModel('emailreport/click')->getCollection();
        $open->getSelect()->group('remote_addr');
        $count = $open->count();

        return round($count / $this->getTotalSends() * 100, 2);
    }

    public function getQueueStatus()
    {
        $result['pending'] = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_PENDING)
            ->count();

        $result['delivered'] = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_DELIVERED)
            ->count();

        $result['canceled'] = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_CANCELED)
            ->count();

        $result['unsubscribed'] = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_UNSUBSCRIBED)
            ->count();

        $result['error'] = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_ERROR)
            ->count();

        $result['missed'] = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_MISSED)
            ->count();

        return $result;
    }
}