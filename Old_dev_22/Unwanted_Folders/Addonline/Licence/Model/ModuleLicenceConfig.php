<?php

/**
 * ceci est l'interface pour les modules AO
 * donc il faut que le model, l'observer soit sous la forme :
 * class Addonline_SoColissimo_Model_Observer extends Varien_Object implements Addonline_Licence_Model_ModuleLicenceConfig
 * ainsi on est obligé de bien implanté toutes les fonctions neccessaire à la licence
 * pour exemple regarder Addonline_SoColissimo_Model_Observer qui implante les fonctions et montrent bien ce qu'il faut retourner
 * @author mdelantes
 *
 */
interface Addonline_Licence_Model_ModuleLicenceConfig
{

    const GET_CONTRAT_ALL = 1;

    const GET_CONTRAT_MONO = 2;

    const GET_CONTRAT_MULTI = 3;

    /**
     * doit retourner les contrats sous la forme [licence_id] = licence_txt
     * $which permet de spécifier si on veut GET_CONTRAT_ALL, GET_CONTRAT_MONO ou GET_CONTRAT_MULTI
     * 
     * @param unknown $which            
     */
    public function getLicenceContrats ($which);

    /**
     * permet d'obtenir des information sur le module (sa licence, son nom, sa version
     * actuellement $what peut prendre comme valeur / doit retourner :
     * licence/serial = le n° de licence selon $store du module
     * module/version = la version du module
     * module/keymaster = le clé qui sert à encoder/decoder notre licence
     * module/name = le nom du module (qui pourra etre utilisé dans les notification par ex.)
     * notification/licence/error/title = le titre de la notification Magento quand il y a une erreur de licence
     * 
     * @param unknown $what            
     * @param unknown $store            
     */
    public function getLicenceInfoConfig ($what, $store = null);
    // exemple de la fonction getLicenceInfoConfig :
    // public function getLicenceInfoConfig($what, $store = null) {
    
    // switch($what) {
    // case "licence/serial":
    // return trim(Mage::getStoreConfig('socolissimo/licence/serial', $store));
    // break;
    
    // case etc..etc...
}