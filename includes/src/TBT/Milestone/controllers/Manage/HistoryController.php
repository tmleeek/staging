<?php

class TBT_Milestone_Manage_HistoryController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->renderLayout();

        return $this;
    }

    public function viewAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('tbtmilestone/rule_log');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError($this->__("This milestone's history no longer exists"));
                $this->_redirect('*/*');

                return $this;
            }
        }

        Mage::register('current_milestone_rule_log', $model);

        $this->loadLayout();

        $editBlock     = $this->getLayout()->createBlock('tbtmilestone/manage_history_view');
        $editTabsBlock = $this->getLayout()->createblock('tbtmilestone/manage_history_view_tabs');

        $this->_addContent($editBlock)
            ->_addLeft($editTabsBlock);

        $this->renderLayout();

        return $this;
    }
}
