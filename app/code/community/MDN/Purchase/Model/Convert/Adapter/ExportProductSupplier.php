<?php

class MDN_Purchase_Model_Convert_Adapter_ExportProductSupplier extends Mage_Dataflow_Model_Convert_Container_Abstract {

    private $_collection = null;

    const k_lineReturn = "\r\n";

    /**
     * Load product collection Id(s)
     *
     */
    public function load() {
        $nameAttributeId = mage::getModel('Purchase/Constant')->GetProductNameAttributeId();

        $this->_collection = mage::getModel('Purchase/ProductSupplier')
                ->getCollection()
                ->join('Purchase/Supplier', 'sup_id=pps_supplier_num')
                ->join('catalog/product', 'pps_product_id=`catalog/product`.entity_id')
                ->join('AdvancedStock/CatalogProductVarchar', '`catalog/product`.entity_id=`AdvancedStock/CatalogProductVarchar`.entity_id and `AdvancedStock/CatalogProductVarchar`.store_id = 0 and `AdvancedStock/CatalogProductVarchar`.attribute_id = ' . mage::getModel('AdvancedStock/Constant')->GetProductNameAttributeId())
        ;

        //Affiche le nombre de commande charg�e
        $this->addException(Mage::helper('dataflow')->__('Loaded %s rows', $this->_collection->getSize()), Mage_Dataflow_Model_Convert_Exception::NOTICE);
    }

    /**
     * Enregistre
     *
     */
    public function save() {
        $this->load();

        //D�finit le chemin ou sauver le fichier
        $path = $this->getVar('path') . '/' . $this->getVar('filename');
        $f = fopen($path, 'w');
        $fields = $this->getFields();

        //add header
        $header = '';
        foreach ($fields as $field) {
            $header .= $field['label'] . ';';
        }
        fwrite($f, $header . self::k_lineReturn);

        //add orders
        foreach ($this->_collection as $item) {
            $line = '';
            foreach ($fields as $field) {
                $line .= $item->getData($field['field']) . ';';
            }
            fwrite($f, $line . self::k_lineReturn);
        }

        //Affiche le nombre de commande charg�e
        fclose($f);
        $this->addException(Mage::helper('dataflow')->__('Export saved in %s', $path), Mage_Dataflow_Model_Convert_Exception::NOTICE);
    }

    /**
     * return fields to export
     *
     */
    public function getFields() {
        $t = array();

        $t[] = array('label' => 'product_sku', 'field' => 'sku');
        $t[] = array('label' => 'product_name', 'field' => 'value');

        $t[] = array('label' => 'supplier_code', 'field' => 'sup_code');
        $t[] = array('label' => 'supplier_name', 'field' => 'sup_name');

        $t[] = array('label' => 'supplier_product_reference', 'field' => 'pps_reference');
        $t[] = array('label' => 'supplier_last_price', 'field' => 'pps_last_price');
        $t[] = array('label' => 'supplier_last_price_with_extended_costs', 'field' => 'pps_last_unit_price');
        $t[] = array('label' => 'supplier_last_order_date', 'field' => 'pps_last_order_date');

        return $t;
    }

}