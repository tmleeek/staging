<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Common_TemplateController
    extends Ess_M2ePro_Controller_Adminhtml_Common_MainController
{
    //########################################

    protected function _initAction()
    {
        $this->loadLayout()
            ->_title(Mage::helper('M2ePro')->__('Policies'));

        $this->getLayout()->getBlock('head')
            ->addJs('M2ePro/Plugin/DropDown.js')
            ->addJs('M2ePro/Plugin/ActionColumn.js')
            ->addCss('M2ePro/css/Plugin/DropDown.css');

        $this->setPageHelpLink(NULL, NULL, "x/ioIVAQ");

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('m2epro_common/configuration');
    }

    //########################################

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent(
                $this->getLayout()->createBlock(
                    'M2ePro/adminhtml_common_configuration', '',
                    array('active_tab' => Ess_M2ePro_Block_Adminhtml_Common_Configuration_Tabs::TAB_ID_TEMPLATE)
                )
            )->renderLayout();
    }

    public function gridAction()
    {
        $channel = $this->getRequest()->getParam('channel');

        if (empty($channel)) {
            return $this->getResponse()->setBody('You should provide correct parameters.');
        }

        $block = $this->loadLayout()->getLayout()->createBlock('M2ePro/adminhtml_common_'.$channel.'_template_grid');
        $this->getResponse()->setBody($block->toHtml());
    }

    //########################################

    public function newAction()
    {
        $type    = $this->getPreparedTemplateType($this->getRequest()->getParam('type'));
        $channel = $this->getRequest()->getParam('channel');

        if (!$type) {

            $this->_getSession()->addError(Mage::helper('M2ePro')->__('You should provide correct parameters.'));
            return $this->_redirect('*/*/index', array(
                'channel' => $this->getRequest()->getParam('channel')
            ));
        }

        return $this->_redirect("*/adminhtml_common_{$channel}_template_{$type}/edit");
    }

    //########################################

    public function editAction()
    {
        $id   = $this->getRequest()->getParam('id');
        $type = $this->getPreparedTemplateType($this->getRequest()->getParam('type'));

        if (is_null($id) || empty($type)) {

            $this->_getSession()->addError(Mage::helper('M2ePro')->__('You should provide correct parameters.'));
            return $this->_redirect('*/*/index', array(
                'channel' => $this->getRequest()->getParam('channel')
            ));
        }

        /** @var Ess_M2ePro_Model_Component_Parent_Abstract $templateModel */
        $templateModel = $this->getTemplateModel($type, $id);

        if ($type == 'shippingOverride') {
            return $this->_redirect(
                "*/adminhtml_common_amazon_template_shippingOverride/edit", array('id'=>$id)
            );
        }

        if ($type == 'shippingTemplate') {
            return $this->_redirect(
                "*/adminhtml_common_amazon_template_shippingTemplate/edit", array('id'=>$id)
            );
        }

        if ($type == 'productTaxCode') {
            return $this->_redirect(
                "*/adminhtml_common_amazon_template_productTaxCode/edit", array('id'=>$id)
            );
        }

        return $this->_redirect(
            "*/adminhtml_common_{$templateModel->getComponentMode()}_template_{$type}/edit", array('id'=>$id)
        );
    }

    public function deleteAction()
    {
        $ids  = $this->getRequestIds();
        $type = $this->getPreparedTemplateType($this->getRequest()->getParam('type'));

        if (count($ids) == 0) {

            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Please select Item(s) to remove.'));
            return $this->_redirect('*/*/index', array(
                'channel' => $this->getRequest()->getParam('channel')
            ));
        }

        if (empty($type)) {

            $this->_getSession()->addError(Mage::helper('M2ePro')->__('You should provide correct parameters.'));
            return $this->_redirect('*/*/index', array(
                'channel' => $this->getRequest()->getParam('channel')
            ));
        }

        $deleted = $locked = 0;
        foreach ($ids as $id) {

            /** @var Ess_M2ePro_Model_Component_Parent_Abstract $templateModel */
            $templateModel = $this->getTemplateModel($type, $id);

            if ($templateModel->isLocked()) {
                $locked++;
            } else {
                $templateModel->deleteInstance();
                $deleted++;
            }
        }

        $tempString = Mage::helper('M2ePro')->__('%amount% record(s) were successfully deleted.', $deleted);
        $deleted && $this->_getSession()->addSuccess($tempString);

        $tempString  = Mage::helper('M2ePro')->__('%amount% record(s) are used in Listing(s).', $locked) . ' ';
        $tempString .= Mage::helper('M2ePro')->__('Policy must not be in use to be deleted.');
        $locked && $this->_getSession()->addError($tempString);

        $this->_redirect('*/*/index', array(
            'channel' => $this->getRequest()->getParam('channel')
        ));
    }

    //########################################

    private function getPreparedTemplateType($type)
    {
        $templateTypes = array(
            Ess_M2ePro_Block_Adminhtml_Common_Template_Grid::TEMPLATE_SELLING_FORMAT,
            Ess_M2ePro_Block_Adminhtml_Common_Template_Grid::TEMPLATE_SYNCHRONIZATION,

            Ess_M2ePro_Block_Adminhtml_Common_Amazon_Template_Grid::TEMPLATE_DESCRIPTION,
            Ess_M2ePro_Block_Adminhtml_Common_Amazon_Template_Grid::TEMPLATE_SHIPPING_OVERRIDE,
            Ess_M2ePro_Block_Adminhtml_Common_Amazon_Template_Grid::TEMPLATE_SHIPPING_TEMPLATE,
            Ess_M2ePro_Block_Adminhtml_Common_Amazon_Template_Grid::TEMPLATE_PRODUCT_TAX_CODE
        );

        if (!in_array(strtolower($type), $templateTypes)) {
            return null;
        }

        if (strtolower($type) == Ess_M2ePro_Block_Adminhtml_Common_Template_Grid::TEMPLATE_SELLING_FORMAT) {
            return 'sellingFormat';
        }

        if (strtolower($type) == Ess_M2ePro_Block_Adminhtml_Common_Amazon_Template_Grid::TEMPLATE_SHIPPING_OVERRIDE) {
            return 'shippingOverride';
        }

        if (strtolower($type) == Ess_M2ePro_Block_Adminhtml_Common_Amazon_Template_Grid::TEMPLATE_SHIPPING_TEMPLATE) {
            return 'shippingTemplate';
        }

        if (strtolower($type) == Ess_M2ePro_Block_Adminhtml_Common_Amazon_Template_Grid::TEMPLATE_PRODUCT_TAX_CODE) {
            return 'productTaxCode';
        }

        return $type;
    }

    private function getTemplateModel($type, $id)
    {
        if ($type == 'shippingOverride') {
            return Mage::getModel('M2ePro/Amazon_Template_ShippingOverride')->load($id);
        }

        if ($type == 'shippingTemplate') {
            return Mage::getModel('M2ePro/Amazon_Template_ShippingTemplate')->load($id);
        }

        if ($type == 'productTaxCode') {
            return Mage::getModel('M2ePro/Amazon_Template_ProductTaxCode')->load($id);
        }

        return Mage::helper('M2ePro/Component')->getUnknownObject('Template_' . ucfirst($type), $id);
    }

    //########################################
}