<?php

class TBT_Rewards_Model_Mysql4_Transfer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	
	protected $didSelectCustomerName = false;
	protected $didSelectCurrency = false;
	protected $_excludeTransferReferences = false;
	protected $_transferReferencesAdded = false;
	
	public function _construct() {
		$this->_init ( 'rewards/transfer' );
	}
	

	/**
     * Add all the references linked with the transfers.
     * This will also include multiple references associated with the same transfer 
     * and might cause transferes to be listed more than once.
     * 
     * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
     */
    public function addAllReferences() {
        $this->_addTransferReferences();
        return $this;
    }

    /**
     * Used to exclude joining the collection with the transfer references table which is done by default on this collection.
     * Call excludeTransferReferences() before loading the collection to exclude the join.
     * 
     * @return this
     */
    public function excludeTransferReferences(){
    	//@mhadianfard -a 1/12/11:
    	$this->_excludeTransferReferences = true;
    	return $this; 	
    }
    
    /**
     * 
     * 
     * (overrides parent method)
     */
    public function _initSelect () {
        parent::_initSelect();
        return $this;
    }
    
    
    public function getIterator(){
    	
    	//@mhadianfard -a 19/12/11: Magento 1.4.0.1 doesn't call _beforeLoad()
    	if (!Mage::helper ( 'rewards' )->isBaseMageVersionAtLeast ( '1.4.2.0' )){
    		if (!$this->_excludeTransferReferences){
    			// Add a simplified version of the transfer references (1 reference per 1 transfer)
    			$this->_addTransferReferences(  true  );
    		}    		
    	}
    	
    	return parent::getIterator();
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();

        $hasFilterOnReferenceTable = false;
        $whereFilters = $this->getSelect()->getPart(Zend_Db_Select::WHERE);
        foreach ($whereFilters as $filter) {
            $pos = strpos($filter, 'reference');
            if ($pos !== false) {
                $hasFilterOnReferenceTable = true;
                break;
            }
        }

        // hack: unless there's a filter on the reference table, we should remove any joins for the COUNT sql
        // TODO: any scenarios that require a filter on the reference table should use a transfer_reference
        //   collection and join onto the transfer table, NOT the other way around (which is what we're doing)
        if (!$hasFilterOnReferenceTable && !$this->didSelectCustomerName) {
            $mainTable = Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.4.1.1')
                ? $this->getMainTable()
                : $this->getResource()->getMainTable();

            // reset any joins that are on this query
            $countSelect->reset(Zend_Db_Select::FROM);
            $countSelect->from(array('main_table' => $mainTable));
        }

        $countSelect->reset(Zend_Db_Select::GROUP);

        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns('COUNT(*)');

        return $countSelect;
    }
    
    /**
     *
     *
     * (overrides parent method)
     */   
    protected function _beforeLoad(){
    	parent::_beforeLoad();
    	
    	if (!$this->_excludeTransferReferences){
    		// Add a simplified version of the transfer references (1 reference per 1 transfer)
    		$this->_addTransferReferences(  true  );
    	}
    	    	
    	return $this;
    }
    
    /**
     * Adds transfer references to this current collection.  By default
     * adds all the transfer referneces, but you can pass a subquery into the $references 
     * parameter to only add specific references.
     * @param bool $sourceReferencesOnly    If true adds ONLY transfer source references, else all references
     */
    protected function _addTransferReferences($sourceReferencesOnly = false) {
    	if ($this->_transferReferencesAdded) return $this;

        $this->getSelect()->joinLeft(
            array('reference_table' => $this->getTable('transfer_reference') ), 
        	'main_table.rewards_transfer_id = reference_table.rewards_transfer_id'.
            ($sourceReferencesOnly
                ? ' AND main_table.source_reference_id = reference_table.rewards_transfer_reference_id'
                : ''),
            array(
            	'rewards_transfer_reference_id' => 'rewards_transfer_reference_id', 
            	'reference_type' => "reference_table.reference_type", 
            	'reference_id' => "reference_table.reference_id", 
            	'transfer_id' => "reference_table.rewards_transfer_id"
            )
        );
        
        // TODO: this would break many other collection queries, but without it we don't support multi-reference
        //$this->getSelect()->group('main_table.rewards_transfer_id');
        
        $this->_transferReferencesAdded = true;
        
        return $this;
    }
	
	/**
	 * Also select the rules for the collection
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function addRules() {
		
		$this->_addTransferReferences();		//@mhadianfard -a 1/12/11: make sure we're adding in transfer references before we do the rest		
 
		$alias = 'rule_name';
		$this->getSelect ()->joinLeft ( array ('salesrules' => $this->getTable ( 'salesrule/rule' ) ), 'reference_table.rule_id = salesrules.rule_id', array ('rule_id' => "reference_table.rule_id", 'salesrule_name' => "salesrules.name" ) );
		$this->getSelect ()->joinLeft ( array ('catalogrules' => $this->getTable ( 'catalogrule/rule' ) ), 'reference_table.rule_id = catalogrules.rule_id', array ('catalogrule_name' => "catalogrules.name" ) );
		
		//die("<PRE>".$this->getSelect()->__toString(). "</PRE>"); // ,
		

		/*
          $this->_joinFields[$alias] = array(
          'table' => false,
          'field' => $expr
          ); */
		return $this;
	}
	
	/**
	 *
	 * @return TBT_Rewards_Model_Transfer_Reference
	 */
	private function _getRTModel() {
		return Mage::getSingleton ( 'rewards/transfer_reference' );
	}
	
	/**
	 * Adds customer info to select
	 *
	 * @return  TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectCurrency() {
		if (! $this->didSelectCurrency) {
			$this->getSelect ()->joinLeft ( array ('currency_table' => $this->getTable ( 'currency' ) ), 'currency_table.rewards_currency_id=main_table.currency_id', array ('currency' => 'caption' ) );
			$this->didSelectCurrency = true;
		}
		return $this;
	}
	
	/**
	 * Add Filter by store
	 * @deprecated not supported in current stable version
	 *
	 * @param int|Mage_Core_Model_Store $store
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function addStoreFilter($store) {
		// TODO WDCA - integral to implementing multi-store capability
		//		if (!Mage::app()->isSingleStoreMode()) {
		//			if ($store instanceof Mage_Core_Model_Store) {
		//				$store = $store->getId();
		//			}
		//
		//			$this->getSelect()->join(
		//				array('store_currency_table' => $this->getTable('store_currency')),
		//				'main_table.currency_id = store_currency_table.currency_id',
		//				array()
		//			)
		//          ->where('store_currency_table.store_id', array('in' => array(0, $store)));
		//          return $this;
		//      }
		return $this;
	}
	
	/**
	 * Add Filter by store
	 *
	 * @param int|Mage_Core_Model_Store $store
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function addFullNameFilter($store) {
		if (! Mage::app ()->isSingleStoreMode ()) {
			if ($store instanceof Mage_Core_Model_Store) {
				$store = array ($store->getId () );
			}
			
			$this->getSelect ()->join ( array ('store_currency_table' => $this->getTable ( 'store_currency' ) ), 'main_table.currency_id = store_currency_table.currency_id', array () )->where ( 'store_currency_table.store_id in (?)', array (0, $store ) );
			
			return $this;
		}
		return $this;
	}
	
	/**
	 * Adds customer info to select
	 *
	 * @return  TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectCustomerName() {
		if (! $this->didSelectCustomerName) {
			/* @var $customer TBT_Rewards_Model_Customer */
			$customer = Mage::getModel ( 'rewards/customer' );
			$firstname = $customer->getAttribute ( 'firstname' );
			$lastname = $customer->getAttribute ( 'lastname' );
			
			//        $customersCollection = Mage::getModel('customer/customer')->getCollection();
			//        /* @var $customersCollection Mage_Customer_Model_Entity_Customer_Collection */
			//        $firstname = $customersCollection->getAttribute('firstname');
			//        $lastname  = $customersCollection->getAttribute('lastname');
			

			$this->getSelect ()->joinLeft ( array ('customer_lastname_table' => $lastname->getBackend ()->getTable () ), 'customer_lastname_table.entity_id=main_table.customer_id
                 AND customer_lastname_table.attribute_id = ' . ( int ) $lastname->getAttributeId () . '
                 ', array ('customer_lastname' => 'value' ) )->joinLeft ( array ('customer_firstname_table' => $firstname->getBackend ()->getTable () ), 'customer_firstname_table.entity_id=main_table.customer_id
                 AND customer_firstname_table.attribute_id = ' . ( int ) $firstname->getAttributeId () . '
                 ', array ('customer_firstname' => 'value' ) );
			$this->didSelectCustomerName = true;
		}
		return $this;
	}
	
	/**
	 * Adds the full customer name to the query.
	 *
	 * @param string|$alias What to name the column
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectFullCustomerName($alias = 'fullname') {
		$this->selectCustomerName ();
		
		$fields = array ();
		$fields ['firstname'] = 'firstname';
		$fields ['lastname'] = 'firstname';
		
		$expr = 'CONCAT(' . (isset ( $fields ['prefix'] ) ? 'IF({{prefix}} IS NOT NULL AND {{prefix}} != "", CONCAT({{prefix}}," "), ""),' : '') . '{{firstname}}' . (isset ( $fields ['middlename'] ) ? ',IF({{middlename}} IS NOT NULL AND {{middlename}} != "", CONCAT(" ",{{middlename}}), "")' : '') . '," ",{{lastname}}' . (isset ( $fields ['suffix'] ) ? ',IF({{suffix}} IS NOT NULL AND {{suffix}} != "", CONCAT(" ",{{suffix}}), "")' : '') . ')';
		
		$expr = str_replace ( "{{firstname}}", "customer_firstname_table.value", $expr );
		$expr = str_replace ( "{{lastname}}", "customer_lastname_table.value", $expr );
		
		$fullExpression = $expr;
		
		$this->getSelect ()->from ( null, array ($alias => $fullExpression ) );
		
		$this->_joinFields [$alias] = array ('table' => false, 'field' => $fullExpression );
		return $this;
	}
	
	/**
	 * Adds the full customer name to the query.
	 *
	 * @param string|$alias What to name the column
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function addFullCustomerNameFilter($filter) {
		$this->selectCustomerName ();
		
		$fields = array ();
		$fields ['firstname'] = 'firstname';
		$fields ['lastname'] = 'firstname';
		
		$expr = 'CONCAT(' . (isset ( $fields ['prefix'] ) ? 'IF({{prefix}} IS NOT NULL AND {{prefix}} != "", CONCAT({{prefix}}," "), ""),' : '') . '{{firstname}}' . (isset ( $fields ['middlename'] ) ? ',IF({{middlename}} IS NOT NULL AND {{middlename}} != "", CONCAT(" ",{{middlename}}), "")' : '') . '," ",{{lastname}}' . (isset ( $fields ['suffix'] ) ? ',IF({{suffix}} IS NOT NULL AND {{suffix}} != "", CONCAT(" ",{{suffix}}), "")' : '') . ')';
		
		$expr = str_replace ( "{{firstname}}", "customer_firstname_table.value", $expr );
		$expr = str_replace ( "{{lastname}}", "customer_lastname_table.value", $expr );
		
		$fullExpression = $expr;
		//$this->getSelect()->where($fullExpression, array('LIKE' => "%".$filter));
		

		return $this;
	}
	
	/**
	 * Adds the full customer name to the query.
	 *
	 * @param string|$alias What to name the column
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectPointsCaption($alias = 'points') {
		$this->selectCurrency ();
		
		$expr = 'CONCAT({{quantity}}, \' \', {{currency_caption}})';
		
		$expr = str_replace ( "{{currency_caption}}", "currency_table.caption", $expr );
		$expr = str_replace ( "{{quantity}}", "main_table.quantity", $expr );
		
		$fullExpression = $expr;
		
		$this->getSelect ()->from ( null, array ($alias => $fullExpression ) );
		
		$this->_joinFields [$alias] = array ('table' => false, 'field' => $fullExpression );
		return $this;
	}
	
	/**
	 * Only fetches points distributon types
	 *
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectOnlyDistributions() {
		$reasons = Mage::getSingleton ( 'rewards/transfer_reason' )->getDistributionReasonIds ();
		$this->getSelect ()->where ( 'main_table.reason_id IN (?)', array (0, $reasons ) )->order ( 'main_table.creation_ts DESC' );
		
		return $this;
	}
	
	/**
	 * Only fetches point redemption types
	 *
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectOnlyRedemptions() {
		$reasons = Mage::getSingleton ( 'rewards/transfer_reason' )->getRedemptionReasonIds ();
		$this->getSelect ()->where ( 'main_table.reason_id IN (?)', array (0, $reasons ) )->order ( 'main_table.creation_ts DESC' );
		
		return $this;
	}
	
	/**
	 * Only Fetches non redemption and non distribution types
	 *
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectOnlyOtherTransfers() {
		$reasons = Mage::getSingleton ( 'rewards/transfer_reason' )->getOtherReasonIds ();
		$this->getSelect ()->where ( 'main_table.reason_id IN (?)', array (0, $reasons ) )->order ( 'main_table.creation_ts DESC' );
		
		return $this;
	}
	
	/**
	 * Fetches only transfers that give points to the customer
	 *
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectOnlyPosTransfers() {
		$this->addFieldToFilter ( 'quantity', array ('gt' => 0 ) );
		return $this;
	}
	
	/**
	 * Fetches only transfers that deduct points from the customer
	 *
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 */
	public function selectOnlyNegTransfers() {
		$this->addFieldToFilter ( 'quantity', array ('lt' => 0 ) );
		return $this;
	}
	
	public function selectOnlyActive() {
		$countableStatusIds = Mage::getSingleton ( 'rewards/transfer_status' )->getCountableStatusIds ();
		$this->getSelect ()->where ( 'main_table.status IN (?)', $countableStatusIds );
		
		return $this;
	}
	
	/**
	 * Filters transfers with a CANCELLED status out of the SQL query
	 * @return self
	 */
	public function excludeCancelledTransfers()
	{
		$this->addFieldToFilter('status', array('neq' => TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED));
		return $this;
	}
	
	/**
	 * Sums up the points by currency and grouped again by customer.
	 *
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection 
	 */
	public function groupByCustomers() {
		$this->selectCurrency ();
		
		$this->getSelect ()->group ( 'main_table.customer_id' );
		$this->sumPoints ();
		$this->getSelect ()->from ( null, array ("points" => "CONCAT(SUM(main_table.quantity), ' ', currency_table.caption)" ) );
		$this->getSelect ()->from ( null, array ("last_changed_ts" => "MAX(main_table.creation_ts)" ) );
		
		return $this;
	}
	
	public function groupByCurrency() {
		return $this->sumPoints ();
	}
	
	/**
	 * Sums up the points in the collection as the "points_count" field for
	 * each currency.
	 * <b>Please use the 'points_count' field instead of the quantity field</b>
	 * 
	 * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
	 *
	 */
	public function sumPoints() {
		$this->getSelect ()->group ( 'main_table.currency_id' );
		$this->getSelect ()->from ( null, array ("points_count" => "SUM(main_table.quantity)" ) );
		$this->addExpressionFieldToSelect('transfer_ids', "GROUP_CONCAT(main_table.rewards_transfer_id)", array());
		return $this;
	}
	
	/**
     * Add attribute expression (SUM, COUNT, etc)
     * Example: ('sub_total', 'SUM({{attribute}})', 'revenue')
     * Example: ('sub_total', 'SUM({{revenue}})', 'revenue')
     * For some functions like SUM use groupByAttribute.
     *
     * @param string $alias
     * @param string $expression
     * @param array $fields
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function addExpressionFieldToSelect($alias, $expression, $fields)
    {
        // validate alias
        if (!is_array($fields)) {
            $fields = array($fields=>$fields);
        }

        $fullExpression = $expression;
        foreach ($fields as $fieldKey=>$fieldItem) {
            $fullExpression = str_replace('{{' . $fieldKey . '}}', $fieldItem, $fullExpression);
        }

        $this->getSelect()->columns(array($alias=>$fullExpression));

        return $this;
    }

	/**
	 * Returns a NEW collection, composed of all transfers that have revoked
	 * any of the transfers in the current collection.
	 * @return self
	 */
	public function selectRevokerTransfers()
	{
		$transferIds = $this->getTransferIds();
		$revokers = Mage::getResourceModel('rewards/transfer_collection');
		$revokers->addFieldToFilter('reference_type', array('eq' => TBT_Rewards_Model_Transfer_Reference::REFERENCE_TRANSFER))
			->addFieldToFilter('reference_id', array('in' => $transferIds));
		return $revokers;
	}
	
	/**
	 * Returns an array of ID's from this collection if it has more than one
	 * item in it or if it has a single item which doesn't have a transfer_ids
	 * value.  The transfer_ids value is generated when sumPoints() is called
	 * since that method groups rows together, losing context of which transfers
	 * made up the sum.
	 * @return array An array of ID's from this collection (even if GROUP'd)
	 */
	public function getTransferIds()
	{
        // need this because in Magento prior to 1.4.2, in Varien_Data_Collection_Db::load()
        // there is no call to _beforeLoad() so our _beforeLoad() is not run before loading collection
        if (! Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.4.2.0')) {
            if (!$this->_excludeTransferReferences){
                // Add a simplified version of the transfer references (1 reference per 1 transfer)
                $this->_addTransferReferences(  $this->_getSingleReferenceSelect()  );
            }
        }

		if ($this->count() > 1) {
			return $this->getAllIds();
		}
		
		$firstItem = $this->getFirstItem();
		if (!$firstItem->getTransferIds()) {
			return $this->getAllIds();
		}
		
		return explode(',', $firstItem->getTransferIds());
	}
	
	/**
	 * Using the parent's getAllIds() if collection hasn't been loaded yet (since
	 * the parent version loads a new collection, composed of ONLY ID's) but
	 * reverting to the original getAllIds() which checks the current collection,
	 * if it's already been loaded.
	 * @return array An array of ID's from this collection
	 */
	public function getAllIds()
	{
		if (!$this->isLoaded()) {
			return parent::getAllIds();
		}
		
		$ids = array();
		foreach ($this->getItems() as $item) {
			$ids[] = $this->_getItemId($item);
		}
		return $ids;
	}
	
	/**
	 * True if the collection only contains zero-point transfers (for some reason)
	 * or if the summed point quantities are zero for all currencies
	 * or if the collection does not contain any transfers.
	 *
	 * @return boolean
	 */
	public function isNoPoints() {
		foreach ( $this->getItems () as $item ) {
			if (isset ( $item ['points_count'] )) {
				if ($item ['points_count'] > 0) {
					return false;
				}
			} elseif (isset ( $item ['quantity'] )) {
				if ($item ['quantity'] > 0) {
					return false;
				}
			} else {
				// should never get here...	
			}
		}
		return true;
	}
	
	/**
	 * Adds an 'absolute_quantity' alias to the query and orders by it.
	 * @param int $direction
	 */
	public function sortByAbsoluteQuantity($direction = self::SORT_ORDER_DESC)
	{
		$this->addExpressionFieldToSelect('absolute_quantity', "ABS({{quantity}})", 'quantity');
		$this->setOrder('absolute_quantity', $direction);
		return $this;
	}

    /**
     * Returns a database select object that selects all references, but limits 1 reference per points transfer
     * @deprecated use _addTransferReferences(true) to get only transfer source references
     *
     * @return Zend_Db_Select
     */
    protected function _getSingleReferenceSelect() {
        $references_table_name = $this->getTable('transfer_reference');
        $read_connection = $this->getResource()->getReadConnection();
        $single_references_select = $read_connection->select()->from($references_table_name)->group('rewards_transfer_id');

        return $single_references_select;

    }
}
