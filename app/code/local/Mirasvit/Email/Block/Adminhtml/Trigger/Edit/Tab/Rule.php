<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Block_Adminhtml_Trigger_Edit_Tab_Rule extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return __('Rules');
    }

    public function getTabTitle()
    {
        return __('Rules');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl(Mage::getModel('adminhtml/url')->getUrl('*/adminhtml_trigger/newConditionHtml/form/run_conditions_fieldset', array('rule_type' => $model->getType())));

        $fieldset = $form->addFieldset('run_fieldset', array('legend' => __('Allow Rules')))
            ->setRenderer($renderer);

        $rule = $model->getRunRule();
        $fieldset->addField('run_conditions', 'text', array(
            'name'     => 'run_conditions',
            'label'    => __('Rules'),
            'title'    => __('Rules'),
            'required' => true,
        ))->setRule($rule)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl(Mage::getModel('adminhtml/url')->getUrl('*/adminhtml_trigger/newConditionHtml/form/stop_conditions_fieldset', array('rule_type' => $model->getType())));

        $fieldset = $form->addFieldset('stop_fieldset', array('legend' => __('Deny Rules')))
            ->setRenderer($renderer);

        $rule = $model->getStopRule();
        $fieldset->addField('stop_conditions', 'text', array(
            'name'     => 'stop_conditions',
            'label'    => __('Rules'),
            'title'    => __('Rules'),
            'required' => true,
        ))->setRule($rule)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}

