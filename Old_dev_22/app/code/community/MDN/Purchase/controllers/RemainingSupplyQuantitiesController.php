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
class MDN_Purchase_RemainingSupplyQuantitiesController extends Mage_Adminhtml_Controller_Action
{
	public function ListAction()
	{
    	$this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Remaining supply quantities'));

        $this->renderLayout();
	}
	
	public function exportCsvAction()
	{
    	$fileName   = 'remaining_supply_quantities.csv';
        $content    = $this->getLayout()->createBlock('Purchase/RemainingSupplyQuantities_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
	}
}