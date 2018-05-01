<?php
/**
 * created : 21 aout 2009
 * Alsobought product controller
 * 
 * 
 * @category SQLI
 * @package Sqli_Alsobought
 * @author sgautier
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Alsobought
 */
require ("app/code/core/Mage/Adminhtml/controllers/Catalog/ProductController.php");

class Tatva_Alsobought_Adminhtml_IndexController extends Mage_Adminhtml_Catalog_ProductController
{
    public function gridOnlyAlsoboughtAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('alsobought/adminhtml_alsobought')
                ->toHtml()
        );
    }
    /**
     * Get alsobought products grid and serializer block
     */
    public function alsoboughtAction(){
        $this->_initProduct();

        $gridBlock = $this->getLayout()->createBlock('alsobought/adminhtml_alsobought')
            ->setGridUrl($this->getUrl('*/*/gridOnlyAlsobought', array('_current' => true)))
        ;
        $serializerBlock = $this->_createSerializerBlock('links[also_bought]', $gridBlock, Mage::registry('product')->getAlsoBoughtProducts());

        $this->_outputBlocks($gridBlock, $serializerBlock);
    }


	public function alsoboughtGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.alsobought')
            ->setProductsAlsobought($this->getRequest()->getPost('products_alsobought', null));
        $this->renderLayout();
    }
}
