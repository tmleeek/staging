<?php
/**
 * 
 *
 */
class MDN_Organizer_Model_Task  extends Mage_Core_Model_Abstract
{
	
	/*****************************************************************************************************************************
	* ***************************************************************************************************************************
	* Constructeur
	*
	*/
	public function _construct()
	{
		parent::_construct();
		$this->_init('Organizer/Task');
	}
	
	
	/**
	 * Retourne le lien pour acc�der � la fiche de l'entit�
	 *
	 */
	public function getEntityLink()
	{
		$link = '';
		
		switch($this->getot_entity_type())
		{
			case 'order':
				$link =  Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $this->getot_entity_id()));
				break;	
			case 'customer':
				$link =  Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $this->getot_entity_id()));
				break;	
			case 'rma':
				$link =  Mage::helper('adminhtml')->getUrl('ProductReturn/Admin/Edit', array('rma_id' => $this->getot_entity_id()));
				break;	
			case 'purchase_order':
				$link =  Mage::helper('adminhtml')->getUrl('Purchase/Orders/Edit', array('po_num' => $this->getot_entity_id()));
				break;	
			case 'product':
				$link =  Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit', array('id' => $this->getot_entity_id()));
				break;	
			case 'supplier':
				$link =  Mage::helper('adminhtml')->getUrl('Purchase/Suppliers/Edit', array('sup_id' => $this->getot_entity_id()));
				break;	
			case 'credit_memo':
				$link =  Mage::helper('adminhtml')->getUrl('adminhtml/sales_creditmemo/view', array('creditmemo_id' => $this->getot_entity_id()));
				break;	
		}
		
		return $link;
	}
	
	/**
	 * M�thode pour savoir si une tache est en retard
	 *
	 */
	public function isLate()
	{
		$retour = false;
		if ($this->getot_finished() == 0)
		{
			if ($this->getot_deadline() != '')
			{
				$deadline = strtotime($this->getot_deadline());
				if ($deadline < time())
					$retour = true;
			}
		}
		return $retour;
	}
	
	/**
	 * M�thode pour savoir si la tache est termin�e
	 *
	 * @return unknown
	 */
	public function isFinished()
	{
		return ($this->getot_finished() == 1);
	}
	
	/**
	 * Return task author
	 *
	 * @return unknown
	 */
	public function getAuthor()
	{
		$authorId = $this->getot_author_user();
		return mage::getModel('admin/user')->load($authorId);
	}
	
	/**
	 * Notify Task Target
	 *
	 */
	public function notifyTarget()
	{
		//retrieve target email
		$target = mage::getModel('admin/user')->load($this->getot_target_user());
			
		$translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $templateId = Mage::getStoreConfig('organizer/notification/email_template');
        $identityId = Mage::getStoreConfig('organizer/notification/email_identity');
        
        $data = array
        	(
        		'task_link' => $this->getEntityLink(),
        		'entity_name' => $this->getot_entity_description(),
        		'author' => $this->getAuthor()->getName(),
        		'subject' => $this->getot_caption(),
        		'comments' => $this->getot_description()
        	);
        	
        //send email
        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'adminhtml', 'store'=>0))
            ->sendTransactional(
                $templateId,
                $identityId,
                $target->getemail(),
                '',
                $data,
                null,
                null);

        $translate->setTranslateInline(true);

        return $this;
	}
	
}