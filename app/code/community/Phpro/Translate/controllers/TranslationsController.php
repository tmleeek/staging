<?php

class Phpro_Translate_TranslationsController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $logging_enabled = Mage::getStoreConfig('translate/general/translation_logging');
        $logging_interfaces = Mage::getStoreConfig('translate/general/translation_interfaces');
        $logging_locales = Mage::getStoreConfig('translate/general/locales');
        $logging_groups = Mage::getStoreConfig('translate/general/customer_groups');

        if (!$logging_enabled || $logging_interfaces == '' || $logging_locales == '' || $logging_groups = '') {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('translate')
                            ->__('Logging of untranslated strings is disabled or not properly configured. If you wish to enable logging of untranslated strings for this Magento installation, please navigate to "System > Configuration > PHPro Translate", enable the logging and select a value for all properties.'));
        }

        $this->loadLayout();

        $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('translate/adminhtml_translate'))
                ->_addLeft($this->getLayout()->createBlock('translate/adminhtml_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('translate/translate')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            if (Mage::registry('translate_data')) {
                Mage::unregister('translate_data');
            }
            Mage::getSingleton('adminhtml/session')->setTranslateId($id);

            Mage::register('translate_data', $model);

            $this->loadLayout();

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()
                    ->getBlock('head')
                    ->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('translate/adminhtml_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translate')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        //write data away to core_translate table
        $resource = Mage::getResourceModel('core/translate_string');

        $request = $this->getRequest();
        $translate_id = $request->getParam('id');
        $original = $request->getParam('original_translation');
        $namespace = $request->getParam('namespace');
        $module = $request->getParam('module');
        $custom = $request->getParam('string');
        $locale = $request->getParam('locale');
        $storeId = $request->getParam('storeid');
        $storeViewSpecific = $request->getParam('storeview_specific');

        if ($namespace != '') {
            $explode = explode('::', $original);
            if (count($explode) > 1) {
                $original = $explode[0] . '::' . $explode[1];
            } else {
                $original = $namespace . '::' . $original;
            }
        }

        if ($storeId != 0 && $storeViewSpecific != 1) {
            $storeId = 0;
        }
        $resource->saveTranslate($original, $custom, $locale, $storeId);

        //delete record from phpro table
        $advancedTranslateRecord = Mage::getModel('translate/translate');
        $advancedTranslateRecord->setId($translate_id)->delete();

        //clear the cache
        Mage::app()->getCache()->clean();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')
                        ->__('Translation was saved.'));

        if ($request->getParam("back") == "edit") {
            $idArray = Mage::getSingleton('adminhtml/session')->getIdArray();
            if ($idArray) {
                $urlId = $request->getParam('id');
                $index = array_search($urlId, $idArray);

                if ($index) {
                    array_splice($idArray, $index, 1); // remove current item
                } else {
                    $index = 0;
                }
                $id = $idArray[$index];

                $this->_redirect("*/*/edit/id/$id");
            } else {
                $this->_redirect('*/*/', array('active_tab' => 'list_untranslated'));
            }
        } else {
            $this->_redirect('*/*/', array('active_tab' => 'list_untranslated'));
        }
    }

    /**
     * AJAX request for untranslated grid 
     */
    public function gridAction()
    {
        $this->getResponse()
                ->setBody(
                        $this->getLayout()
                        ->createBlock('translate/adminhtml_grid')
                        ->toHtml()
        );
    }

    /**
     * AJAX request for string search 
     */
    public function searchAction()
    {
        $cache = $this->getRequest()->getParam('cache');
        if (!$cache) {
            $string = $this->getRequest()->getParam('q');
            $keysearch = $this->getRequest()->getParam('keysearch');
            $untranslatedsearch = $this->getRequest()->getParam('untranslatedsearch');
            $case = $this->getRequest()->getParam('case');
            $modules = $this->getRequest()->getParam('modules');
            $interface = $this->getRequest()->getParam('interface');
            $locale = $this->getRequest()->getParam('locale');

            $result = Mage::getModel('translate/translator')->search($string, $case, $modules, $interface, $locale, $keysearch, $untranslatedsearch);

            $cache = Mage::getModel('core/cache');
            $cache->save($string, 'translate_search_string', array('translate_cache'), null);
            $cache->save(serialize($result), 'translate_search_result', array('translate_cache'), null);
            $cache->save('asc', 'translate_search_order', array('translate_cache'), null);
            Mage::getSingleton('core/session')->setTranslateSearchCache(true);
        } else {
            $result = unserialize(Mage::getModel('core/cache')->load('translate_search_result'));
        }

        $this->loadLayout();

        $response = array();
        if (empty($result)) {
            $response['records'] = Mage::helper('translate')->__("No results found.");
        } else if ($result == "over") {
            $response['records'] = Mage::helper('translate')->__("The search returned too many results. Please narrow your search criteria.");
        } else {
            $response['records'] = $this->getLayout()
                    ->createBlock('translate/adminhtml_translateResult')
                    ->setResults($result)
                    ->setTemplate('translate/translateresult.phtml')
                    ->toHtml();
        }

        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    public function truncateAction()
    {
        $collection = Mage::getModel('translate/translate')->getCollection();
        foreach ($collection as $item)
        {
            $item->delete();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Table truncated'));

        $this->_redirect('*/*/');
    }

    public function removeDuplicateAction()
    {
        $collection = Mage::getModel('translate/translate')->getCollection();

        $results = Mage::getModel('core/translate')->removeDuplicatesInTable();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Following duplicates are found and removed:') . '<ul>' . implode($results, '\n') . '</ul>');
        $this->_redirect('*/*/');
    }

    public function sortAction()
    {
        $cache = Mage::getModel('core/cache');
        $column = $this->getRequest()->getParam('column');
        $order = $cache->load('translate_search_order');
        $results = unserialize(Mage::getModel('core/cache')->load('translate_search_result'));

        switch ($column) {
            case 'origin':
                $keys = array_keys($results);
                $locale = array();
                foreach ($keys as $key => $value)
                { // strip locale code away
                    $locale[$key] = substr($value, 0, strpos($value, ':'));
                    $keys[$key] = substr($value, (strpos($value, ':') + 1));
                }

                switch ($order) {
                    case 'desc-origin':
                    case 'desc':
                        asort($keys);
                        $keys = array_reverse($keys, true);
                        $cache->save('asc-origin', 'translate_search_order', array('translate_cache'), null);
                        break;
                    default: // catches all other, including 'asc'
                        asort($keys);
                        $cache->save('desc-origin', 'translate_search_order', array('translate_cache'), null);
                        break;
                }

                foreach ($keys as $key => $value)
                {
                    $sortedKey = $locale[$key] . ':' . $value;
                    $temp[$sortedKey] = $results[$sortedKey];
                }
                $results = $temp;

                break;
            case 'source':

                function compare($x, $y)
                {
                    if (strcmp($x['source'], $y['source']) == 0) {
                        return 0;
                    } else if (strcmp($x['source'], $y['source']) < 0) {
                        return -1;
                    } else
                        return 1;
                }

                switch ($order) {
                    case 'desc-source':
                    case 'desc':
                        uasort($results, 'compare');
                        $results = array_reverse($results, true);
                        $cache->save('asc-source', 'translate_search_order', array('translate_cache'), null);
                        break;
                    default: // catches all other, including 'asc'
                        uasort($results, 'compare');
                        $cache->save('desc-source', 'translate_search_order', array('translate_cache'), null);
                        break;
                }

                break;
            case 'locale':
                $keys = array_keys($results);
                switch ($order) {
                    case 'desc-locale':
                    case 'desc':
                        sort($keys);
                        $keys = array_reverse($keys);
                        $cache->save('asc-locale', 'translate_search_order', array('translate_cache'), null);
                        break;
                    default: //catches all, also 'asc'
                        asort($keys);
                        $cache->save('desc-locale', 'translate_search_order', array('translate_cache'), null);
                        break;
                }

                foreach ($keys as $key)
                {
                    $temp[$key] = $results[$key];
                }
                $results = $temp;

                break;
            case 'translate':
            default:
                switch ($order) {
                    case 'desc-translate':
                    case 'desc':
                        asort($results);
                        $results = array_reverse($results, true);
                        $cache->save('asc-translate', 'translate_search_order', array('translate_cache'), null);
                        break;
                    default: //catches all, also 'asc'
                        asort($results);
                        $cache->save('desc-translate', 'translate_search_order', array('translate_cache'), null);
                        break;
                }
                break;
        }

        $response['records'] = $this->getLayout()
                ->createBlock('translate/adminhtml_translateResult')
                ->setResults($results)
                ->setTemplate('translate/translateresult.phtml')
                ->toHtml();

        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

}
