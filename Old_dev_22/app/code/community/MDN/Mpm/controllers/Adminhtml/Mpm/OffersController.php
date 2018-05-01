<?php

class MDN_Mpm_Adminhtml_Mpm_OffersController extends Mage_Adminhtml_Controller_Action
{

    public function IndexAction()
    {
        if (!Mage::helper('Mpm/Carl')->checkCredentials())
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('Please configure Carl credentials first'));
            $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
        }
        else {
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function exportOffersGridCsvAction()
    {
        $fileName = 'carl_offers_'.date('Y_M_D_H_i_s').'.csv';
        $content = $this->getLayout()->createBlock('Mpm/Offers_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportOffersGridExcelAction()
    {
        $fileName = 'carl_offers_'.date('Y_M_D_H_i_s').'.xls';
        $content = $this->getLayout()->createBlock('Mpm/Offers_Grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return true;
    }

}