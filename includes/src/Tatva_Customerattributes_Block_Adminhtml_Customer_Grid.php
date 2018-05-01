<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tatva_Customerattributes_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
        $attribute = Mage::getStoreConfig('customerattributes/config/customer_attribute');
        $attr_array = explode(',',$attribute);
        $count = count($attr_array);
        for($i=0;$i<$count;$i++)
        {
            $attribute1 = Mage::getModel('catalog/resource_eav_attribute')->load($attr_array[$i]);
            $collection->addAttributeToSelect($attribute1->getAttributeCode());
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
		
		$this->addColumn('increment_id', array(
            'header'    => Mage::helper('customer')->__('Customer ID'),
            'index'     => 'increment_id'
        ));
        /*$this->addColumn('firstname', array(
            'header'    => Mage::helper('customer')->__('First Name'),
            'index'     => 'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    => Mage::helper('customer')->__('Last Name'),
            'index'     => 'lastname'
        ));*/
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone',
        ));

        $attribute = Mage::getStoreConfig('customerattributes/config/customer_attribute');
        $attr_array = explode(',',$attribute);
        $count = count($attr_array);
		if($count > 0)
		{
	        for($i=0;$i<$count;$i++)
	        {
	           $attribute1 = Mage::getModel('catalog/resource_eav_attribute')->load($attr_array[$i]);
	           //echo "<pre>";print_r($attribute1->getData());
	           if($attribute1->getFrontendInput() == 'select')
	           {
	                $attribute_code = Mage::getModel('eav/config')->getAttribute('customer', $attribute1->getAttributeCode());
	                $options = $attribute_code->getSource()->getAllOptions(false);
	                $options = array();
	                foreach( $attribute_code->getSource()->getAllOptions(true, true) as $option ) {
	                   if($option['label'] == '')
	                    {
	                        continue;
	                    }
	                    else
	                    {
	                        $options[$option['value']] = $option['label'];
	                   }
	                }
	                $this->addColumn($attribute1->getAttributeCode(),
	                array(
	                    'header'=>Mage::helper('customer')->__($attribute1->getFrontendLabel()),
	                    'type'=>'options',
	                    'index'=>$attribute1->getAttributeCode(),
	                    'options' => $options,
	                ));
	           }/*else if($attribute1->getFrontendInput() == 'price')
	           {
	                $store = $this->_getStore();

	                $this->addColumn($attribute1->getAttributeCode(),
	                array(
	                    'header'=>Mage::helper('customer')->__($attribute1->getFrontendLabel()),
	                    'type'=>'price',
	                    'currency_code' => $store->getBaseCurrency()->getCode(),
	                    'index'=>$attribute1->getAttributeCode(),
	                ));
	           }*/else if($attribute1->getFrontendInput() == 'boolean')
	           {
	               $this->addColumn($attribute1->getAttributeCode(),
	                array(
	                    'header'=>Mage::helper('customer')->__($attribute1->getFrontendLabel()),
	                    'type'=>'options',
	                    'index'=>$attribute1->getAttributeCode(),
	                    'options'=>Mage::getModel('customerattributes/customerattributes')->getOptionArray(),
	                ));
	           }else if($attribute1->getFrontendInput() == 'date')
	           {
	               $this->addColumn($attribute1->getAttributeCode(),
	                array(
	                    'header'=>Mage::helper('customer')->__($attribute1->getFrontendLabel()),
	                    'type'=>'date',
	                    'index'=>$attribute1->getAttributeCode(),
	                ));
	           }else if($attribute1->getFrontendInput() == 'image')
	           {
	               $this->addColumn($attribute1->getAttributeCode(),
	                array(
	                    'header'=>Mage::helper('customer')->__($attribute1->getFrontendLabel()),
	                    'type'=>'image',
	                    'width'=>'80px',
	                    'index'=>$attribute1->getAttributeCode(),
	                    'escape'    => true,
	                    'sortable'  => false,
	                    'filter'    => false,
	                    'renderer' => new Tatva_Customerattributes_Block_Adminhtml_Grid_Renderer_Image,
	                ));
	           }else if($attribute1->getFrontendInput() == 'multiselect')
	           {
	                $this->addColumn($attribute1->getAttributeCode(),
	                array(
	                    'header'=>Mage::helper('customer')->__($attribute1->getFrontendLabel()),
	                    'type'=>'options',
	                    'index'=>$attribute1->getAttributeCode(),
	                    'options'  =>Mage::getModel('customerattributes/customerattributes')->getOption($attr_array[$i]),
	                    'renderer'  => 'Tatva_Customerattributes_Block_Adminhtml_Widget_Grid_Column_Renderer_Multiselectattributes',
	                    'filter_condition_callback' => array($this, '_filterMultiSelectAttribue'),
	                ));
	           }
	           else
	           {
	               $type = $attribute1->getFrontendInput();
				   if($type != "")
				   {
		               $this->addColumn($attribute1->getAttributeCode(),
		                array(
		                    'header'=>Mage::helper('customer')->__($attribute1->getFrontendLabel()),
		                    'type'=>$attribute1->getFrontendType(),
		                    'index'=>$attribute1->getAttributeCode(),
		                ));
					}
	           }
	        }
		}

        $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('customer')->__('ZIP'),
            'width'     => '90',
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => Mage::helper('customer')->__('State/Province'),
            'width'     => '100',
            'index'     => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('customer')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('customer')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('newsletter_subscribe', array(
             'label'    => Mage::helper('customer')->__('Subscribe to Newsletter'),
             'url'      => $this->getUrl('*/*/massSubscribe')
        ));

        $this->getMassactionBlock()->addItem('newsletter_unsubscribe', array(
             'label'    => Mage::helper('customer')->__('Unsubscribe from Newsletter'),
             'url'      => $this->getUrl('*/*/massUnsubscribe')
        ));

        $groups = $this->helper('customer')->getGroups()->toOptionArray();

        array_unshift($groups, array('label'=> '', 'value'=> ''));
        $this->getMassactionBlock()->addItem('assign_group', array(
             'label'        => Mage::helper('customer')->__('Assign a Customer Group'),
             'url'          => $this->getUrl('*/*/massAssignGroup'),
             'additional'   => array(
                'visibility'    => array(
                     'name'     => 'group',
                     'type'     => 'select',
                     'class'    => 'required-entry',
                     'label'    => Mage::helper('customer')->__('Group'),
                     'values'   => $groups
                 )
            )
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

    protected function _filterMultiSelectAttribue($collection, $column)
    {
      $value = $column->getFilter()->getValue();
      //echo "<pre>";print_r($column->getData());
      $this->getCollection()->addFieldToFilter($column['index'], array(array('finset'=>$value)));
    }
}
