<?php
  class Tatva_MandatCash_Model_System_Config_Source_Cmsblock
  {
   protected $_options;

   public function toOptionArray()
   {
      if (!$this->_options) {
        $this->_options = Mage::getResourceModel('cms/block_collection')
                        ->load()
                        ->toOptionArray();
        }
        return $this->_options;
   }
  }
?>