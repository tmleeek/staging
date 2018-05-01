<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AdvancedStock_Block_Product_Edit_Tabs_Stock extends Mage_Adminhtml_Block_Widget_Form
{
	private $_defaultStock = null;
	
	/**
	 * Product get/set
	 *
	 * @var unknown_type
	 */
	private $_product = null;
	public function setProduct($Product)
	{
		$this->_product = $Product;
		return $this;
	}
	public function getProduct()
	{
		return $this->_product;
	}
	
	/**
	 * Constructeur
	 *
	 */
	public function __construct()
	{
		$this->_blockGroup = 'AdvancedStock';
        $this->_objectId = 'id';
        $this->_controller = 'product';
        
        
		parent::__construct();

	    $this->setTemplate('AdvancedStock/Product/Edit/Tab/Stock.phtml');
		
	}	
	
	/**
	 * Return combobox for manage stocks
	 *
	 * @param unknown_type $name
	 */
	public function getManageStockCombo($name)
	{
		$retour = '<select name="'.$name.'" id="'.$name.'">';
		
		$selected = '';
		if ($this->productManageStocks())
			$selected = ' selected ';
		$retour .= '<option value="1" '.$selected.'>'.$this->__('Yes').'</option>';
		
		$selected = '';
		if (!$this->productManageStocks())
			$selected = ' selected ';		
		$retour .= '<option value="0" '.$selected.'>'.$this->__('No').'</option>';
		
		$retour .= '</select>';
		return $retour;
	}
	
	public function getBackordersCombo($name)
	{
		$retour = '<select name="'.$name.'" id="'.$name.'">';
		
		$array = Mage::getSingleton('cataloginventory/source_backorders')->toOptionArray();
		foreach ($array as $item)
		{
			$selected = '';
			if ($item['value'] == $this->getBackOrderValue())
				$selected = ' selected ';
			$retour .= '<option value="'.$item['value'].'" '.$selected.'>'.$item['label'].'</option>';
		}
		
		$retour .= '</select>';
		return $retour;
		
	}

        /**
         * Return true if product is not affected to all warehouses
         */
        public function hasNonAffectedWarehouses()
        {
            //get warehouses associated to product
            $associatedWarehouseIds = array();
            $stocks = mage::helper('AdvancedStock/Product_Base')->getStocks($this->getProduct()->getId());
            foreach($stocks as $stock)
            {
                    $associatedWarehouseIds[] = $stock->getstock_id();
            }
            
            $warehouses = mage::getModel('AdvancedStock/Warehouse')
                                                            ->getCollection()
                                                            ->addFieldToFilter('stock_id', array('nin' => $associatedWarehouseIds));

            return ($warehouses->getSize() > 0);
        }

	public function getAffectToWarehouseCombo($name)
	{
		$retour = '<select name="'.$name.'" id="'.$name.'">';
	
		//get warehouses associated to product
		$associatedWarehouseIds = array();
		$stocks = mage::helper('AdvancedStock/Product_Base')->getStocks($this->getProduct()->getId());
		foreach($stocks as $stock)
		{
			$associatedWarehouseIds[] = $stock->getstock_id();
		}
		
		//get non linked warehouses
		$warehouses = mage::getModel('AdvancedStock/Warehouse')
								->getCollection()
								->addFieldToFilter('stock_id', array('nin' => $associatedWarehouseIds));
		$retour .= '<option></option>';
		foreach($warehouses as $warehouse)
		{
			$retour .= '<option value="'.$warehouse->getstock_id().'" >'.$warehouse->getstock_name().'</option>';
		}
	
		$retour .= '</select>';
		return $retour;
	}
	
	public function getIsInStockCombo($name)
	{
		$retour = '<select name="'.$name.'" id="'.$name.'">';
		
		$selected = '';
		if (($this->getDefaultStock()) && ($this->getDefaultStock()->getis_in_stock() == 1))
			$selected = ' selected ';
		$retour .= '<option value="1" '.$selected.'>'.$this->__('In stock').'</option>';

		
		$selected = '';
		if (($this->getDefaultStock()) && ($this->getDefaultStock()->getis_in_stock() == 0))
			$selected = ' selected ';
		$retour .= '<option value="0" '.$selected.'>'.$this->__('Out of stock').'</option>';
		
		$retour .= '</select>';
		return $retour;		
	}
	
	public function getUseDefaultCombo($name, $value, $targetField)
	{
		$onChange = "toggleFieldFromCombo('".$name."', '".$targetField."');";
		$retour = '<select name="'.$name.'" id="'.$name.'" onchange="'.$onChange.'">';

		$selected = '';
		if ($value == 1)
			$selected = ' selected ';
		$retour .= '<option value="1" '.$selected.'>'.$this->__('Yes').'</option>';
		
		$selected = '';
		if ($value == 0)
			$selected = ' selected ';
		$retour .= '<option value="0" '.$selected.'>'.$this->__('No').'</option>';
		
		$retour = $this->__('Use default : ').$retour;
		
		return $retour;	
	}
	
	/**
	 * Define if product manage stock (based on default stock)
	 *
	 */
	public function productManageStocks()
	{
		if ($this->getDefaultStock() == null)
			return false;
		else 
		{
			if ($this->getDefaultStock()->getuse_config_manage_stock())
			{
                            $inventoryGroupName = mage::helper('AdvancedStock/MagentoVersionCompatibility')->getStockOptionsGroupName();
                            return (Mage::getStoreConfig('cataloginventory/'.$inventoryGroupName.'/manage_stock') == 1);
			}
			else
			{
				return ($this->getDefaultStock()->getmanage_stock() == 1);
			}
		}
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function useConfigManageStock()
	{
		if ($this->getDefaultStock())
			return ($this->getDefaultStock()->getuse_config_manage_stock() == 1);
		else 
			return false;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function useConfigBackorders()
	{
		if ($this->getDefaultStock())
			return ($this->getDefaultStock()->getuse_config_backorders() == 1);
		else 
			return false;
	}
	
	/**
	 * Enter description here...
	 *
	 */
	public function useConfigMinSaleQty()
	{
		if ($this->getDefaultStock())
			return ($this->getDefaultStock()->getuse_config_min_sale_qty() == 1);
		else 
			return false;
	}
	
	/**
	 * Enter description here...
	 *
	 */
	public function useConfigMaxSaleQty()
	{
		if ($this->getDefaultStock())
			return ($this->getDefaultStock()->getuse_config_max_sale_qty() == 1);
		else 
			return false;
	}
	
	public function getMaxSalesQty()
	{
		if ($this->getDefaultStock() == null)
			return 999;
		else 
		{
			if ($this->getDefaultStock()->getuse_config_max_sale_qty())
			{
                            $inventoryGroupName = mage::helper('AdvancedStock/MagentoVersionCompatibility')->getStockOptionsGroupName();
                            return Mage::getStoreConfig('cataloginventory/'.$inventoryGroupName.'/max_sale_qty');
			}
			else
			{
				return ($this->getDefaultStock()->getmax_sale_qty());
			}
		}		
	}
	
	public function getBackOrderValue()
	{
		if ($this->getDefaultStock() == null)
			return 0;
		else 
		{
			if ($this->getDefaultStock()->getuse_config_backorders())
			{
                            $inventoryGroupName = mage::helper('AdvancedStock/MagentoVersionCompatibility')->getStockOptionsGroupName();
                            return Mage::getStoreConfig('cataloginventory/'.$inventoryGroupName.'/backorders');
			}
			else
			{
				return ($this->getDefaultStock()->getbackorders());
			}
		}		
	}
	
	public function getMinSalesQty()
	{
		if ($this->getDefaultStock() == null)
			return 999;
		else 
		{
			if ($this->getDefaultStock()->getuse_config_min_sale_qty())
			{
                            $inventoryGroupName = mage::helper('AdvancedStock/MagentoVersionCompatibility')->getStockOptionsGroupName();
                            return Mage::getStoreConfig('cataloginventory/'.$inventoryGroupName.'/min_sale_qty');
			}
			else
			{
				return ($this->getDefaultStock()->getmin_sale_qty());
			}
		}		
	}
	
	/**
	 * return default stock
	 *
	 * @return unknown
	 */
	public function getDefaultStock()
	{
		if ($this->_defaultStock == null)
		{
			$this->_defaultStock = mage::getModel('cataloginventory/stock_item')->loadByProductWarehouse($this->getProduct()->getId(), 1);
		}
		return $this->_defaultStock;
	}
}
