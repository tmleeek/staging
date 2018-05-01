<?php
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/app/Mage.php';
//echo date('Y-m-d h:i:s');exit;
//Varien_Profiler::enable();

//Mage::setIsDeveloperMode(true);

//ini_set('display_errors', 1);

Mage::app();

$authDetails = array(
               'ssl' => 'tls',
               'port' => 587,  //or 465
               'auth' => 'login',
               'username' => 'service-technique@az-boutique.fr',
               'password' => 'nxgqYMiAlJHZLW82xdrX'
       );
       $transport = new Zend_Mail_Transport_Smtp('smtp.critsend.com', $authDetails);
       Zend_Mail::setDefaultTransport($transport);
       try { 
       $mail = new Zend_Mail();
       $mail->setBodyText('This is the text of the mail.');
       $mail->setFrom('sagar.shah@tatvasoft.com', 'Some Sender');
       $mail->addTo('nisha.baksani@tatvasoft.com', 'Some Recipient');
       $mail->setSubject('TestSubject');
       $mail->send();

       } catch (Zend_Exception $e) {
       echo $e->getMessage(); exit;

      }

?>