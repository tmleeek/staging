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


class Mirasvit_EmailReport_IndexController extends Mage_Core_Controller_Front_Action
{
    public function openAction()
    {
        $queueKey = $this->getRequest()->getParam('emqo');

        $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($queueKey);

        if ($queue && $queue->getId()) {
            $helper = Mage::helper('core/http');

            $open = Mage::getModel('emailreport/open')
                ->setQueueId($queue->getId())
                ->setRemoteAddr($helper->getRemoteAddr(true))
                ->save();
        }

        echo "\x47\x49\x46\x38\x37\x61\x1\x0\x1\x0\x80\x0\x0\xfc\x6a\x6c\x0\x0\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x44\x1\x0\x3b";
    }
}