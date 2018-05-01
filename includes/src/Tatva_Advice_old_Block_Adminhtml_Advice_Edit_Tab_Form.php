<?php

class Tatva_Advice_Block_Adminhtml_Advice_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('advice_form', array('legend'=>Mage::helper('advice')->__('Item information')));

        $fieldset->addField('advice_fr', 'editor', array(
            'name'      => 'advice_fr',
            'label'     => Mage::helper('advice')->__('Advice In France'),
            'title'     => Mage::helper('advice')->__('Advice In France'),
            'style'     => 'width:700px; height:200px;',
            'wysiwyg'   => false,
            'required'  => true,
        ));

        $fieldset->addField('advice_en', 'editor', array(
            'name'      => 'advice_en',
            'label'     => Mage::helper('advice')->__('Advice In English'),
            'title'     => Mage::helper('advice')->__('Advice In English'),
            'style'     => 'width:700px; height:200px;',
            'wysiwyg'   => false,
            'required'  => true,
        ));

        $fieldset->addField('material', 'multiselect', array(
            'label'     => Mage::helper('advice')->__('Material'),
            'required'  => true,
            'name'      => 'material',
            'values'    => $this->_getMaterial(),
  	    ));

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
        $material = Mage::getModel('material/material')->getCollection()->addFieldToFilter('status', array('eq' => 1));

        $product_material = array();
        $final_material = array();

        foreach($material as $_material)
        {
            $_material = explode(',',$_material->getMaterial());

            foreach($_material as $_mat)
            {
                $product_material[] = $_mat;
            }
        }

        $product_material = array_unique($product_material);
        $final_material = array('-1' =>  array( 'label' => Mage::helper('advice')->__('Please Select..'), 'value' => '-1'));

        foreach($product_material as $_product_material)
        {
            $final_material[] = array( 'label' => $_product_material, 'value' => $_product_material);
        }
        return $final_material;
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