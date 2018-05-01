<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Common_AccountController
    extends Ess_M2ePro_Controller_Adminhtml_Common_MainController
{
    //########################################

    protected function _initAction()
    {
        $this->loadLayout()
             ->_title(Mage::helper('M2ePro')->__('Configuration'))
             ->_title(Mage::helper('M2ePro')->__('Accounts'));

        $this->getLayout()->getBlock('head')->addJs('M2ePro/Plugin/DropDown.js')
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
                    array('active_tab' => Ess_M2ePro_Block_Adminhtml_Common_Configuration_Tabs::TAB_ID_ACCOUNT)
                )
            )->renderLayout();
    }

    public function editAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $componentMode = Mage::getModel('M2ePro/Account')->load($id)->getComponentMode();
        return $this->_redirect('*/adminhtml_common_'.$componentMode.'_account/edit', array('id'=>$id));
    }

    public function accountGridAction()
    {
        $response = $this->loadLayout()->getLayout()->createBlock('M2ePro/adminhtml_common_account_grid')->toHtml();
        $this->getResponse()->setBody($response);
    }

    //########################################

    public function deleteAction()
    {
        $ids = $this->getRequestIds();

        if (count($ids) == 0) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Please select account(s) to remove.'));
            $this->_redirect('*/*/index');
            return;
        }

        /** @var Ess_M2ePro_Model_Mysql4_Account_Collection $accountCollection */
        $accountCollection = Mage::getModel('M2ePro/Account')->getCollection();
        $accountCollection->addFieldToFilter('id', array('in' => $ids));

        $accounts = $accountCollection->getItems();

        if (empty($accounts)) {
            $this->_redirect('*/*/index');
            return;
        }

        $deleted = $locked = 0;
        foreach ($accounts as $account) {

            /** @var $account Ess_M2ePro_Model_Account */

            if ($account->isLocked(true)) {
                $locked++;
                continue;
            }

            try {

                $dispatcherObject = null;

                if ($account->isComponentModeAmazon()) {

                    $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');

                } else if ($account->isComponentModeBuy()) {

                    $dispatcherObject = Mage::getModel('M2ePro/Buy_Connector_Dispatcher');

                }

                if ($dispatcherObject) {

                    $connectorObj = $dispatcherObject->getConnector('account','delete','entityRequester',
                                                                    array(), $account);
                    $dispatcherObject->process($connectorObj);
                }

            } catch (Exception $e) {

                $account->deleteProcessings();
                $account->deleteProcessingLocks();
                $account->deleteInstance();

                throw $e;
            }

            $account->deleteProcessings();
            $account->deleteProcessingLocks();
            $account->deleteInstance();

            $deleted++;
        }

        $tempString = Mage::helper('M2ePro')->__('%amount% record(s) were successfully deleted.', $deleted);
        $deleted && $this->_getSession()->addSuccess($tempString);

        $tempString  = Mage::helper('M2ePro')->__('%amount% record(s) are used in M2E Pro Listing(s).', $locked) . ' ';
        $tempString .= Mage::helper('M2ePro')->__('Account must not be in use to be deleted.');
        $locked && $this->_getSession()->addError($tempString);

        $this->_redirect('*/*/index');
    }

    //########################################
}