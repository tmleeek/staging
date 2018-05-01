<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Block_Adminhtml_Client_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('client_form', array(
            'legend'=>Mage::helper('glsbox')->__('Daten')
        ));

        $fieldset->addField('display_name', 'text', array(
            'name'      => 'display_name',
            'label'     => Mage::helper('glsbox')->__('Sender'),
        ));

        $fieldset->addField('kundennummer', 'text', array(
            'name'      => 'kundennummer',
            'label'     => Mage::helper('glsbox')->__('Customer number'),
        ));			

        $fieldset->addField('customerid', 'text', array(
            'name'      => 'customerid',
            'label'     => Mage::helper('glsbox')->__('Customer Id'),
        ));		

        $fieldset->addField('contactid', 'text', array(
            'name'      => 'contactid',
            'label'     => Mage::helper('glsbox')->__('Contact Id'),
        ));
		
        $fieldset->addField('depotnummer', 'text', array(
            'name'      => 'depotnummer',
            'label'     => Mage::helper('glsbox')->__('Depot number'),
        ));

		$fieldset->addField('depotcode', 'text', array(
            'name'      => 'depotcode',
            'label'     => Mage::helper('glsbox')->__('Depot code'),
        ));
		
        $fieldset->addField('notes', 'textarea', array(
            'name'      => 'notes',
            'style'     => 'height:200px',
            'label'     => Mage::helper('glsbox')->__('Note'),
			'note'      => Mage::helper('glsbox')->__('Interne Notizen zu diesem Depot zu besseren späteren Identifikation'),
        ));

		$fieldset->addField('numcircle_standard_start', 'text', array(
            'name'      => 'numcircle_standard_start',
            'label'     => Mage::helper('glsbox')->__('number range start'),
			'note'      => Mage::helper('glsbox')->__('ATTENTION: please fill in the range of numbers only one time. The beginning values of the number range counts automatically after generating a label'),
        ));
        
        $fieldset->addField('numcircle_standard_end', 'text', array(
            'name'      => 'numcircle_standard_end',
            'label'     => Mage::helper('glsbox')->__('number range end'),
			'note'      => Mage::helper('glsbox')->__('ATTENTION: please fill in the range of numbers only one time.'),
        ));
        
        $fieldset->addField('numcircle_express_start', 'text', array(
            'name'      => 'numcircle_express_start',
            'label'     => Mage::helper('glsbox')->__('number range express start'),
			'note'      => Mage::helper('glsbox')->__('ATTENTION: please fill in the range of numbers only one time. The beginning values of the number range counts automatically after generating a label'),
        ));
        
        $fieldset->addField('numcircle_express_end', 'text', array(
            'name'      => 'numcircle_express_end',
            'label'     => Mage::helper('glsbox')->__('number range express end'),
			'note'      => Mage::helper('glsbox')->__('ATTENTION: please fill in the range of numbers only one time.'),
        ));
		$fieldset->addField('status', 'select', array(
		  'label'     => Mage::helper('glsbox')->__('Status'),
		  'name'      => 'status',
		  'note'      => 'Wenn aktiv steht Ihnen dieses Depot zum Versand zur Auswahl',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('glsbox')->__('Aktiv'),
			  ),

			  array(
				  'value'     => 0,
				  'label'     => Mage::helper('glsbox')->__('Inaktiv'),
			  ),
		  ),
	  ));
    
    Mage::dispatchEvent('glsbox_adminhtml_edit_prepare_form', array('block'=>$this, 'form'=>$form));

    if (Mage::registry('client_data')) {
    	$form->setValues(Mage::registry('client_data')->getData());
    }
	return parent::_prepareForm();
    }
}