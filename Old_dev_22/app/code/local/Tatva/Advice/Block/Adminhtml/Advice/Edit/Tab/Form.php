<?php

class Tatva_Advice_Block_Adminhtml_Advice_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('advice_form', array('legend'=>Mage::helper('advice')->__('Item information')));

        $fieldset->addField('advice_text', 'editor', array(
            'name'      => 'advice_text',
            'label'     => Mage::helper('advice')->__('Advice'),
            'title'     => Mage::helper('advice')->__('Advice'),
            'style'     => 'width:700px; height:200px;',
            'wysiwyg'   => true,
            'required'  => true,
        ));

        $fieldset->addField('material', 'multiselect', array(
            'label'     => Mage::helper('advice')->__('Material'),
            'required'  => true,
            'name'      => 'material[]',
            'values'    => $this->_getMaterial(),
  	    ));

         if (!Mage::app()->isSingleStoreMode()) {
          $fieldset->addField('store_id', 'multiselect',
                  array (
                          'name' => 'store_id[]',
                          'label' => Mage::helper('cms')->__('Store view'),
                          'title' => Mage::helper('cms')->__('Store view'),
                          'required' => true,
                          'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true) ));
      }
      else {
          $fieldset->addField('store_id', 'hidden', array (
                  'name' => 'store_id[]',
                  'value' => Mage::app()->getStore(true)->getId() ));
          $fieldset->setStoreId(Mage::app()->getStore(true)->getId());
      }

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('advice')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('advice')->__('Enabled'),
                ),

                array(
                    'value'     => 2,
                    'label'     => Mage::helper('advice')->__('Disabled'),
                ),
            ),
        ));

        if ( Mage::getSingleton('adminhtml/session')->getAdviceData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getAdviceData());
            Mage::getSingleton('adminhtml/session')->setAdviceData(null);
        } elseif ( Mage::registry('advice_data') ) {
            $form->setValues(Mage::registry('advice_data')->getData());
        }
        return parent::_prepareForm();
    }

    public function _getMaterial()
    {
       $option_arr = array();
       $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'materiaux');
       foreach ($attribute->getSource()->getAllOptions(false) as $option) {
           $option_arr[] = array('value'=>$option['value'],'label'=>$option['label']);
        }
        return  $option_arr;
    }

    /*array(
        '-1'=> array( 'label' => 'Please Select..', 'value' => '-1'),
        '1' => array( 'label' => 'Steel' , 'value'=>'Steel'),
        '2' => array( 'label' =>'Iron' , 'value'=>'Iron' ),
        '3' => array( 'label' =>'Aluminium' , 'value'=>'Aluminium' ),
        '4' => array( 'label' =>'Copper' , 'value'=>'Copper' ),
        '5' => array( 'label' =>'Nickel' , 'value'=>'Nickel' ),
        '6' => array( 'label' =>'Plastic' , 'value'=>'Plastic' ),
        '7' => array( 'label' =>'Glass' , 'value'=>'Glass' ),
        '8' => array( 'label' =>'Inox 18%' , 'value'=>'Inox 18%' )
      ),*/

}