<?php

class MDN_ProductReturn_Helper_Returnlabel extends Mage_Core_Helper_Abstract
{

    public function getReturnlabelUrl($rma)
    {
        $file = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'rma_return_labels/' . md5($rma->getrma_id() . '-' . $rma->getrma_ref()) . '.pdf';

        return $file;
    }


    public function isExists($rma)
    {
        $file = $this->getBaseReturnlabelUrl($rma);

        return file_exists($file);
    }

    public function getBaseReturnlabelUrl($rma)
    {
        return Mage::getBaseDir('media') . DS . 'rma_return_labels' . DS . md5($rma->getrma_id() . '-' . $rma->getrma_ref()) . '.pdf';
    }

}