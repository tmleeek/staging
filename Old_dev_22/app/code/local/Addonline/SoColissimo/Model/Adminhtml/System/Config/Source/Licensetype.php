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
 * Used in creating options for Socolissimo Gestion des étiquettes via Flexibilité selection
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

class Addonline_SoColissimo_Model_Adminhtml_System_Config_Source_LicenseType
{
  /**
   * Return a dropdown containing only one option (the type of license webservices or files baseed)
   * @return array
   */
  public function toOptionArray() {
      $options = array();
      $version = Mage::getConfig()->getNode('modules/Addonline_SoColissimo/version');
      $storeId = Mage::getSingleton('adminhtml/config_data')->getScopeId();
      $moi = Mage::helper('addonline_licence');

      // on veut des infos sur la licence
      $module = Mage::getSingleton('socolissimo/observer');
      $licenceInfos = $moi->_9cd4777ae76310fd6977a5c559c51820($module, $storeId, false);

      $licenceType = $licenceInfos['keyIsForEan'];

      if($licenceInfos['isKeyValide']) {
          if(strpos($licenceInfos['keyIsForEan'], 'Flexibilite') !== false) {
              $options[] = array('value' => 'webservices', 'label' => 'webservices');
          } else {
              $options[] = array('value' => 'files', 'label' => 'files');
          }
      }

      return $options;
  }
}
