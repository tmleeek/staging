<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Common_Amazon_Template_ShippingTemplateController
    extends Ess_M2ePro_Controller_Adminhtml_Common_MainController
{
    //########################################

    protected function _initAction()
    {
        $this->loadLayout()
             ->_title(Mage::helper('M2ePro')->__('Policies'))
             ->_title(Mage::helper('M2ePro')->__('Shipping Template Policies'));

        $this->getLayout()->getBlock('head')
            ->addJs('M2ePro/Template/EditHandler.js')
            ->addJs('M2ePro/Common/Amazon/Template/EditHandler.js')
            ->addJs('M2ePro/Common/Amazon/Template/ShippingTemplateHandler.js');

        $this->_initPopUp();

        // todo must be added when develop branch becomes "public"
//        $this->pageHelpLink = Mage::helper('M2ePro/Module_Support')
//                                    ->getDocumentationUrl(null, null, 'TODO LINK');

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('m2epro_common/configuration');
    }

    //########################################

    public function indexAction()
    {
        return $this->_redirect('*/adminhtml_common_template/index', array(
            'channel' => Ess_M2ePro_Helper_Component_Amazon::NICK
        ));
    }

    //########################################

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('M2ePro/Amazon_Template_ShippingTemplate')->load($id);

        if (!$model->getId() && $id) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Policy does not exist'));
            return $this->_redirect('*/adminhtml_common_template/index', array(
                'channel' => Ess_M2ePro_Helper_Component_Amazon::NICK
            ));
        }

        Mage::helper('M2ePro/Data_Global')->setValue('temp_data', $model);

        $this->_initAction()
             ->_addContent(
                 $this->getLayout()->createBlock('M2ePro/adminhtml_common_amazon_template_shippingTemplate_edit')
             )
             ->renderLayout();
    }

    //########################################

    public function saveAction()
    {
        if (!$post = $this->getRequest()->getPost()) {
            return $this->indexAction();
        }

        $id = $this->getRequest()->getParam('id');

        // Base prepare
        // ---------------------------------------
        $data = array();

        $keys = array(
            'title',

            'template_name_mode',
            'template_name_value',
            'template_name_attribute',
        );

        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }

        $model = Mage::getModel('M2ePro/Amazon_Template_ShippingTemplate')->load($id);

        $oldData = (!empty($id)) ? $model->getDataSnapshot() : array();

        $model->addData($data)->save();

        $newData = $model->getDataSnapshot();

        $model->setSynchStatusNeed($newData,$oldData);

        $this->_getSession()->addSuccess(Mage::helper('M2ePro')->__('Policy was successfully saved'));
        $this->_redirectUrl(Mage::helper('M2ePro')->getBackUrl('*/adminhtml_common_template/index', array(), array(
            'edit' => array('id' => $model->getId()),
            'channel' => Ess_M2ePro_Helper_Component_Amazon::NICK
        )));
    }

    public function deleteAction()
    {
        $ids = $this->getRequestIds();

        if (count($ids) == 0) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Please select Item(s) to remove.'));
            $this->_redirect('*/*/index');
            return;
        }

        $deleted = $locked = 0;
        foreach ($ids as $id) {
            $template = Mage::getModel('M2ePro/Amazon_Template_ShippingTemplate')->load($id);
            if (!$template->getId()) {
                continue;
            }

            if ($template->isLocked()) {
                $locked++;
            } else {
                $template->deleteInstance();
                $deleted++;
            }
        }

        $tempString = Mage::helper('M2ePro')->__('%amount% record(s) were successfully deleted.', $deleted);
        $deleted && $this->_getSession()->addSuccess($tempString);

        $tempString  = Mage::helper('M2ePro')->__('%amount% record(s) are used in Listing(s).', $locked) . ' ';
        $tempString .= Mage::helper('M2ePro')->__('Policy must not be in use to be deleted.');
        $locked && $this->_getSession()->addError($tempString);

        return $this->_redirect('*/adminhtml_common_template/index', array(
            'channel' => Ess_M2ePro_Helper_Component_Amazon::NICK
        ));
    }

    //########################################
}