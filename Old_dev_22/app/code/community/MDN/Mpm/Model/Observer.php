<?php

class MDN_Mpm_Model_Observer extends Mage_Core_Model_Abstract
{

    /**
     * Generate the full catalog file
     */
    public function exportCatalog()
    {
        if (Mage::getStoreConfig('mpm/catalog_export/enable_cron'))
            Mage::helper('Mpm/Export')->ExportCatalog();
    }

    public function importOffers()
    {
        if (Mage::getStoreConfig('mpm/offers_import/enable_cron'))
            Mage::helper('Mpm/Product')->synchronizeAllOffers();
    }

    public function indexAllRules()
    {
        $rules = Mage::getModel('Mpm/Rule')->getCollection();
        foreach ($rules as $rule) {
            $rule->indexProducts();
        }
    }

    public function calculateAllPrices()
    {
        if (Mage::getStoreConfig('mpm/repricing/enable'))
            Mage::helper('Mpm/Product')->repriceAll();
    }

    public function importPricing()
    {
        if (Mage::getStoreConfig('mpm/repricing/enable')) {
            if (!Mage::getStoreConfig('mpm/repricing/test_mode'))
                Mage::helper('Mpm/PricingImport')->importAll();
        }
    }

    public function cleanAttributes()
    {
        if (Mage::getStoreConfig('mpm/repricing/enable_clean_attributes')) {
            Mage::helper('Mpm/Attribute_Cleaner')->cleanAttributesForMissingProducts();
        }
    }

    public function updateStatistics()
    {
        Mage::getSingleton('Mpm/Stat')->run();
    }

    public function queue()
    {
        Mage::helper('Mpm/PricingQueue')->playTasks();
    }

    public function configuration_changed(Varien_Event_Observer $observer)
    {

        foreach (Mage::Helper('Mpm/Configuration')->getSubscribedChannelsWithApi() as $channelCode => $channelLabel) {

            if (preg_match('#amazon#', $channelCode)) {

                $data = array();
                $data['MERCHANT_ID'] = Mage::getStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_merchant_id');
                $data['AWS_ACCESS_KEY_ID'] = Mage::getStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_access_key');
                $data['AWS_SECRET_ACCESS_KEY'] = Mage::getStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_secret_key');

                Mage::helper('Mpm/Carl')->setWebserviceCredentials($channelCode, $data);

            } elseif (preg_match('#fnac#', $channelCode)) {

                $data = array();
                $data['PARTNER_ID'] = Mage::getStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_partner_id');
                $data['SHOP_ID'] = Mage::getStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_shop_id');
                $data['KEY'] = Mage::getStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_key');

                Mage::helper('Mpm/Carl')->setWebserviceCredentials($channelCode, $data);

            } else {

                $data = array();
                $data['LOGIN'] = Mage::getStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_login');
                $data['PASSWORD'] = Mage::getStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_password');

                Mage::helper('Mpm/Carl')->setWebserviceCredentials($channelCode, $data);

            }

        }

        foreach (Mage::helper('Mpm/Carl')->getChannelsSubscribed() as $channel) {

            $sellerId = Mage::getStoreConfig('mpm/repricing/seller_id_' . $channel->channelCode);

            if ($sellerId) {

                list($sellerIdChannelCode, $sellerIdSellerReference) = explode(':', $sellerId);
                $code = 'CLIENT-DATA.' . strtoupper($channel->organization) . '.' . strtoupper($channel->locale) . '.SELLER-REFERENCE';
                $source = '<?php return "'.$sellerIdSellerReference.'";';
                Mage::Helper('Mpm/Carl')->postRule($code, $source, true);

            }

        }

        foreach (Mage::Helper('Mpm/Carl')->getFieldsToMap() as $field) {

            $value = Mage::getStoreConfig('mpm/mapping/' . strtolower($field));

            Mage::helper('Mpm/Carl')->postRule(
                'CLIENT-DATA.MAPPING.PRODUCT.' . $field,
                sprintf('<?php return "%s";', $value)
            );

        }

        Mage::getSingleton('core/session')->setData('carl_token', null);

    }

    public function configuration_edit()
    {
        try {
            if (Mage::getSingleton('adminhtml/config_data')->getSection() === 'mpm') {
                Mage::helper('Mpm/Product')->pricingInProgress();
            }
        } catch (\Exception $e) {
            // Account does not configure
        }
    }

    public function productChanged($observer)
    {
        if (!Mage::getStoreConfig('mpm/catalog_export/enable_single_update'))
            return $this;

        $fieldsUsed = Mage::helper('Mpm/Carl')->getFieldsUsed();
        $product = $observer->getEvent()->getProduct();
        if ($product->hasDataChanges()) {
            foreach ($product->getAttributes() as $attribute) {
                $attribute = $attribute->getName();
                if (!in_array($attribute, $fieldsUsed)) {
                    continue;
                }

                if (!is_array($product->getData($attribute))) {
                    if ($product->getData($attribute) != $product->getOrigData($attribute)) {
                        Mage::helper('Mpm/PricingQueue')->addTask($product->getId());
                    }
                }
            }
        }

        return $this;
    }

}