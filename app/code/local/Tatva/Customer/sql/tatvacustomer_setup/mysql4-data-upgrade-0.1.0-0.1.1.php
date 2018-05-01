<?php
/**
 * Module setupe
 *
 * @category   Sqli
 * @package    Sqli_Customer
 * @author 	   emchaabelasri
 *
 * EXIG CLI-002
 * REG CLI-103
 *
/* @var $installer Mage_Customer_Model_Entity_Setup */

$installer = $this;
$installer->startSetup();

##### Add new customer attributes #####
	/*
	 * sqli_sponsor_email
	 * sqli_answer_hau
	 */


$installer->addAttribute('customer_address', 'tatva_is_company', array(
			'type'              => 'varchar',
            'label'             => 'Company',
            'input'             => 'text',
            'visible'           => false,
            'required'          => false,
            'user_defined'      => false,
            'visible_on_front'  => true,
            'unique'            => false,
));


$installer->endSetup();