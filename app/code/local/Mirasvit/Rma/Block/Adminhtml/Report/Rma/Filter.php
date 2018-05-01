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
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rma_Block_Adminhtml_Report_Rma_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/sales');
        $form = new Varien_Data_Form(
            array('id' => 'filter_form', 'action' => $actionUrl, 'method' => 'get')
        );
        $htmlIdPrefix = 'mst_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('reports')->__('Filter')));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('store_ids', 'hidden', array(
            'name'  => 'store_ids'
        ));

        $fieldset->addField('period_type', 'select', array(
            'name' => 'period_type',
            'options' => array(
                'day'   => Mage::helper('reports')->__('Day'),
                'month' => Mage::helper('reports')->__('Month'),
                'year'  => Mage::helper('reports')->__('Year')
            ),
            'label' => Mage::helper('reports')->__('Period'),
            'title' => Mage::helper('reports')->__('Period')
        ));

        $fieldset->addField('report_type', 'select', array(
            'name'      => 'report_type',
            'options'   => array(
                'all'   => Mage::helper('reports')->__('Totals'),
                'by_product' => Mage::helper('reports')->__('By product'),
            ),
            'label'     => Mage::helper('reports')->__('Report Type'),
            'title'     => Mage::helper('reports')->__('Report Type')
        ));

        $fieldset->addField('from', 'date', array(
            'name'      => 'from',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('From'),
            'title'     => Mage::helper('reports')->__('From'),
            'required'  => true
        ));

        $fieldset->addField('to', 'date', array(
            'name'      => 'to',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('To'),
            'title'     => Mage::helper('reports')->__('To'),
            'required'  => true
        ));

        $fieldset->addField('show_empty_rows', 'select', array(
            'name'      => 'show_empty_rows',
            'options'   => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'label'     => Mage::helper('reports')->__('Empty Rows'),
            'title'     => Mage::helper('reports')->__('Empty Rows')
        ));

        $form->addValues($this->getFilterData()->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /************************/

}