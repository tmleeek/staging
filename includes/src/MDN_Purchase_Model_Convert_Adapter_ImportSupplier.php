<?php

class MDN_Purchase_Model_Convert_Adapter_ImportSupplier extends Mage_Dataflow_Model_Convert_Adapter_Abstract {

    public function load() {
        // you have to create this method, enforced by Mage_Dataflow_Model_Convert_Adapter_Interface
    }

    public function save() {
        // you have to create this method, enforced by Mage_Dataflow_Model_Convert_Adapter_Interface
    }

    public function saveRow(array $importData)
    {
        $code = $this->getParam($importData, 'sup_code');
        $name = $this->getParam($importData, 'sup_name');

        if (($code == '') || ($name == ''))
            throw new Exception('Row skipped, supplier code or name is missing');

        //load or create supplier
        $supplier = $this->loadSupplier($code);
        if ($supplier == null)
        {
            //create supplier if not exists
            $supplier = mage::getModel('Purchase/Supplier');
            $supplier->setsup_code($code)->setsup_name($name)->save();
        }

        //update supplier
        foreach($importData as $key => $value)
        {
            $supplier->setData($key, $value);
        }
        $supplier->save();
    }

    protected function getParam($data, $name)
    {
        if (isset($data[$name]))
            return $data[$name];
        else
            return '';
    }

    protected function loadSupplier($supCode)
    {
        $supplier = mage::getModel('Purchase/Supplier')->load($supCode, 'sup_code');
        if ($supplier->getId())
            return $supplier;
        else
            return null;
    }
}