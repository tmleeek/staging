<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Observer_Magento_Configuration_Init extends Ess_M2ePro_Model_Observer_Abstract
{
    //########################################

    public function canProcess()
    {
        return Mage::helper('M2ePro/Module')->isDisabled();
    }

    // ---------------------------------------

    public function process()
    {
        /** @var Varien_Simplexml_Config $config */
        $config = $this->getEvent()->getData('config');
        $sections = $config->getXpath('//sections/*[@module="M2ePro"]');

        if (!$sections) {
            return;
        }

        foreach ($sections as $section) {

            if ($section->tab != 'm2epro') {
                continue;
            }

            $dom = dom_import_simplexml($section);
            $dom->parentNode->removeChild($dom);
        }

        $tab = $config->getNode('tabs/m2epro');

        if ($tab && $tab instanceof SimpleXMLElement) {
            $dom = dom_import_simplexml($tab);
            $dom->parentNode->removeChild($dom);
        }
    }

    //########################################
}