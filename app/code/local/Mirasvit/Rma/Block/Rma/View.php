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
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rma_Block_Rma_View extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $rma = $this->getRma();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('rma')->__('RMA #%s', $rma->getIncrementId()));
        }
    }

    public function getId()
    {
        return $this->getRma()->getId();
    }

    public function getRma()
    {
        return Mage::registry('current_rma');
    }

    public function getOrderUrl($orderId)
    {
        return Mage::getUrl('sales/order/view', array('order_id' => $orderId));
    }

    public function getCommentPostUrl()
    {
        return Mage::getUrl('rma/rma/savecomment');
    }

    protected $commentCollection = false;
    public function getCommentCollection()
    {
        if (!$this->commentCollection) {
            $this->commentCollection = $this->getRma()->getCommentCollection()
                ->addFieldToFilter('is_visible_in_frontend', true)
                ;
        }
        return $this->commentCollection;
    }

    public function getPrintUrl()
    {
        return Mage::getUrl('rma/rma/print', array('id' => $this->getRma()->getId()));
    }

    public function getCustomFields()
    {
        $collection = Mage::helper('rma/field')->getVisibleCustomerCollection();
        return $collection;
    }
}