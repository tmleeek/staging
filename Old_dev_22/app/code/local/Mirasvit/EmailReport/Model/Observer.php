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


class Mirasvit_EmailReport_Model_Observer extends Varien_Object
{
    public function controllerActionPredispatch($observer)
    {
        if (Mage::app()->getRequest()->getParam('emqc')) {
            $queueKey = Mage::app()->getRequest()->getParam('emqc');

            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($queueKey);

            if ($queue && $queue->getId()) {
                $helper = Mage::helper('core/http');

                $open = Mage::getModel('emailreport/click')
                    ->setRemoteAddr($helper->getRemoteAddr(true))
                    ->setQueueId($queue->getId())
                    ->save();
            }
        }
    }
}