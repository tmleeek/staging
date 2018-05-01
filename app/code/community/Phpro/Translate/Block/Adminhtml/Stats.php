<?php

class Phpro_Translate_Block_Adminhtml_Stats extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareLayout() {
        return parent::_prepareLayout();
    }

    protected function _prepareForm() {
        $logNotice = false;
        $form = new Varien_Data_Form(
                        array('id' => 'search_form',
                            'action' => $this->getUrl('*/*/search', array('id' => $this->getRequest()->getParam('id'))),
                            'method' => 'post')
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('translate_form', array('legend' => Mage::helper('translate')->__('Translation Statistics')));

        $translator = Mage::getModel("translate/translator");
        $existing = array();

        foreach (Mage::app()->getStores() as $store) {
            $locale = Mage::app()->getStore($store->getId())->getConfig('general/locale/code');

            if (in_array($locale, $existing)) { //prevent crash with multiple identical locales
                continue;
            }
            $existing[] = $locale;

            $statsFront = $translator->getStatistics($locale, 'frontend');
            $statsAdmin = $translator->getStatistics($locale, 'adminhtml');

            $stats['module'] = $statsFront['module'] + $statsAdmin['module'];
            $stats['theme'] = $statsFront['theme'] + $statsAdmin['theme'];
            $stats['database'] = $statsFront['database'] + $statsAdmin['database'];
            $stats['untranslated'] = $statsFront['untranslated'] + $statsAdmin['untranslated'];
            $stats['total'] = $stats['module'] + $stats['theme'] + $stats['database'];

            $percentage = round(($stats['total'] / ($stats['total'] + $stats['untranslated'])) * 100, 2);
            if ($percentage == 100) {
                $logNotice = true;
            }

            $fieldset->addField($locale, 'note', array(
                'label' => '<strong>' . Mage::helper('translate')->__("Translated strings in %s", $locale) . '</strong>',
                'class' => 'button',
                'required' => false,
                'text' => $percentage . '%<br />' .
                Mage::helper('translate')->__('%d strings in modules', $stats['module']) . '<br />' .
                Mage::helper('translate')->__('%d strings in theme', $stats['theme']) . '<br />' .
                Mage::helper('translate')->__('%d strings in database', $stats['database']) . '<br />' .
                Mage::helper('translate')->__('%d untranslated strings', $stats['untranslated'])
            ));
            //}


            if ($logNotice) {
                $fieldset->addField('log-notice', 'note', array(
                    'label' => '<strong>' . Mage::helper('translate')->__('Attention! 100% translation can mean either a fully translated locale or simply no logged untranslated strings!') . '</strong>'
                ));
            }

            $fieldset->addField('cache-notice', 'note', array(
                'label' => Mage::helper('translate')->__('This data is cached. Please flush the Magento cache to refresh this data.')
            ));

            return parent::_prepareForm();
        }
    }

}
