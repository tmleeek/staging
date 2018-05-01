<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitmanufacturers_Model_Config extends Mage_Core_Model_Abstract
{
    protected $_collection = null;
    protected $_optionCollection = null;
    protected $_scope = null;
    protected $_scopeCode = null;
    protected $_scopeId = null;
    protected $_cachedConfig = array();
    
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitmanufacturers/config');
    }
    
    public function getScopeConfig($attributeCode, $scope = 'default', $id = 0, $search = true, $config = false)
    {
        if (!$attributeCode)
        {
            return false;
        }

        $configKey = implode('-', array('aitmanufacturer',$attributeCode, $scope, $id));
        if (!Mage::registry($configKey))
        {
            Mage::register($configKey, $this->searchConfig($attributeCode, $scope , $id , $search , $config));
        }
        return Mage::registry($configKey);
    }

    public function searchConfig($attributeCode, $scope = 'default', $id = 0, $search = true, $config = false)
    {
        $config = $this->getResource()->loadConfig($attributeCode, $scope, $id);
        if ($config)
        {
            $config['use_default'] = 0;
            if ($config['is_active'] || $config)
                return $config;
            else 
                return false;
        }
        
        if ($search)  
        {
            switch ($scope)
            {
                case 'default':
                    return false;
                    break;
                case 'website':
                    $config = $this->searchConfig($attributeCode, 'default', 0, $search, $config);
                    if ($config)
                    {
                        $config['use_default'] = true;
                        $config['is_active'] = true;
                        return $config;
                    }
                    break;
                case 'store':
                    $config = $this->searchConfig($attributeCode, 'website', Mage::app()->getStore($id)->getWebsiteId(), $search, $config);
                    if ($config)
                    {
                        $config['use_default'] = true;
                        $config['is_active'] = true;
                        return $config;
                    }
                    break;
            }
        }
        return false;
    }

    public function getIsActive($attributeCode, $scope = 'store')
    {
        $scopes = array('store', 'website', 'default');
        $scopeLevel = array_search($scope, $scopes);
        
        $scopeId = 0;
        if ('store' == $scope) {
            $scopeId = Mage::app()->getStore()->getId();
        }
        if ('website' == $scope) {
            $scopeId = Mage::app()->getStore()->getWebsiteId();
        }

        $config = $this->getResource()->loadConfig($attributeCode, $scope, $scopeId);
        if ($config)
        {
            return $config['is_active'];
        }
        elseif (isset($scopes[$scopeLevel + 1]))
        {
            return $this->getIsActive($attributeCode, $scopes[$scopeLevel + 1]);
        }
        else
        {
            return false;
        }
    }

    public function isValid($values, $dropdown = true)
    {
        $this->valid = true;
        $active = false;
        
        if (!is_array($values))
        {
            return false;
        }
        
        foreach ($values as $key=>$storeConfig)
        {
            if (!(($storeConfig['is_active'] == 0) || (isset($storeConfig['use_default']) && ($storeConfig['use_default'] != 0))))
            {
                $active = true;
                $scopeName = $this->getScopeName($key);
                
                if (!$storeConfig['url_prefix'])
                {
                    $this->valid = false;
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('You should enter url prefix on %s store config.', $scopeName)
                    );
                }
                if (!$storeConfig['url_pattern'])
                {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('You should enter url pattern on %s store config.', $scopeName)
                    );
                }
                
                $originalValue = $storeConfig['url_pattern'];
                $newValue = str_replace(str_split('$#?"\'%&@'),'',$originalValue);
                
                if($newValue!=$originalValue)
                {
                    $this->valid = false;
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('Special symbols are not allowed in URL pattern on %s store config.', $scopeName)
                    );
                }
                if((substr($newValue,-1,1)=='/')||(substr($newValue,0,1)=='/'))
                {
                    $this->valid = false;
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('Symbol \' / \' must be neither first neither last in URL pattern on %s store config.', $scopeName)
                    );
                }
                if(strpos($newValue,'[attribute]')===false)
                {
                    $this->valid = false;
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('URL pattern must contain [attribute] part on %s store config.', $scopeName)
                    );
                }
                
                if ((int)$storeConfig['columns_num'] < 1) 
                {
                    $this->valid = false;
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('You should enter number of columns on %s store config.', $scopeName)
                    );
                }
                if ((int)$storeConfig['brief_num'] < 0)
                {
                    $this->valid = false;
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('You should enter number of attributes in the attribute block on %s store config.', $scopeName)
                    );
                }
            } 
        }
        
        if (!$dropdown && $active)
        {
            $this->valid = false;
            
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('catalog')->__('You can activate Shop By Attribute for Dropdown type attributes only.')
            );
        }
        return $this->valid;
    }
    
    public function getScopeName($scopeCode)
    {
        $scope = $this->getScopeFromCode($scopeCode);
        $scopeId = $this->getScopeIdFromCode($scopeCode);
        
        switch ($scope)
        {
            case 'default':
                return Mage::helper('aitmanufacturers')->__('All Store Views');
                break;
            case 'website':
                return Mage::app()->getWebsite($scopeId)->getName();
                break;
            case 'store':
                return Mage::app()->getStore($scopeId)->getName();
                break;
        }
        return false;
    }
    
    public function saveConfigData($configData, $attributeCode)
    {
        if (!$configData)
        {
            return false;
        }
        
        $collection = $this->getCollection();
        $collection->addFieldToFilter('attribute_code',array('eq'=>$attributeCode));
                  
        foreach ($collection as $item)
        {
            $scopeCode = $item->getScope().'_'.$item->getScopeId();
            if (isset($configData[$scopeCode]) && (($item->getScope() == 'default') || (!$configData[$scopeCode]['use_default'])))
            {
                $configData[$scopeCode]['id'] = $item->getId();
                $item->setData($configData[$scopeCode]);
                unset($configData[$scopeCode]);
                $item->save();
            } else {
                $item->delete();
            }
        }
                   
        foreach ($configData as $key => $data)
        {
            //if (($configData[$key]['is_active']) && (($this->getScopeFromCode($key) == 'default') || (!$configData[$key]['use_default'])))
            if (($configData[$key]['is_active'] && ($this->getScopeFromCode($key) == 'default')) || (isset($configData[$key]['use_default']) && !$configData[$key]['use_default']))
            {
                $item = Mage::getModel('aitmanufacturers/config');
                $item->addData($data);
                $item->setData('attribute_code', $attributeCode);
                $item->setData('scope_id',       $this->getScopeIdFromCode($key));
                $item->setData('scope',          $this->getScopeFromCode($key));
                $item->save();
            }
        }
        
        if ($collection->getSize())
        {
            $stores = Mage::app()->getStores(true, false);
            
            foreach ($stores as $storeId=>$value)
            {
                
                $config = $this->getScopeConfig($attributeCode, 'store', $storeId);
                if (!$config || !$config['is_active'])
                {
                    $this->getResource()->disableBrandPages($storeId, $attributeCode);
                }
            }
        }
        
        Mage::getSingleton('adminhtml/session')->setData('aitmanufacturers_update_stores',true);
        return true;
    }
    
    public function getAttributesByScope($storeId)
    {
        $attributes = Mage::getResourceModel('aitmanufacturers/config')->getAttributeList();
        $resultList = array();
        foreach ($attributes as $attributeCode => $label)
        {
            if ($config = $this->getScopeConfig($attributeCode, 'store', $storeId))
            {
                $resultList[] = $config;
            }
        }
        return $resultList;
    }
    
    public function getScopeFromCode($code)
    {
        $arr = explode('_',$code);
        if (!isset($arr[0]) || !isset($arr[1]))
        {
            return false;
        }
        return $arr[0];
    }
    
    public function getScopeIdFromCode($code)
    {
        $arr = explode('_',$code);
        if (!isset($arr[0]) || !isset($arr[1]))
        {
            return false;
        }
        return $arr[1];
    }
    
    public function getAttributeName($code)
    {
        if (!$code)
        {
            return 'Attribute';
        }
        
        $allStoreLabels = Mage::getSingleton('catalog/entity_attribute')->loadByCode('catalog_product', $code)->getStoreLabels();

        if (isset($allStoreLabels[Mage::app()->getStore()->getStoreId()]))
        {
            $name = $allStoreLabels[Mage::app()->getStore()->getStoreId()];
        }
        else
        {
            $name = '';
        }   
        
        if ($name == '')
        {
            $name = Mage::getModel('catalog/entity_attribute')->load($this->getAttributeId($code))->getFrontendLabel();
        }
        
        if ($code == 'manufacturer' && Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_replace_manufacturer'))
        {
            $name = 'Brand';
        }
        
        return $name; 
    }
    
    public function getAttributeId($code)
    {
        return Mage::getModel('catalog/entity_attribute')->loadByCode('catalog_product', $code)->getId();
    }
    
    public function getAttributeIdByOption($optionId)
    {
        $model = Mage::getModel('eav/entity_attribute_option');
        
        return Mage::getModel('eav/entity_attribute_option')->load($optionId)->getAttributeId();
    }
    
    public function getAttributeNameByOption($optionId)
    {
        return Mage::getModel('catalog/entity_attribute')->load($this->getAttributeIdByOption($optionId))->getFrontendLabel();
    }
    
    public function getAttributeCodeByOption($optionId)
    {
        return Mage::getModel('catalog/entity_attribute')->load($this->getAttributeIdByOption($optionId))->getAttributeCode();
    }
    
    public function getAttributeCodeById($attributeId)
    {
        return Mage::getModel('catalog/entity_attribute')->load($attributeId)->getAttributeCode();
    }
    
}