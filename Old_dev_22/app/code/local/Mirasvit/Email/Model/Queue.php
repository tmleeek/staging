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


class Mirasvit_Email_Model_Queue extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING       = 'pending';
    const STATUS_DELIVERED     = 'delivered';
    const STATUS_CANCELED      = 'canceled';
    const STATUS_UNSUBSCRIBED  = 'unsubscribed';
    const STATUS_ERROR         = 'error';
    const STATUS_MISSED        = 'missed';

    protected $_args    = null;
    protected $_trigger = null;
    protected $_chain   = null;

    protected function _construct()
    {
        $this->_init('email/queue');
    }

    public function loadByUniqKeyMd5($code)
    {
        $queue = Mage::getModel('email/queue')->getCollection()
            ->addFieldToFilter('uniq_key_md5', $code)
            ->getFirstItem();

        if ($queue->getId()) {
            return Mage::getModel('email/queue')->load($queue->getId());
        }

        return false;
    }

    public function getTrigger()
    {
        if ($this->_trigger == null) {
            $this->_trigger = Mage::getModel('email/trigger')->load($this->getTriggerId());
        }

        return $this->_trigger;
    }

    public function getChain()
    {
        if ($this->_chain == null) {
            $this->_chain = Mage::getModel('email/trigger_chain')->load($this->getChainId());
        }

        return $this->_chain;
    }

    public function getTemplate()
    {
        return $this->getChain()->getTemplate();
    }

    public function getArgs()
    {
        if ($this->_args == null) {
            $this->_args = unserialize($this->getData('args_serialized'));

            $this->getTrigger()->prepareArgs($this->getUniqKey(), $this->_args);
            $this->getChain()->prepareArgs($this->getUniqKey(), $this->_args);
        }

        return $this->_args;
    }

    public function getSubject()
    {
        if ($this->getData('subject') == '') {
            $subject = $this->getTemplate()->getProcessedTemplateSubject($this->getArgs());
            $this->setData('subject', $subject);
        }

        return $this->getData('subject');
    }

    public function getContent()
    {
        if ($this->getData('content') == '') {
            $content = $this->getTemplate()->getProcessedTemplate($this->getArgs());
            $trigger = $this->getTrigger();
            $chain   = $this->getChain();

            if ($this->getTemplate()->getDesign()
                && $this->getTemplate()->getDesign()->getTemplateType() != Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_TEXT) {
                $content = Mage::helper('email')->prepareMailContent($content, $trigger, $chain, $this);
                $content = Mage::helper('emailreport')->prepareMailContent($content, $trigger, $chain, $this);
            }

            $this->setData('content', $content);
        }

        return $this->getData('content');
    }


    public function send()
    {
        if (time() - strtotime($this->getScheduledAt()) > 60 * 60 * 24 * 2) {
            $this->missed();
            return $this;
        }

        $this->getArgs();

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->_args['store_id']);

        $email     = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        if ($this->getTemplate()->getDesign()
            && $this->getTemplate()->getDesign()->getTemplateType() == Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_TEXT) {
            $email->setTemplateType(Mage_Core_Model_Template::TYPE_TEXT);
        }

        $email->setReplyTo($this->getSenderEmail());
        $email->setSenderName($this->getSenderName());
        $email->setSenderEmail($this->getSenderEmail());

        $email->setTemplateSubject($this->getSubject());
        if ($this->getTest()) {
            $email->setTemplateSubject($this->getSubject().' ['.'Test Store #'.$this->_args['store_id'].' '.microtime(true).']');
        }
        $email->setTemplateText($this->getContent());

        $recipient = $this->getRecipientEmail();
        if (Mage::getSingleton('email/config')->isSandbox()) {
            $recipient = Mage::helper('email')->determineEmails(Mage::getSingleton('email/config')->getSandboxEmail());
        }

        $copyTo = Mage::helper('email')->determineEmails($this->getTrigger()->getCopyEmail());
        foreach ($copyTo as $bcc) {
            $email->addBcc($bcc);
        }

        $result = $email->send(
            $recipient,
            $this->getRecipientName(),
            array(
                'name'    => $this->getRecipientName(),
                'email'   => $recipient,
                'subject' => $this->getSubject(),
                'message' => $this->getContent()
            )

        );

        $this->delivery();

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $result;
    }

    public function delivery()
    {
        $this->setSentAt(Mage::getSingleton('core/date')->gmtDate())
            ->setStatus(Mirasvit_Email_Model_Queue::STATUS_DELIVERED)
            ->save();
    }

    public function missed()
    {
        $this->setStatus(Mirasvit_Email_Model_Queue::STATUS_MISSED)
            ->save();
    }

    public function cancel()
    {
        $this->setStatus(Mirasvit_Email_Model_Queue::STATUS_CANCELED)
            ->save();

        return $this;
    }

    public function unsubscribe()
    {
        $this->setStatus(Mirasvit_Email_Model_Queue::STATUS_UNSUBSCRIBED)
            ->save();

        return $this;
    }

    public function reset()
    {
        $this->setStatus(Mirasvit_Email_Model_Queue::STATUS_PENDING)
            ->setSentAt(null)
            ->save();

        return $this;
    }
}