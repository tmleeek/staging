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
 * Batch qui importe les relais en base
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Liberte_Batch
{

    const LOG_FILE = 'socolissimo_import_liberte.log';

    /**
     * @var array
     */
    private $_tabRelais;

    /**
     * @var array
     */
    private $_tabRelaisBelgique;

    /**
     * @var string
     */
    private $_log;

    /**
     * log
     * @param unknown $message
     */
    private function _log($message)
    {
        if ($this->_log === false) {
            Mage::log($message, null, self::LOG_FILE);
        } else {
            $this->_log .= $message . "<br/>";
        }
    }

    /**
     * traitement d'import
     * @param string $log
     * @return unknown
     */
    public function run($log = false)
    {
        $this->_log = $log;
        
        $_export_dir = Mage::getStoreConfig('carriers/socolissimo/rep_fichier_liberte', Mage::app()->getStore()->getId());
        if (! $_export_dir) {
            $this->_log("La paramètre répertoire des fichiers Socolissimo n'est pas configuré");
            return $this->_log;
        }
        
        $_export_path = BP . DS . $_export_dir;
        
        $repertoire = @opendir($_export_path);
        if ($repertoire === false) {
            $this->_log("Le repertoire $_export_path n'existe pas");
            return $this->_log;
        }
        
        $file = null;
        $timestamp = 0;
        while ($nom_fichier = @readdir($repertoire)) {
            if (preg_match("/^PR_CLP_/", $nom_fichier)) {
                // enlever les traitements inutile
                if ($nom_fichier == "." || $nom_fichier == "..")
                    continue;
                    
                    // il faut prendre le fichier le plus récent dans le répértoire
                if ($timestamp < filemtime($_export_path . DS . $nom_fichier)) {
                    $timestamp = filemtime($_export_path . DS . $nom_fichier);
                    $file = $nom_fichier;
                }
            }
        }
        closedir($repertoire);
        
        if ($file) {
            $this->_importRelais($_export_path . DS . $file);
            $this->_log("Import SoColissimo $_export_path/$file effectué avec succès");
        } else {
            $this->_log("Aucun fichier SoColissimo à importer dans $_export_path");
        }
        
        return $this->_log;
    }

    /**
     * import un fichier
     * @param unknown $nom_fichier
     */
    function _importRelais($nom_fichier)
    {
        $this->_log("Import SoColissimo $nom_fichier");
        // ini_set('memory_limit', '1024M');
        /* Ouverture du fichier en lecture seule */
        $file = fopen($nom_fichier, 'r');
        /* Si on a réussi à ouvrir le fichier */
        if ($file) {
            // on vide les tables socolissimo_horaire_ouverture et socolissimo_periode_fermeture pour mettre à jour leur données
            $this->_viderTables();
            
            /* Tant que l'on est pas à la fin du fichier */
            $countRelais = 0;
            $countRelaisBE = 0;
            $countOuvertures = 0;
            $countOuverturesBE = 0;
            $countFermetures = 0;
            $countFermeturesBE = 0;
            while (! feof($file)) {
                set_time_limit(0);
                
                // on lit la ligne courante
                $ligne = fgets($file);
                
                // Lire les lignes des points de retrait
                if (strpos($ligne, "PR") === 0) {
                    $this->majRelais($ligne);
                    $countRelais ++;
                }                 // Lire les lignes des horaires d'ouverture des points de retrait
                else 
                    if (strpos($ligne, "HO") === 0) {
                        $this->remplirHoraireOuverture($ligne);
                        $countOuvertures ++;
                    }                     // Lire les lignes des horaires de fermeture des points de retrait
                    else 
                        if (strpos($ligne, "FE") === 0) {
                            $this->remplirPeriodeFermeture($ligne);
                            $countFermetures ++;
                        }                         

                        // Lire les lignes des points de retrait (Belgique)
                        else 
                            if (strpos($ligne, "PB") === 0) {
                                $this->majRelaisBelgique($ligne);
                                $countRelaisBE ++;
                            }                             // Lire les lignes des horaires d'ouverture des points de retrait (Belgique)
                            else 
                                if (strpos($ligne, "HB") === 0) {
                                    $this->remplirHoraireOuvertureBelgique($ligne);
                                    $countOuverturesBE ++;
                                }                                 // Lire les lignes des horaires de fermeture des points de retrait (Belgique)
                                else 
                                    if (strpos($ligne, "FB") === 0) {
                                        $this->remplirPeriodeFermetureBelgique($ligne);
                                        $countFermeturesBE ++;
                                    }
            }
            /* On ferme le fichier */
            fclose($file);
            
            $this->_log("Relais : $countRelais");
            $this->_log("Ouvertures : $countOuvertures");
            $this->_log("Fermetures : $countFermetures");
            $this->_log("Relais Belgique : $countRelaisBE");
            $this->_log("Ouvertures Belgique : $countOuverturesBE");
            $this->_log("Fermetures Belgique : $countFermeturesBE");
        }
    }

    /**
     * maj dans la table socolissimo_relais'
     * @param unknown $ligneRelais
     */
    function majRelais($ligneRelais)
    {
        $donnes_relais = explode(";", $ligneRelais);
        
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        if ($id = $this->getIdRelais($donnes_relais[1], 'R01')) {
            $updateQuery = "UPDATE " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_relais') . " SET ";
            $updateQuery .= "libelle='" . str_replace("'", "\'", $donnes_relais[2]) . "', ";
            $updateQuery .= "adresse='" . str_replace("'", "\'", $donnes_relais[3]) . "', ";
            $updateQuery .= "complement_adr='" . str_replace("'", "\'", $donnes_relais[4]) . "', ";
            $updateQuery .= "lieu_dit='" . str_replace("'", "\'", $donnes_relais[5]) . "', ";
            $updateQuery .= "indice_localisation='" . str_replace("'", "\'", $donnes_relais[6]) . "', ";
            $updateQuery .= "code_postal='" . $donnes_relais[7] . "', ";
            $updateQuery .= "commune='" . str_replace("'", "\'", $donnes_relais[8]) . "', ";
            $updateQuery .= "latitude=" . str_replace(',', '.', $donnes_relais[9]) . ", ";
            $updateQuery .= "longitude=" . str_replace(',', '.', $donnes_relais[10]) . ", ";
            $updateQuery .= "indicateur_acces='" . $donnes_relais[11] . "', ";
            $updateQuery .= "type_relais='" . $donnes_relais[12] . "', ";
            $updateQuery .= "point_max='" . $donnes_relais[13] . "', ";
            $updateQuery .= "lot_acheminement='" . $donnes_relais[14] . "', ";
            $updateQuery .= "distribution_sort='" . $donnes_relais[15] . "', ";
            $updateQuery .= "version='" . $donnes_relais[16] . "' ";
            $updateQuery .= " WHERE id_relais=" . $id;
            // echo $updateQuery;
            $connectionWrite->query($updateQuery);
        } else {
            $insertQuery = "INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_relais') . " (";
            $insertQuery .= "code_reseau, identifiant, libelle, adresse,complement_adr,lieu_dit,indice_localisation,code_postal,commune,latitude,";
            $insertQuery .= "longitude,indicateur_acces,type_relais,point_max,lot_acheminement,distribution_sort,version) ";
            $insertQuery .= "VALUES ('R01','" . $donnes_relais[1] . "','" . str_replace("'", "\'", $donnes_relais[2]) . "','" . str_replace("'", "\'", $donnes_relais[3]);
            $insertQuery .= "','" . str_replace("'", "\'", $donnes_relais[4]) . "','" . str_replace("'", "\'", $donnes_relais[5]);
            $insertQuery .= "','" . str_replace("'", "\'", $donnes_relais[6]) . "','" . $donnes_relais[7] . "','" . str_replace("'", "\'", $donnes_relais[8]);
            $insertQuery .= "'," . str_replace(',', '.', $donnes_relais[9]) . "," . str_replace(',', '.', $donnes_relais[10]);
            $insertQuery .= ",'" . $donnes_relais[11] . "','" . $donnes_relais[12] . "','" . $donnes_relais[13] . "','" . $donnes_relais[14];
            $insertQuery .= "','" . $donnes_relais[15] . "','" . $donnes_relais[16] . "')";
            $connectionWrite->query($insertQuery);
            $id = $this->getIdRelais($donnes_relais[1], 'R01');
        }
        
        // sauvegarde des clé primaire de chaque identifiants relais
        $this->_tabRelais[$donnes_relais[1]] = $id;
    }

    /**
     * récupère l'id d'un relais en base
     * @param unknown $identifiant
     * @param unknown $reseau
     * @return boolean
     */
    function getIdRelais($identifiant, $reseau)
    {
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $results = $connectionRead->fetchAll("SELECT id_relais FROM " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_relais') . " WHERE identifiant='" . $identifiant . "' AND code_reseau='" . $reseau . "'");
        if ($results) {
            return $results[0]["id_relais"];
        } else {
            return false;
        }
    }

    /**
     * maj dans la table socolissimo_horaire_ouverture'
     * @param unknown $ligneHO
     */
    function remplirHoraireOuverture($ligneHO)
    {
        $donnes_horaire = explode(";", $ligneHO);
        
        if (isset($this->_tabRelais[$donnes_horaire[1]])) {
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            $insertQuery = "INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_horaire_ouverture') . " (";
            $insertQuery .= "id_relais_ho, deb_periode_horaire, fin_periode_horaire,horaire_lundi,horaire_mardi,horaire_mercredi,";
            $insertQuery .= "horaire_jeudi,horaire_vendredi,horaire_samedi,horaire_dimanche) ";
            $insertQuery .= "VALUES (" . $this->_tabRelais[$donnes_horaire[1]] . ",'" . $donnes_horaire[2] . "','" . $donnes_horaire[3];
            $insertQuery .= "','" . $donnes_horaire[4] . "','" . $donnes_horaire[5] . "','" . $donnes_horaire[6] . "','" . $donnes_horaire[7];
            $insertQuery .= "','" . $donnes_horaire[8] . "','" . $donnes_horaire[9] . "','" . $donnes_horaire[10] . "')";
            $connectionWrite->query($insertQuery);
        }
    }

    /**
     * maj dans la table socolissimo_periode_fermeture'
     * @param unknown $ligneFE
     */
    function remplirPeriodeFermeture($ligneFE)
    {
        $donnes_fe = explode(";", $ligneFE);
        
        if (isset($this->_tabRelais[$donnes_fe[1]])) {
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            $dd = new Zend_Date($donnes_fe[2], "dd/MM/yyyy");
            $df = new Zend_Date($donnes_fe[3], "dd/MM/yyyy");
            $insertQuery = "INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_periode_fermeture') . " (";
            $insertQuery .= "id_relais_fe, deb_periode_fermeture, fin_periode_fermeture) ";
            $insertQuery .= "VALUES (" . $this->_tabRelais[$donnes_fe[1]] . ",'" . $dd->toString("yyyy-MM-dd") . "','" . $df->toString("yyyy-MM-dd") . "')";
            $connectionWrite->query($insertQuery);
        }
    }

    /**
     * maj dans la table socolissimoliberte_relais' (Belgique)
     * @param unknown $ligneRelais
     */
    function majRelaisBelgique($ligneRelais)
    {
        $donnes_relais = explode(";", $ligneRelais);
        
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        if ($id = $this->getIdRelais($donnes_relais[2], $donnes_relais[1])) {
            $updateQuery = "UPDATE " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_relais') . " SET ";
            $updateQuery .= "code_reseau='" . $donnes_relais[1] . "', ";
            $updateQuery .= "libelle='" . str_replace("'", "\'", $donnes_relais[3]) . "', ";
            $updateQuery .= "libelle_nl='" . str_replace("'", "\'", $donnes_relais[4]) . "', ";
            $updateQuery .= "adresse='" . str_replace("'", "\'", $donnes_relais[5]) . "', ";
            $updateQuery .= "adresse_nl='" . str_replace("'", "\'", $donnes_relais[6]) . "', ";
            $updateQuery .= "code_postal='" . $donnes_relais[7] . "', ";
            $updateQuery .= "commune='" . str_replace("'", "\'", $donnes_relais[8]) . "', ";
            $updateQuery .= "commune_nl='" . str_replace("'", "\'", $donnes_relais[9]) . "', ";
            $updateQuery .= "latitude=" . str_replace(',', '.', $donnes_relais[10]) . ", ";
            $updateQuery .= "longitude=" . str_replace(',', '.', $donnes_relais[11]) . ", ";
            $updateQuery .= "indicateur_acces='" . $donnes_relais[12] . "', ";
            $updateQuery .= "type_relais='" . $donnes_relais[13] . "', ";
            $updateQuery .= "point_max='" . $donnes_relais[14] . "' ";
            $updateQuery .= " WHERE id_relais=" . $id;
            $connectionWrite->query($updateQuery);
        } else {
            $insertQuery = "INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_relais') . " (";
            $insertQuery .= "identifiant, code_reseau, libelle, libelle_nl,adresse,adresse_nl,code_postal,commune,commune_nl,";
            $insertQuery .= "latitude,longitude,indicateur_acces,type_relais,point_max) ";
            $insertQuery .= "VALUES ('" . $donnes_relais[2] . "','" . $donnes_relais[1] . "','" . str_replace("'", "\'", $donnes_relais[3]);
            $insertQuery .= "','" . str_replace("'", "\'", $donnes_relais[4]) . "','" . str_replace("'", "\'", $donnes_relais[5]);
            $insertQuery .= "','" . str_replace("'", "\'", $donnes_relais[6]) . "','" . $donnes_relais[7];
            $insertQuery .= "','" . str_replace("'", "\'", $donnes_relais[8]) . "','" . str_replace("'", "\'", $donnes_relais[9]);
            $insertQuery .= "'," . str_replace(',', '.', $donnes_relais[10]) . "," . str_replace(',', '.', $donnes_relais[11]);
            $insertQuery .= ",'" . $donnes_relais[12] . "','" . $donnes_relais[13] . "','" . $donnes_relais[14] . "')";
            $connectionWrite->query($insertQuery);
            $id = $this->getIdRelais($donnes_relais[2], $donnes_relais[1]);
        }
        
        // sauvegarde des clé primaire de chaque identifiants relais
        $this->_tabRelaisBelgique[$donnes_relais[2]] = $id;
    }

    /**
     * maj dans la table socolissimoliberte_horaire_ouverture' (Belgique)
     * @param unknown $ligneHB
     */
    function remplirHoraireOuvertureBelgique($ligneHB)
    {
        $donnes_horaire = explode(";", $ligneHB);
        
        if (isset($this->_tabRelaisBelgique[$donnes_horaire[2]])) {
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            $insertQuery = "INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_horaire_ouverture') . " (";
            $insertQuery .= "id_relais_ho, deb_periode_horaire, fin_periode_horaire,horaire_lundi,horaire_mardi,horaire_mercredi,";
            $insertQuery .= "horaire_jeudi,horaire_vendredi,horaire_samedi,horaire_dimanche) ";
            $insertQuery .= "VALUES (" . $this->_tabRelaisBelgique[$donnes_horaire[2]] . ",'" . $donnes_horaire[3] . "','" . $donnes_horaire[4];
            $insertQuery .= "','" . $donnes_horaire[5] . "','" . $donnes_horaire[6] . "','" . $donnes_horaire[7] . "','" . $donnes_horaire[8];
            $insertQuery .= "','" . $donnes_horaire[9] . "','" . $donnes_horaire[10] . "','" . $donnes_horaire[11] . "')";
            $connectionWrite->query($insertQuery);
        }
    }

    /**
     * maj dans la table socolissimoliberte_periode_fermeture' (Belgique)
     * @param unknown $ligneFB
     */
    function remplirPeriodeFermetureBelgique($ligneFB)
    {
        $donnes_fe = explode(";", $ligneFB);
        
        if (isset($this->_tabRelaisBelgique[$donnes_fe[2]])) {
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            $dd = new Zend_Date($donnes_fe[3], "dd/MM/yyyy");
            $df = new Zend_Date($donnes_fe[4], "dd/MM/yyyy");
            $insertQuery = "INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_periode_fermeture') . " (";
            $insertQuery .= "id_relais_fe, deb_periode_fermeture, fin_periode_fermeture) ";
            $insertQuery .= "VALUES (" . $this->_tabRelaisBelgique[$donnes_fe[2]] . ",'" . $dd->toString("yyyy-MM-dd") . "','" . $df->toString("yyyy-MM-dd") . "')";
            $connectionWrite->query($insertQuery);
        }
    }

    /**
     * vide les tables
     */
    function _viderTables()
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $db->query("TRUNCATE TABLE " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_horaire_ouverture'));
        $result = $db->query("TRUNCATE TABLE " . Mage::getSingleton('core/resource')->getTableName('socolissimoliberte_periode_fermeture'));
    }
}