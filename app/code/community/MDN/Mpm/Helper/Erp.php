<?php


class MDN_Mpm_Helper_Erp extends Mage_Core_Helper_Abstract
{

    public function getBestSupplier($product)
    {
        $bestSupplier = null;

        $suppliers = Mage::getSingleton('Purchase/ProductSupplier')->getSuppliersForProduct($product);
        foreach($suppliers as $supplier)
        {
            if (!$this->supplierIsEligible($supplier))
                continue;

            if ($bestSupplier == null)
                $bestSupplier = $supplier;
            else
            {
                if ($bestSupplier->getpps_last_unit_price() > $supplier->getpps_last_unit_price())
                    $bestSupplier = $supplier;
            }
        }

        return $bestSupplier;
    }

    protected function supplierIsEligible($supplier)
    {
        if (Mage::getStoreConfig('mpm/catalog_export_erp/exclude_supplier_no_stock') && $supplier->getpps_quantity_product() <= 0)
            return false;

        if (Mage::getStoreConfig('mpm/catalog_export_erp/exclude_supplier_no_price') && $supplier->getpps_last_unit_price() <= 0)
            return false;

        return true;
    }

    public function isInstalled()
    {
        return (Mage::getStoreConfig('advancedstock/erp/is_installed') == 1);
    }

}