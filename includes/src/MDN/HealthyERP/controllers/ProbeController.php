<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_HealthyERP_ProbeController extends Mage_Adminhtml_Controller_Action {


  /**
   * Router to launch the fix relative to the calling probe
   * Will call the function fixIssue in the class
   */
  public function FixAction(){
      $probeClassName = $this->getRequest()->getParam('type');
      $action = $this->getRequest()->getParam('action');

      $redirect = true;

      if(!empty($probeClassName)){
        $redirect = $probeClassName::fixIssue($action);
      }

      if($redirect){
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'healthyerp'));
      }
  }
  
}