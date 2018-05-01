<?php
/**
 * This class is a re-implemenation of Mage_Adminhtml_Model_System_Config_Source_Email_Template 
 * with a single change on line 33 to load a dynamic template location rather than a static one
 * 
 * This basically lets us re-use the same default locale email template on multiple sources.
 * I assume this is how Magento intended to build this source model. It's probably a bug that they didn't. 
 *
 * Also adding an option to pick no template at all.
 */
class TBT_Milestone_Model_System_Config_Source_Email_Template extends Mage_Adminhtml_Model_System_Config_Source_Email_Template
{
    /**
     * Config xpath to email template node
     *
     */
    const XML_PATH_TEMPLATE_EMAIL = 'global/template/email/';

    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        if(!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('core/email_template_collection')
                ->load();

            Mage::register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateName = Mage::helper('adminhtml')->__('Default Template from Locale');
        $nodeName = Mage::app()->getConfig()->getNode("default/{$this->getPath()}_default"); // This is the only difference with parent class
        $templateLabelNode = Mage::app()->getConfig()->getNode(self::XML_PATH_TEMPLATE_EMAIL . $nodeName . '/label');
        if ($templateLabelNode) {
            $templateName = Mage::helper('adminhtml')->__((string)$templateLabelNode);
            $templateName = Mage::helper('adminhtml')->__('%s (Default Template from Locale)', $templateName);
        }
        array_unshift(
            $options,
            array(
                'value'=> $nodeName,
                'label' => $templateName
            )
        );
        
        /* This was also added to allow disabling emails as well: */
        array_unshift(
            $options,
            array(
                 'value' => 'none',
                 'label' => "(" . Mage::helper('tbtmilestone')->__("disable these emails") .")"
            )
        );
        
        return $options;
    }

}
