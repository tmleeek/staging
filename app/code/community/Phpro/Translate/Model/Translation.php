<?php

class Phpro_Translate_Model_Translation extends Mage_Core_Model_Translate {

    public function _construct() {
        parent::_construct();
        $this->_init('translate/translation');
    }

    public function search($string) {
        $array = $this->init('frontend');
        $fl_array = preg_grep("$string", $array);

        return $fl_array;
    }

    protected function _loadModuleTranslation($moduleName, $files, $forceReload=false) {
        foreach ($files as $file) {
            $file = $this->_getModuleFilePath($moduleName, $file);
            $this->_addData($this->_getFileData($file), $moduleName, $forceReload, 'module');
        }
        return $this;
    }

    protected function _loadThemeTranslation($forceReload = false) {
        $file = Mage::getDesign()->getLocaleFileName('translate.csv');
        $this->_addData($this->_getFileData($file), false, $forceReload, 'theme');
        return $this;
    }

    protected function _loadDbTranslation($forceReload = false) {
        $arr = $this->getResource()->getTranslationArray(null, $this->getLocale());
        $this->_addData($arr, $this->getConfig(self::CONFIG_KEY_STORE), $forceReload, 'database');
        return $this;
    }

    protected function _addData($data, $scope, $forceReload=false, $translationSource=null) {
        foreach ($data as $key => $value) {
            if ($key === $value) {
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

}

