<?php class Tatva_Marqueproducts_Model_Marqueproducts extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('marqueproducts/marqueproducts');
    }
}