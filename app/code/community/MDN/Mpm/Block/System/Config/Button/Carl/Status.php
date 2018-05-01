<?php

class MDN_Mpm_Block_System_Config_Button_Carl_Status extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $steps = array('subscription' => 'Subscription',
                            'client_subscribed' => 'Initial catalog export',
                            'catalog_submited' => 'Fields mapping',
                            'mapping_defined' => 'Rule configuration',
                            'setup_complete' => 'Setup complete');
        $currentStepPast = false;

        $status = $this->getStatus();
        $status = $status === 'matching_processed' ? 'mapping_defined' : $status;
        if ($status === 'mapping_defined') {
            $rulesDefined = Mage::helper('Mpm/Carl')->getConfigurationStatus();
            if(empty($rulesDefined->rules_errors)) {
                $status = 'setup_complete_after';
            }
        }

        $html = '<table border="0" width="700px"><tr>';
        foreach($steps as $k => $v)
        {
            $isOk = (!$currentStepPast && ($status != $k));
            $html .= '<td align="center" width="100px"><img width="32" height="32" src="'.$this->getSkinUrl('Mpm/images/carl-step-'.($isOk ? 'ok' : 'nok').'.png').'"><br><b>'.$this->__($v).'<b></td>';
            if ($k == $status)
                $currentStepPast = true;
        }
        $html .= "</tr></table>";

        return $html;

    }

    protected function getStatus()
    {

        if (!Mage::helper('Mpm/Carl')->checkCredentials())
        {
            return 'subscription';
        }

        $status = Mage::helper('Mpm/Carl')->getUserStatus();
        return $status;
    }

}