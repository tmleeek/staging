<?php

/**
 * Retail Evolved - Facebook Like Button
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with this
 * package in the file EVOLVED_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://retailevolved.com/eula-1-0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to service@retailevolved.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * You may edit this file, but only at your own risk, as long as it is within
 * the constraints of the license agreement. Before upgrading the module (not Magento), 
 * be sure to back up your original installation as the upgrade may override your
 * changes.
 *
 * @category   Evolved
 * @package    Evolved_Like
 * @copyright  Copyright (c) 2010 Kaelex Inc. DBA Retail Evolved (http://retailevolved.com)
 * @license    http://retailevolved.com/eula-1-0 (Retail Evolved EULA 1.0)
 */

class Evolved_Like_Helper_Data extends Mage_Core_Helper_Abstract
{
	const CONFIG_PATH = 'evlike/evlike/';
	const GRID_CONFIG_PATH = 'evlike/evlike_grid/';
	const LIST_CONFIG_PATH = 'evlike/evlike_list/';
	const CATEGORY_CONFIG_PATH = 'evlike/evlike_category/';
	const CMS_CONFIG_PATH = 'evlike/evlike_cms/';
	
	var $_locale;
	
	protected function getFbLocales()
	{
		return array(
			'af_ZA', 'ar_AR','az_AZ', 'be_BY', 'bg_BG', 'bn_IN', 'bs_BA', 'ca_ES', 'cs_CZ',
			'cy_GB', 'da_DK', 'de_DE', 'el_GR', 'en_GB', 'en_PI', 'en_UD', 'en_US','eo_EO', 
			'es_ES', 'es_LA', 'et_EE', 'eu_ES', 'fa_IR', 'fb_LT', 'fi_FI', 'fo_FO', 'fr_CA',
			'fr_FR', 'fy_NL', 'ga_IE', 'gl_ES', 'he_IL', 'hi_IN', 'hr_HR', 'hu_HU', 'hy_AM',
			'id_ID', 'is_IS', 'it_IT', 'ja_JP', 'ka_GE', 'km_KH', 'ko_KR', 'ku_TR', 'la_VA',
			'lt_LT', 'lv_LV', 'mk_MK', 'ml_IN', 'ms_MY', 'nb_NO', 'ne_NP', 'nl_NL', 'nn_NO',
			'pa_IN', 'pl_PL', 'ps_AF', 'pt_BR', 'pt_PT', 'ro_RO', 'ru_RU', 'sk_SK', 'sl_SI',
			'sq_AL', 'sr_RS', 'sv_SE', 'sw_KE', 'ta_IN', 'te_IN', 'th_TH', 'tl_PH', 'tr_TR',
			'uk_UA', 'vi_VN', 'zh_CN', 'zh_HK', 'zh_TW'
		);
	}
	
	public function getLikeConfig($configName) 
	{			
		$configValue = Mage::getStoreConfig(self::CONFIG_PATH . $configName);
		
		return $configValue;
	}
	
	public function getLikeGridConfig($configName)
	{
		$configValue = Mage::getStoreConfig(self::GRID_CONFIG_PATH . $configName);
		
		return $configValue;
	}
	
	public function getLikeListConfig($configName)
	{
		$configValue = Mage::getStoreConfig(self::LIST_CONFIG_PATH . $configName);
		
		return $configValue;
	}
	
	public function getLikeCategoryConfig($configName)
	{
		$configValue = Mage::getStoreConfig(self::CATEGORY_CONFIG_PATH . $configName);
		
		return $configValue;
	}
	
	public function getLikeCmsConfig($configName)
	{
		$configValue = Mage::getStoreConfig(self::CMS_CONFIG_PATH . $configName);
		
		return $configValue;
	}
	
	public function getLikeHtml($block, $object, $setCategory = false, $setCms = false) {
		$_layout = $block->getLayout();
		
		if ($setCategory) {
			$_childBlockName = 'category.likebutton';
		} else if ($setCms) {
			$_childBlockName = 'cms.likebutton';
		} else {
			$_childBlockName = 'likebutton';
		}
		
		// Add child block for button if necessary
		if(!$block->getChild($_childBlockName)) {
			$_likeBlock = $_layout->createBlock('evlike/like', $_childBlockName)
				->setTemplate('evlike/likebutton.phtml');
			
			$block->append($_likeBlock, $_childBlockName);
		} 
		
		// Add FBINIT block to page if necessary
		if(!$_layout->getBlock('ev_fb_init')) {
			$_initBlock = $_layout->createBlock('core/template', 'ev_fb_init')
				->setTemplate('evlike/fbinit.phtml');
				
			$_layout->getBlock('before_body_end')->append($_initBlock, 'ev_fb_init');
		}
		
		if(!$setCategory && !$setCms) {
			$block->getChild($_childBlockName)->setProduct($object);
		} else if ($setCms) {
			$block->getChild($_childBlockName)->setCms(true);
		} else {
			$block->getChild($_childBlockName)->setCategory($object);
		}
		
		return $block->getChildHtml($_childBlockName, false);
	}
	
	public function getFacebookLocale() 
	{
		if(isset($this->_locale)) {
			return $this->_locale;
		}
		
		$_locale = Mage::app()->getLocale()->getLocaleCode(); 
		
		if(!in_array($_locale, $this->getFbLocales())) {
			$_locale = 'en_US';
		}
		
		$this->_locale = $_locale;
		
		return $_locale;
	}
}