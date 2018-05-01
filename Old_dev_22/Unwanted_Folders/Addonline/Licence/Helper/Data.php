<?php

/**
 * Important : pour toute mise à jour (toutes!) changer le self::VERSION ceci afin de pouvoir comprendre
 * les incompatibliltés que pourrait avoir le client
 *
 * @author mdelantes
 *
 */
class Addonline_Licence_Helper_Data extends Mage_Core_Helper_Abstract
{

    const VERSION = "0.0.1";

    public function getVersion ()
    {
        return self::VERSION;
    }

    /**
     * test le numéro de licence pour le FO pour un store précis
     *
     * @param unknown $toCheckStoreId
     * @param string $simpleReturn
     *            on retourne true ou false si simpleReturn sinon on retourne un tableau avec les infos sur la clé
     * @return boolean
     */
    public function _9cd4777ae76310fd6977a5c559c51820 ($module, $toCheckStoreId, $simpleReturn = true)
    {
        $this->licenceLog(
            "_9cd4777ae76310fd6977a5c559c51820()" . $this->getVersion() . " : start pour " .
                 $this->_getStoreConfigOfModule($module, "module/name") . " , le storeId " . $toCheckStoreId);

        // on recupere la clé spécifique de notre module qui sert a encoder/decoder les licences
        $key = $this->_getStoreConfigOfModule($module, "module/keymaster");

        $store_error = null;

        // on recupere les contrats de du module (ex : flexibilite, liberte) sous la forme d'un array[licence_id] =
        // licence_txt
        $contratPossibles = $module->getLicenceContrats(Addonline_Licence_Model_ModuleLicenceConfig::GET_CONTRAT_MONO);

        $this->licenceLog("Contrats du module " . $this->_getStoreConfigOfModule($module, "module/name"));
        foreach ($contratPossibles as $k => $v) {
            $this->licenceLog($k . " => " . $v);
        }

        $this->licenceLog("keymaster = " . $key);

        $isKeyValide = false; // est ce que la clé est valide
        $keyValideIs = ""; // la clé qu'on a trouvé qui nous a permis de dire qu'il y a une licence
        $keyOfStore = ""; // clé du magasin sur lequel on est
        $isKeyMulti = null; // est ce une clé multi sites
        $valideStoreIds = array(); // listes des id de store qui ont une clé valide
        $keyIsForEan = ""; // la clé positive est utilisé pour <ean> (en texte)
        $keyIsForEanId = - 1; // la clé positive est utilisé pour <ean_id> eg :
                              // Addonline_Licence_Model_ModuleLicenceConfig::CONTRAT_LIBERTE

        $toCheckStore = $storeErreur = Mage::getModel('core/store')->load($toCheckStoreId);

        // tableau qu'on retourne si on nous a demandé de faire un return() "complexe" = avec toutes les infos
        $returnComplexes = array();
        $returnComplexes["isKeyValide"] = $isKeyValide;
        $returnComplexes["keyValideIs"] = $keyValideIs;
        $returnComplexes["keyOfStore"] = trim(Mage::getStoreConfig('socolissimo/licence/serial', $toCheckStoreId));
        $returnComplexes["isKeyMulti"] = $isKeyMulti;
        $returnComplexes["keyIsForEan"] = $keyIsForEan;
        $returnComplexes["keyIsForEanId"] = $keyIsForEanId;

        // si on a le module de licence AO alors on dit que la licence est toujours bonne
        if (1 == 1 && Mage::getStoreConfig('addonline/licence/aomagento')) {
            $this->licenceLog("return true licence/aomagento présent");
            if ($simpleReturn) {
                return true;
            } else {
                $returnComplexes["isKeyValide"] = true;
                $returnComplexes["keyOfStore"] = "";
                $returnComplexes["isKeyMulti"] = true;
                $returnComplexes["keyIsForEan"] = "";
                $returnComplexes["keyValideIs"] = "";
                $returnComplexes["keyIsForEanId"] = $keyIsForEanId;
                return $returnComplexes;
            }
        }

        // on veut tous les stores + le default
        $stores = Mage::app()->getStores(true);

        $this->licenceLog("debut test pour mono site");

        // on va tester dans un premier temps les cles contrat mono site
        // on test pour tous les types de contrats
        foreach ($contratPossibles as $contratPossibleKey => $contratPossibleEan) {
            // on test tous les stores
            $store = $toCheckStore;
            // pour chaque store on va donc tester la licence vs clé mono site et vs clé multisite
            $storeUrl = $this->_prepareUrl($store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
            $storeKey = $this->_getStoreConfigOfModule($module, 'licence/serial', $store);

            $this->licenceLog(
                "storeId = " . $toCheckStoreId . " / storeKey = " . $storeKey . " / storeUrl = " . $storeUrl .
                     " / contratEan = " . $contratPossibleEan);

            if (md5($storeUrl . $key . $contratPossibleEan) === $storeKey) {
                $isKeyValide = true;
                $keyValideIs = $storeKey;
                $isKeyMulti = false;
                $valideStoreIds[] = $store->getStoreId();
                $keyIsForEan = $contratPossibleEan;
                $keyIsForEanId = $contratPossibleKey;
                $this->licenceLog(
                    "licence valide trouvee pour le store " . $store->getStoreId() . " ( " . $store['code'] . ")");
            }
        }

        $this->licenceLog("fin test pour mono site");

        // si on n'a pas trouvé une licence valide pour notre store quand on recherchait dans les clé mono
        if (! in_array($toCheckStoreId, $valideStoreIds)) {
            $this->licenceLog("debut test pour multi sites");

            // on prend va tester que dans les contrats multi
            $contratPossibles = $module->getLicenceContrats(
                Addonline_Licence_Model_ModuleLicenceConfig::GET_CONTRAT_MULTI);

            foreach ($contratPossibles as $contratPossibleKey => $contratPossibleEan) {
                // on test tous les stores
                foreach ($stores as $store) {
                    // pour chaque store on va donc tester la licence vs clé multi site et vs clé multisite
                    $storeUrl = $this->_prepareUrl($store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
                    $storeKey = $this->_getStoreConfigOfModule($module, 'licence/serial', $store);

                    $this->licenceLog(
                        "storeId = " . $store->getStoreId() . " / storeKey = " . $storeKey . " / storeUrl = " . $storeUrl .
                             " / contratEan = " . $contratPossibleEan);

                    if (md5($storeUrl . $key . $contratPossibleEan) === $storeKey) {
                        $isKeyValide = true;
                        $keyValideIs = $storeKey;
                        $isKeyMulti = TRUE;
                        $valideStoreIds[] = $store->getStoreId();
                        $keyIsForEan = $contratPossibleEan;
                        $keyIsForEanId = $contratPossibleKey;
                        $this->licenceLog(
                            "licence valide trouvee pour le store " . $store->getStoreId() . " ( " . $store['code'] . ")");
                    }
                }
            }

            $this->licenceLog("fin test pour multi sites");
        }

        $returnComplexes["isKeyValide"] = $isKeyValide;
        $returnComplexes["keyValideIs"] = $keyValideIs;
        $returnComplexes["isKeyMulti"] = $isKeyMulti;
        $returnComplexes["keyIsForEan"] = $keyIsForEan;
        $returnComplexes["keyIsForEanId"] = $keyIsForEanId;

        if ($isKeyValide) {
            $t = "licence valide pour $keyIsForEan";
            if ($isKeyMulti) {
                $t .= ", c'est du multisite";
            } else {
                $t .= ", c'est du monosite";
            }
        } else {
            $t = "pas de licence";
        }
        $this->licenceLog($t);
        $this->licenceLog(" on vs avec $toCheckStoreId et y a " . count($valideStoreIds) . " store(s) valide(s)");

        // est ce qu'on a une clé valide ?
        if ($isKeyValide) {
            // si on est mono site alors on vérifie que la clé valide est bien celle du site sur lequel on est
            if (! $isKeyMulti) {
                if (in_array($toCheckStoreId, $valideStoreIds)) {
                    $retour = true;
                } else {
                    $retour = false;
                }
            } else {
                $retour = true;
            }
        }         // on n'a pas de clé
        else {
            $retour = false;
        }

        $storeErreur = Mage::getModel('core/store')->load($toCheckStoreId);
        $storeErreurKey = $this->_getStoreConfigOfModule($module, 'licence/serial', $storeErreur);
        // on a pas trouvé la clé
        if (! $retour) {
            // le soco du store a une clé, donc y a une vrai erreur
            if ($storeErreurKey != 'DISABLED') {
                // on previent en mettant un msg dans le bo que la clé n'est pas bonne pour ce store
                $this->_addNotificationErrorLicence($module, $toCheckStoreId);
                if (!$simpleReturn) {
                    $returnComplexes["isKeyValide"] = false;
                    return $returnComplexes;
                } else {
                    return false;
                }
            }             // donc la clé n'a pas été trouvé mais le soco est marqué disabled, on retourne false
            else {
                if (! $simpleReturn) {
                    $returnComplexes["isKeyValide"] = false;
                    return $returnComplexes;
                } else {
                    return false;
                }
            }
        }         // pas d'erreur ? on retourne true alors
        else {
            $this->_removeNotificationsLicenceError($module, $toCheckStoreId);
            if (! $simpleReturn) {
                $returnComplexes["isKeyValide"] = true;
                return $returnComplexes;
            } else {
                return true;
            }
        }
    }

    /**
     * retourne une info de config du module dont on teste la licence
     * ca permet du coup de récuperer le clé de licence, la version du module, etc....
     * on met un switch comme ca si on doit faire un traitement sur le retour pour un $what particulier on peut
     * par defaut on recupere le $what direct dans le module avec sa fonction getLicenceInfoConfig()
     *
     * @see Addonline_Licence_Model_ModuleLicenceConfig
     * @param unknown $module
     * @param unknown $what
     * @param unknown $store
     * @return NULL
     */
    private function _getStoreConfigOfModule ($module, $what, $store = -1)
    {
        switch ($what) {
            default:
                return $module->getLicenceInfoConfig($what, $store);
                break;
        }
    }

    /**
     * permet d'ajouter une notification les notice() dans l'inbox de Magento
     * cette fonction est "générique" car on donne le title, la desc, etc...
     * il y a des fonctions donc qui font appel à elle (sorte d'alias pour une une notification specifique)
     * on spécifie pour quel module et pour quel store
     *
     * @param unknown $module
     * @param unknown $toCheckStoreId
     */
    public function _addNotification ($title, $description,
        $severity = Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR)
    {
        $date = date('Y-m-d H:i:s');

        // We check if we already send this notification in the latest 10 minutes
        // If so we don't send it again
        if($this->_isAlreadyInNotification(60 * 10, $title)) return;

        Mage::getModel('adminnotification/inbox')->parse(
            array(
                    array(
                            'severity' => $severity,
                            'date_added' => $date,
                            'title' => $title,
                            'description' => $description,
                            'url' => '',
                            'internal' => true
                    )
            ));
    }

    /**
     * permet de savoir si une notification dans la boite de réception est déjà présente et date de moins de XX secondes
     *
     * @param  integer  $delayTimestamp delay in second between two notifications with same title
     * @param  string  $titleToSearch  title of the notification we want to search
     * @return boolean                 has a same notification been sent during the last XX seconds
     */
    public function _isAlreadyInNotification($delayTimestamp, $titleToSearch) {
        $notifications = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('title', $titleToSearch);
        foreach($notifications as $notification) {
            $dateAddedTimestamp = strtotime($notification->date_added);
            if(time() - $dateAddedTimestamp < $delayTimestamp) return true;
        }
        return false;
    }

    /**
     * ajoute une notification signalant une erreur de licence dans le inbox de magento
     *
     * @see _addNotification
     * @param unknown $module
     * @param unknown $storeId
     */
    public function _addNotificationErrorLicence ($module, $storeId)
    {
        $store = Mage::getModel('core/store')->load($storeId);

        $title = 'Le module a été désactivé '.$this->getNotificationErrorTitle($module, $store['code']);

        $desc = "Vous devez renseigner une clé licence valide pour le module "
            .$this->_getStoreConfigOfModule($module, "module/name") . " pour le magasin " . $store['code']
            .'(En obtenir une : https://www.acyba.com/?utm_source=colissimo&utm_medium=app-magento&utm_campaign=license-nokey)';

        $this->_addNotification($title, $desc);
    }

    /**
     * retourne le titre qu'on va mettre à la notification Magento pour une erreur de licence
     *
     * @param unknown $storeCode
     */
    private function getNotificationErrorTitle ($module, $storeCode)
    {
        return str_replace("__storeCode__", $storeCode,
            $this->_getStoreConfigOfModule($module, "notification/licence/error/title"));
    }

    /**
     * retourne une url au format attendu pour faire le checksum de la clé ( = ww.abc.com )
     *
     * @param unknown $url
     */
    private function _prepareUrl ($url)
    {
        $url = strtolower($url);
        $domainname = preg_replace("/^[\w\:\/]*\/\/?([\w\d\.\-]+).*\/*$/", "$1", $url);
        return preg_replace("/^([\w\d\.\-]+).*\/*$/", "$1", $domainname);
    }

    /**
     * on marque les notifications d'erreur de licence qui concerne le store $storeCode comme lu
     * a noter qu'on trouve les notifications concernant notre store d'apres le titre de la notification
     *
     * @param unknown $storeCode
     */
    public function _removeNotificationsLicenceError ($module, $storeId)
    {
        $this->licenceLog("_removeNotificationsLicenceError() pour $storeId");

        $store = Mage::getModel('core/store')->load($storeId);

        $_unreadNotices = Mage::getModel('adminnotification/inbox')->getCollection()->getItemsByColumnValue('is_read',
            0);

        $title = $this->getNotificationErrorTitle($module, $store['code']);

        foreach ($_unreadNotices as $notice) {
            if ($notice->getData('title') == $title) {
                $notice->setIsRead(1)->save();
            }
        }
    }

    /**
     * permet de logguer dans AOLicence.log des infos
     *
     * @param unknown $t
     */
    private function licenceLog ($t)
    {
        Mage::log($t, null, 'AOLicence.log');
    }
}
