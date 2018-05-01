<?php

class TBT_Milestone_Block_Manage_History_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('milestone_history_view_form');
        $this->setTitle($this->__("Milestone Information"));

        return $this;
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'     => 'view_form',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
