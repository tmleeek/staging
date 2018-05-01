<?php

class Phpro_Translate_Model_Translator extends Mage_Core_Model_Translate {

    protected $searchLocale;
    protected $searchStoreId;
    protected $searchInterface;
    protected $searchModules;
    protected $_helper;

    /**
     * Advanced translation array (used for advanced search)
     *
     * @var array
     *
     * TODO: not used for moment, check if needed or delete them
     */
    protected $_advancedData = array();
    protected $_advancedSearchLocale;

    public function _construct() {
        parent::_construct();
        $this->_init('translate/translator');
    }

    /**
     * @codeCoverageIgnore
     */
    protected function _helper() {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('translate');
        }
        return $this->_helper;
    }

    public function search($string, $case, $modules, $interface, $locale, $original=false, $untranslatedsearch = false) {
        $results = array();
        $searchPattern = (isset($case)) ? "^" . $string . "^" : "^(?i)" . $string . "^";

        // No search when string is empty;
        if ($string == "") {
            return $results;
        }

        $this->setSearchModules($modules);
        $this->setSearchInterface($interface);

        $this->_data = array();

        if (!$untranslatedsearch) {
            //search in Magento Array
            $this->_loadAdvancedTranslations($locale);

            if ($original) { // search in original rather than translation
                $results = $this->_matchOriginalInArray($searchPattern);
            } else {
                $results = $this->_matchTranslationInArray($searchPattern);
            }
        }
        // load untranslated strings
        // TODO: remove duplicates
        $dbResults = $this->_matchStringInDb($string, $locale);
        foreach ($dbResults as $dbResult) {
            $explode = explode('::', $dbResult->getString());
            $translate = (isset($explode[1])) ? $explode[1] : $dbResult->getString();
            $results[$dbResult->getString()][$dbResult->getLocale()] = array('translate' => $translate, 'db_id' => $dbResult->getTranslateId(), 'source' => 'Untranslated String (' . $dbResult->getModule() . ')');
        }

        if (count($results) >= 2000) {
            return "over";
        } else {
            foreach ($results as $key => $_result) {
                foreach ($_result as $locale => $translation) {
                    $newKey = $locale . ':' . $key;
                    $temp[$newKey] = $translation;
                }
            }
            $results = $temp;
        }
        return $results;
    }

    public function getStatistics($locale, $interface) {
        $results = array();
        $stats = array();
        $cache = Mage::getSingleton('core/cache');
        $isCached = $cache->load('translate_data_' . $interface . '_' . $locale);

        if (!$isCached) {
            $this->setSearchModules('all');
            $this->setSearchInterface($interface);

            // count module translations
            $this->_data = array();
            $this->_loadAdvancedStatistics($locale, 'module');
            $stats['module'] = count($this->_data);
            $cache->save($stats['module'], 'translate_module_' . $interface . '_' . $locale, array('translate_cache'), null);

            // count theme translations
            $this->_data = array();
            $this->_loadAdvancedStatistics($locale, 'theme');
            $stats['theme'] = count($this->_data);
            $cache->save($stats['theme'], 'translate_theme_' . $interface . '_' . $locale, array('translate_cache'), null);

            // count database translations
            $this->_data = array();
            $this->_loadAdvancedStatistics($locale, 'database');
            $stats['database'] = count($this->_data);
            $cache->save($stats['database'], 'translate_database_' . $interface . '_' . $locale, array('translate_cache'), null);

            // count untranslated strings
            $this->_data = array();
            $this->_loadAdvancedStatistics($locale, 'database');
            $stats['database'] = count($this->_data);

            $strings = Mage::getModel('translate/translate')->getCollection()
                    ->addFieldToFilter('interface', array('eq' => "$interface"))
                    ->addFieldToFilter('locale', array('eq' => $locale));
            $strings->load();
            $stats['untranslated'] = count($strings);
            $cache->save($stats['untranslated'], 'translate_untranslated_' . $interface . '_' . $locale, array('translate_cache'), null);

            // set cache-flag for isCached
            $cache->save(true, 'translate_data_' . $interface . '_' . $locale, array('translate_cache'), null);
        } else {
            $stats['module'] = $cache->load('translate_module_' . $interface . '_' . $locale);
            $stats['theme'] = $cache->load('translate_theme_' . $interface . '_' . $locale);
            $stats['database'] = $cache->load('translate_database_' . $interface . '_' . $locale);
            $stats['untranslated'] = $cache->load('translate_untranslated_' . $interface . '_' . $locale);
        }

        return $stats;
    }

    private function _loadAdvancedTranslations($locale) {
        if ($locale == 'all') {
            foreach (Mage::getModel('translate/system_config_source_locales')->toArray() as $key => $value) {
                $this->setSearchLocale($key);

                $this->_loadAdvancedModuleTranslation();
                $this->_loadAdvancedThemeTranslation();
                $this->_loadAdvancedDbTranslation();
            }
        } else {
            $this->setSearchLocale($locale);

            $this->_loadAdvancedModuleTranslation();
            $this->_loadAdvancedThemeTranslation();
            $this->_loadAdvancedDbTranslation();
        }
    }

    private function _loadAdvancedStatistics($locale, $source) {
            $this->setSearchLocale($locale);

        switch ($source) {
            case("module") :
                $this->_loadAdvancedModuleTranslation();
                break;
            case("theme") :
                $this->_loadAdvancedThemeTranslation();
                break;
            case("database") :
                $this->_loadAdvancedDbTranslation();
                break;
            default:
                break;
        }
    }

    protected function _matchOriginalInArray($searchPattern) {
        $results = array();
        $keys = preg_grep($searchPattern, array_keys($this->_data));
        foreach ($keys as $key) {
            $results[$key] = $this->_data[$key];
        }

        return $results;
    }

    protected function _matchTranslationInArray($searchPattern) {
        $results = array();
        $locales = explode(',', Mage::getStoreConfig('translate/general/locales'));

        // flatten array to be able to search it
        foreach ($this->_data as $string => $locale) {
            foreach ($locale as $key => $data) {
                $searchArray[$string . "::" . $key] = $data["translate"];
            }
        }

        // search flattened array
        $searchResults = preg_grep($searchPattern, $searchArray);

        // re-inflate array
        foreach ($searchResults as $key => $value) {
            $key = substr($key, 0, -7);
            $results[$key] = $this->_data[$key];

            foreach ($results[$key] as $locale => $array) {
                if ($array['translate'] != $value) {
                    unset($results[$key][$locale]);
                }
            }
        }
        return $results;
    }

    protected function _matchStringInDb($string, $locale) {
        $results = array();
        $strings = Mage::getModel('translate/translate')->getCollection()
                ->addFieldToFilter('string', array('like' => "%$string%"));
        if ($locale != 'all') {
            $strings->addFieldToFilter('locale', array('like' => $locale));
        }
        $strings->load();

        return $strings;
    }

    protected function _getModuleConfig() {
        $config = Mage::getConfig()->getNode($this->getSearchInterface() . '/translate/modules')->children();
        return $config;
    }

    protected function _fetchStoreConfigLocale($store) {
        $config = Mage::app()->getStore($store[0])->getConfig('general/locale/code');
        return $config;
    }

    protected function _loadAdvancedModuleTranslation($forceReload=false) {
        // Locale doorsturen naar alle 'load translation' methoden, zodanig dat deze daar rekening mee houden en niet de back-end locale nemen!!!
        $config = $this->_getModuleConfig();
        foreach ($config as $moduleName => $info) {
            if ($moduleName == $this->getSearchModules() || $this->getSearchModules() == "all") {
                $info = $info->asArray();
                foreach ($info['files'] as $file) {
                    $file = $this->_getAdvancedModuleFilePath($moduleName, $file);
                    $this->_addDataToTranslate($this->_getFileData($file), $moduleName, $forceReload, 'Module');
                }
            }
        }
        return $this;
    }

    protected function _loadAdvancedThemeTranslation($forceReload = false) {
        $original = Mage::app()->getLocale()->getLocaleCode();
        Mage::app()->getLocale()->setLocaleCode($this->getSearchLocale());

        $file = Mage::getDesign()->getLocaleFileName('translate.csv');

        Mage::app()->getLocale()->setLocaleCode($original);

        $this->_addDataToTranslate($this->_getFileData($file), false, $forceReload, 'Theme');

        return $this;
    }

    protected function _loadAdvancedDbTranslation($forceReload = false) {
        $arr = $this->getResource()->getTranslationArray($this->getSearchStoreId(), $this->getSearchLocale());
        $this->_addDataToTranslate($arr, $this->getConfig(self::CONFIG_KEY_STORE), $forceReload, 'Database');
        return $this;
    }

    protected function _addDataToTranslate($data, $scope, $forceReload=false, $translationSource=null) {
        /*
         * new array:
         * 
         * [orig. string] => array(
         *                      [locale1] => array(
         *                                      [translate] => "blablabla"
         *                                      [source]    => "database"
         *                                      )
         *                      [locale2] => array(
         *                                      [translate] => "blablabla2"
         *                                      [source]    => "theme"
         *                                      )
         * TODO:
         * add level [storeview] under locale
         */

        foreach ($data as $key => $value) {
            $key = $this->_prepareDataString($key);
            $value = $this->_prepareDataString($value);
            $locale = $this->getSearchLocale();

            if ($scope && isset($this->_dataScope[$key]) && !$forceReload) {
                /**
                 * Checking previos value
                 */
                $scopeKey = $this->_dataScope[$key] . self::SCOPE_SEPARATOR . $key;
                if (!isset($this->_data[$scopeKey])) {
                    if (isset($this->_data[$key])) {
                        /**
                         * Not allow use translation not related to module
                         */
                        if (Mage::getIsDeveloperMode()) {
                            unset($this->_data[$key]);
                        }
                    }
                }
                $scopeKey = $scope . self::SCOPE_SEPARATOR . $key;

                $this->_data[$scopeKey][$locale] = array(
                    "translate" => $value,
                    "source" => $translationSource . " (" . $scope . ")"
                );
            } else {
                $this->_data[$key][$locale] = array(
                    "translate" => $value,
                    "source" => $translationSource . " (" . $scope . ")"
                );
                $this->_dataScope[$key] = $scope;
            }
        }
        return $this;
    }

    protected function _getTranslatedString($text, $code) {
        //$translated = parent::_getTranslatedString($text, $code);

        $translated = '';
        if (array_key_exists($code, $this->getData())) {
            $translated = $this->_data[$code];
        } elseif (array_key_exists($text, $this->getData())) {
            $translated = $this->_data[$text];
        } else {
            if ($this->_helper()->allowedToLogString(Mage::app()->getLocale()->getLocaleCode(), $this->getConfig(self::CONFIG_KEY_AREA))) {
                $this->_saveUnTranslatedString($text, $code);
            }
            $translated = $text;
        }
        
        return $translated;
    }

    /**
     * Adding translation data
     *
     * @param array $data
     * @param string $scope
     * @return Mage_Core_Model_Translate
     */
    protected function _addData($data, $scope, $forceReload=false) {
        foreach ($data as $key => $value) {
            if ($key === $value && !Mage::getStoreConfig('translate/general/translation_logging')) {
                continue;
            }
            $key = $this->_prepareDataString($key);
            $value = $this->_prepareDataString($value);
            if ($scope && isset($this->_dataScope[$key]) && !$forceReload) {
                /**
                 * Checking previos value
                 */
                $scopeKey = $this->_dataScope[$key] . self::SCOPE_SEPARATOR . $key;
                if (!isset($this->_data[$scopeKey])) {
                    if (isset($this->_data[$key])) {
                        $this->_data[$scopeKey] = $this->_data[$key];
                        /**
                         * Not allow use translation not related to module
                         */
                        if (Mage::getIsDeveloperMode()) {
                            unset($this->_data[$key]);
                        }
                    }
                }
                $scopeKey = $scope . self::SCOPE_SEPARATOR . $key;
                $this->_data[$scopeKey] = $value;
            } else {
                $this->_data[$key] = $value;
                $this->_dataScope[$key] = $scope;
            }
        }
        return $this;
    }

    protected function _saveUnTranslatedString($text, $code) {
        $moduleString = explode("::", $code);
        $module = $moduleString[0];

        $data = array(
            "string" => $module . '::' . $text,
            "module" => $module,
            "store_id" => Mage::app()->getStore()->getId(),
            "locale" => Mage::app()->getLocale()->getLocaleCode(),
            "interface" => $this->getConfig(self::CONFIG_KEY_AREA),
            'time' => now(),
            'page' => Mage::helper('core/url')->getCurrentUrl()
        );
        try {
            Mage::getModel('translate/translate')->setData($data)->save();
        } catch (Exception $e) {
            //Do nothing, probably just DB constraint failing.
            //Mage::log('Translate module: ' . $e->getMessage(), Zend_Log::ERR);
        }
    }
    
    public function removeDuplicatesInTable(){
        $this->setSearchModules('all');
        
        $interfaces = array('frontend','adminhtml');
        
        $locales = Mage::getModel('translate/system_config_source_locales')->toArray();
        
        $removed = array();
        
        foreach($interfaces as $interface){
            foreach($locales as $localeKey => $locale){
                $this->_data = array();

                $this->setSearchInterface($interface);

                $this->setSearchLocale($localeKey);
                $this->_loadAdvancedModuleTranslation(true);
                $this->_loadAdvancedThemeTranslation(true);
                $this->_loadAdvancedDbTranslation(true);

                $strings = Mage::getModel('translate/translate')->getCollection()
                        ->addFieldToFilter('locale', array('like' => $localeKey))
                        ->addFieldToFilter('interface', array('like'=>$interface));

                foreach($strings as $string){
                    if(array_key_exists($string->getString(), $this->_data)){
                        $removed[] = '<li>- '.$locale.' '.$string->getString().'</li>';
                        $string->delete();
                    }
                }
            }
        }
        
        return $removed;
    }

    /**
     * Retrieve translation file for module
     *
     * @param   string $module
     * @return  string
     */
    protected function _getAdvancedModuleFilePath($module, $fileName) {
        $file = Mage::getBaseDir('locale');
        $file.= DS . $this->getSearchLocale() . DS . $fileName;
        return $file;
    }

    /* Getters and setters */

    protected function getSearchLocale() {
        return $this->searchLocale;
    }

    protected function setSearchLocale($locale) {
        $this->searchLocale = $locale;
    }

    protected function getSearchStoreId() {
        return $this->searchStoreId;
    }

    protected function setSearchStoreId($storeId) {
        $this->searchStoreId = $storeId;
    }

    protected function getSearchInterface() {
        return $this->searchInterface;
    }

    protected function setSearchInterface($interface) {
        $this->searchInterface = $interface;
    }

    protected function getSearchModules() {
        return $this->searchModules;
    }

    protected function setSearchModules($modules) {
        $this->searchModules = $modules;
    }

}
