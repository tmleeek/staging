<?php
// We need to include the original core class as our class is going to act as the main class.
require_once("Mage/Adminhtml/controllers/Cms/BlockController.php");

class Webshops4All_DuplicateBlock_Adminhtml_Cms_BlockController extends Mage_Adminhtml_Cms_BlockController
{
    public function duplicateAction()
    {
        // Get query string params such as the block_id we wish to duplicate
        $params = $this->getRequest()->getParams();
        
        // Load the salesrule we wish to duplicate
        $model = Mage::getModel('cms/block')->load($params['block_id']);

        // Now to check to see if it has loaded correctly/found the block we are wanting to duplicate
        if($model)
        {
            // Now we need to get the existing block data
            $cms_data = $model->getData();


            // We need to use the salesrule/rule model again to create our new block
            $duplicate_block = Mage::getModel('cms/block');
            
            // We need to make sure that id field is unset to prevent overwriting our block
            unset($cms_data['block_id']);

            // Rename our title and add (Duplicated)
            $cms_data['title'] = $cms_data['title'] . " (Duplicated)";

            // Rename our identifier and add -duplicated - remember identifiers have to be unique!
            $cms_data['identifier'] = $cms_data['identifier'] . "-duplicated";
            
            // Set the data of the duplicated block
            $duplicate_block->setData($cms_data);

            // Saves the block
            $duplicate_block->save();
            
            // All done, now to redirect you to the new duplicated block!
            $this->_redirect('*/*/edit', array('block_id' => $duplicate_block->getId(), '_current' => true));
                return;
        }
    }
}
?>