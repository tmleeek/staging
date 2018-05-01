<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0) 
 */

/**
 * Batche qui importe les villes en base
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Cities_Batch
{

    /**
     * @var unknown
     */
    private $_connectionRead;

    /**
     * @var unknown
     */
    private $_connectionWrite;

    /**
     * @var unknown
     */
    private $_aCountries = array(
        'FR',
        'BE'
    );

    /**
     * @var unknown
     */
    private $_urlFiles = 'http://download.geonames.org/export/zip/';

    /**
     * Enter Description here
     * @param string $forceUpdate
     */
    public function updatecities($forceUpdate = false)
    {
        $this->_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        // On vérifie s'il faut créer ou nom la table dans la BDD
        if (! $this->testTableBDD())
            $this->createTableBDD();
            
            // Pour tous les pays configurés
        foreach ($this->_aCountries as $country) {
            
            // On récupère la date de nos données
            $date_fichier = $this->dateFichierActuel($country);
            // On récupère la date du fichier distant
            $date_fichier_distant = $this->getDateFichierDistant($country);
            
            // On compare les 2 dates
            if ($date_fichier_distant > $date_fichier || $forceUpdate) {
                
            	//On test le dossier tmp
            	if(!file_exists(Mage::getBaseDir() . DS . 'var' . DS . 'tmp')){
            		mkdir(Mage::getBaseDir() . DS . 'var' . DS . 'tmp');
            	}
            	
                // On télécharge le fichier zip en local
                file_put_contents(Mage::getBaseDir() . DS . 'var' . DS . 'tmp' . DS . 'cities.zip', fopen($this->_urlFiles . $country . '.zip', 'r'));
                
                // On extrait l'archive en local
                $zip = new ZipArchive();
                $res = $zip->open(Mage::getBaseDir() . DS . 'var' . DS . 'tmp' . DS . 'cities.zip');
                if ($res === TRUE) {
                    $zip->extractTo(Mage::getBaseDir() . DS . 'var' . DS . 'tmp');
                    $zip->close();
                } else {
                    Mage::log('Probleme de récupération du fichier ' . $country . '.zip ', null, 'socolissimo.log');
                    continue;
                }
                
                // On vide la table des données du pays à mettre à jour
                $this->_connectionWrite->query("DELETE FROM " . Mage::getSingleton('core/resource')->getTableName('socolissimo_cities') . " WHERE country = '" . $country . "'");
                
                // On parcours le fichier pour mettre les villes dans notre table
                $fp = fopen(Mage::getBaseDir() . DS . 'var' . DS . 'tmp' . DS . $country . '.txt', 'r');
                while (! feof($fp)) {
                    $line = fgets($fp, 2048);
                    $delimiter = "\t";
                    $data = str_getcsv($line, $delimiter);
                    
                    // Insertion dans la table
                    if (! isset($data[2]))
                        continue;
                    $this->_connectionWrite->query("INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('socolissimo_cities') . " (`country`,`city_name`,`city_zipcode`,`date_fichier`) VALUES ('" . $country . "'," . $this->_connectionWrite->quote($data[2]) . "," . $this->_connectionWrite->quote($data[1]) . ",'" . $date_fichier_distant . "');");
                }
                
                fclose($fp);
            }
        }
    }

    /**
     * Enter Description here
     * @return boolean
     */
    public function testTableBDD()
    {
        $results = false;
        try {
            $results = $this->_connectionRead->fetchAll("SELECT 1 FROM " . Mage::getSingleton('core/resource')->getTableName('socolissimo_cities') . " LIMIT 1");
        } catch (Exception $e) {
            Mage::log('La table ' . Mage::getSingleton('core/resource')->getTableName('socolissimo_cities') . 'n\'existe pas', null, 'socolissimo.log');
        }
        if ($results) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Enter Description here
     */
    public function createTableBDD()
    {
        $prefix = Mage::getConfig()->getTablePrefix();
        
        $createQuery = "CREATE TABLE IF NOT EXISTS `" . $prefix . "socolissimo_cities` (
  `geonameid` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(45) NOT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `city_zipcode` varchar(45) DEFAULT NULL,
  `date_fichier` date NOT NULL,
  PRIMARY KEY (`geonameid`),
  KEY `zipcode` (`city_zipcode`)
) ;";
        $this->_connectionWrite->query($createQuery);
    }

    /**
     * Enter Description here
     * @param unknown $country
     * @return unknown|string
     */
    public function dateFichierActuel($country)
    {
        $results = $this->_connectionRead->fetchOne("SELECT date_fichier FROM " . Mage::getSingleton('core/resource')->getTableName('socolissimo_cities') . " WHERE country ='" . $country . "'");
        
        if ($results) {
            return $results;
        } else {
            return '0000-00-00';
        }
    }

    /**
     * Enter Description here
     * @param unknown $country
     * @return unknown
     */
    public function getDateFichierDistant($country)
    {
        $filename = $this->_urlFiles . $country . '.zip';
        $f = fopen($filename, "rb");
        $meta_data = stream_get_meta_data($f);
        $return = date('Y-m-d', strtotime(substr($meta_data['wrapper_data'][3], 14, strlen($meta_data['wrapper_data'][3]))));
        fclose($f);
        return $return;
    }
}
