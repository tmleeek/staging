<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Common_GeneralController
    extends Ess_M2ePro_Controller_Adminhtml_Common_MainController
{
    //########################################

    protected function _initAction()
    {
        $this->loadLayout()
            ->_title(Mage::helper('M2ePro')->__('Configuration'))
            ->_title(Mage::helper('M2ePro')->__('General'));

        $this->getLayout()->getBlock('head')
            ->addJs('M2ePro/Common/Configuration/GeneralHandler.js');

        $this->setComponentPageHelpLink('General');

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isLoggedIn();
    }

    //########################################

    public function indexAction()
    {
        if (!Mage::helper('M2ePro/Component_Amazon')->isActive()) {
            $this->_redirect('*/adminhtml_common_synchronization/index');
        }

        $this->_initAction()
            ->_addContent(
                $this->getLayout()->createBlock(
                    'M2ePro/adminhtml_common_configuration', '',
                    array('active_tab' => Ess_M2ePro_Block_Adminhtml_Common_Configuration_Tabs::TAB_ID_GENERAL)
                )
            )->renderLayout();
    }

    //########################################

    public function searchAutocompleteAction()
    {
        $model       = $this->getRequest()->getParam('model');
        $component   = $this->getRequest()->getParam('component');
        $queryString = $this->getRequest()->getParam('query');
        $maxResults  = (int) $this->getRequest()->getParam('maxResults');

        if (!$model || !$component || !$queryString || !$maxResults) {
            return $this->getResponse()->setBody(Mage::helper('M2ePro')->jsonEncode(array()));
        }

        $where = array();
        $parts = explode(' ', $queryString);
        foreach ($parts as $part) {
            $part = trim($part);
            if (!$part) {
                continue;
            }
            $where[]['like'] = "%$part%";
        }

        if (empty($where)) {
            return $this->getResponse()->setBody(Mage::helper('M2ePro')->jsonEncode(array()));
        }

        $quotedQueryString = addslashes(trim($queryString));

        $relevanceQueryString  = "IF( `main_table`.`title` LIKE '%". $quotedQueryString. "%', ";
        $relevanceQueryString .= substr_count($quotedQueryString, " ") + 1;
        $relevanceQueryString .= "*3, 0) + IF( `main_table`.`title` LIKE '%";
        $relevanceQueryString .= str_replace(" ", "%', 1, 0) + IF( `main_table`.`title` LIKE '%", $quotedQueryString);
        $relevanceQueryString .= "%', 1 , 0)";

        $collection = Mage::helper('M2ePro/Component')
            ->getComponentModel($component, $model)
            ->getCollection()
            ->addFieldToFilter("main_table.title", $where)
            ->setOrder('relevance', 'DESC');

        $collection->getSelect()->columns(array('relevance' => new Zend_Db_Expr($relevanceQueryString)));

        $quantity = $collection->getSize();
        $collection->getSelect()->limit($maxResults);
        $results = $collection->getData();

        $suggestions = array();
        $ids         = array();

        foreach ($results as $result) {
            $suggestions[] = $result['title'];
            $ids[] = $result['id'];
        }
        $array = array(
            'query'       => $queryString,
            'suggestions' => $suggestions,
            'data'        => $ids,
            'quantity'    => $quantity
        );
        return $this->getResponse()->setBody(Mage::helper('M2ePro')->jsonEncode($array));
    }

    //########################################
}