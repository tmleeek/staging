<?php

class TBT_Milestone_Manage_RuleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->renderLayout();
        return $this;
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        try {
            Mage::getModel('tbtmilestone/rule')->setData($data)->save();
        } catch (Exception $ex) {
            $this->_getSession()->addError($ex->getMessage());

            if (isset($data['rule_id'])) {
                $this->_redirect('*/*/edit', array('id' => $data['rule_id']));
            } else {
                $this->_redirect('*/*/new');
            }

            return $this;
        }

        $this->_getSession()->addSuccess($this->__("Successfully saved milestone."));
        $this->_redirect('*/*');

        return $this;
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('tbtmilestone/rule');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError($this->__("This milestone no longer exists"));
                $this->_redirect('*/*');
                return $this;
            }
        }

        Mage::register('current_milestone_rule', $model);

        $this->loadLayout();

        $block = $this->getLayout()->createBlock('tbtmilestone/manage_rule_edit');
        $block->setData('action', $this->getUrl('*/*/save'));
        $this->_addContent($block);

        $this->renderLayout();

        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
        return $this;
    }
}
