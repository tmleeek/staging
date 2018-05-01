<?php
class MondialRelay_Pointsrelais_System_ConfigController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->setFlag('index', 'no-preDispatch', true);
        return parent::_construct();
    }

    public function exportAction()
    {        
        $this->exportCSV();
    }

    public function exportcdAction()
    {        
        $this->exportCSV('pointsrelais/carrier_pointsrelaiscd');
    }

    public function exportld1Action()
    {        
        $this->exportCSV('pointsrelais/carrier_pointsrelaisld1');
    }

    public function exportldsAction()
    {        
        $this->exportCSV('pointsrelais/carrier_pointsrelaislds');
    }
    
    public function exportCSV($class = 'pointsrelais/carrier_pointsrelais')
    {
        $websiteModel = Mage::app()->getWebsite($this->getRequest()->getParam('website'));

        $conditionName = $this->getRequest()->getParam('conditionName');
        
        Mage::log('conditionName : '.$conditionName);
        $tableratesCollection = Mage::getResourceModel($class.'_collection');
        $tableratesCollection->setConditionFilter($conditionName);
        $tableratesCollection->setWebsiteFilter($websiteModel->getId());
        $tableratesCollection->load();

        $csv = '';

        $conditionName = Mage::getModel($class)->getCode('condition_name_short', $conditionName);

        $csvHeader = array(
        	'"'.Mage::helper('adminhtml')->__('Country').'"', 
        	'"'.Mage::helper('adminhtml')->__('Region/State').'"', 
        	'"'.Mage::helper('adminhtml')->__('Zip/Postal Code').'"', 
        	'"'.$conditionName.'"', 
        	'"'.Mage::helper('adminhtml')->__('Shipping Price').'"'
        	);
        	
        $csv .= implode(',', $csvHeader)."\n";

        foreach ($tableratesCollection->getItems() as $item) 
        {
            if ($item->getData('dest_country') == '') 
            {
                $country = '*';
            } 
            else 
            {
                $country = $item->getData('dest_country');
            }
            
            if ($item->getData('dest_region') == '') 
            {
                $region = '*';
            } 
            else 
            {
                $region = $item->getData('dest_region');
            }
            
            if ($item->getData('dest_zip') == '') 
            {
                $zip = '*';
            } 
            else 
            {
                $zip = $item->getData('dest_zip');
            }

            $csvData = array(
            	$country, 
            	$region, 
            	$zip, 
            	$item->getData('condition_value'), 
            	$item->getData('price'),
            	);
            	
            foreach ($csvData as $cell) 
            {
                $cell = '"'.str_replace('"', '""', $cell).'"';
            }
            
            $csv .= implode(',', $csvData)."\n";
        }

        header('Pragma: public');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=pointsrelais.csv");
        echo $csv;
        exit;
    }
}      