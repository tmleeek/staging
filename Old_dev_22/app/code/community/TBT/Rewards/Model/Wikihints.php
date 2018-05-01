<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 * 
 * By adding to, editing, or in any way modifying this code, WDCA is 
 * not held liable for any inconsistencies or abnormalities in the 
 * behaviour of this code. 
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the 
 * provided Sweet Tooth License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Special Header
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_WikiHints extends Mage_Core_Model_Abstract {
	

				
				
	/* Adds a wikiHint link to the specified element if wikiHints is enabled.
	 * 
	 * This function will call the protected function generateLink(..) to produce 
	 * the HTML code neccesary for the wikiHint link. Depending on which element
	 * the link is for (either a fieldset or a form element), the HTML code generated will be placed appropriatly so that
	 * it is displayed to the right of that element on the screen. 
	 * 
	 * Usage: Mage::getSingleton('rewards/wikihints')->addWikiHint($fieldset/$formElement, $kbCode, $pageTitle, [$sectionTitle], [$linkTitle]);
	 * Example: Mage::getSingleton('rewards/wikihints')->addWikiHint($fieldset, "21480107", "Installation Guide", "Additional Steps", "");
	 * 
	 * 
	 * @param Varien_Data_Form_Element_Fieldset $element or Varien_Data_Form_Element_Abstract $element, the item which the wikiHint link will be associated with. 
	 * @param string $kbCode, the numeric value from ZenDesk that's unique and identifies the URL. Nothing after this makes any difference
	 * @param string $pageTitle, the title of the article page on the wiki website.
	 * @param string $sectionTitle, optional argument. Use this to refer to a sub-title or section title on the wiki page specified. This will be treated like an anchor tag.
	 * @param string $linkTitle, optional argument. If specified, this will show as the tooltip when the link is hovered by the user. Otherwise $pageTitle will become the tooltip text.
	 *  
	 * @return $element, the form element or fieldset which was passed in.
	 *  
	 * @todo should we allow disabling wikiHints?
	 * @author <mhadianfard@wdca.ca> Mohsen Hadianfard 
	 * 
	 */
	public function addWikiHint($element, $kbCode, $pageTitle, $sectionTitle = NULL, $linkTitle = NULL){
		
		/* Should we allow disabling wikiHints?		 
		$enabled = Mage::getStoreConfig ( 'rewards/WikiHints/is_enabled' );
		if (!$enabled) return $element;
		*/
		
		if ($element instanceof Varien_Data_Form_Element_Fieldset){
			$oldLegend = $element->getLegend();
			$linkHtml = $this->generateLink($element->getId(), $kbCode, $pageTitle, $sectionTitle, $linkTitle);
			$element->setLegend($oldLegend . $linkHtml);
			
		} else if ($element instanceof Varien_Data_Form_Element_Abstract) {
                        $linkHtml = $this->generateLink($element->getId(), $kbCode, $pageTitle, $sectionTitle, $linkTitle);
                        $afterElementHtml = $element->getAfterElementHtml();
                        $linkHtml = empty($afterElementHtml) ? $linkHtml : $linkHtml.$afterElementHtml;
                        $element->setAfterElementHtml($linkHtml);
			$element->setFieldsetHtmlClass('rewards-wikihinted');
		}
		
		return $element;		
	}	

	
	protected function generateLink($elementId, $kbCode, $pageTitle, $sectionTitle, $linkTitle){
		$baseWikiURL = $this->getBaseWikiURL();
		
		$linkTitle = empty($linkTitle) ? $pageTitle : $linkTitle;		
		$pageTitle = trim(str_replace(" ", "-", $pageTitle));
		$sectionTitle = trim(str_replace(" ", "-", $sectionTitle));
		$linkId = $elementId . "_wikiHint";
		
		$link = $baseWikiURL . "/" . $kbCode . "-" . $pageTitle . (!empty($sectionTitle) ? "#".$sectionTitle : "");
		
		// images/fam_help.gif
		return "<a id = \"$linkId\" class=\"wikiHint\"	href=\"$link\" title=\"$linkTitle\" target=\"_blank\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>";	
	}
	

	public function getBaseWikiURL() {
		$baseWikiURL = Mage::getStoreConfig ( 'rewards/WikiHints/baseURL' );
	
		//@nelkaake: If the page is supposed to be HTTPS and the AJAX call is not HTTPS, add HTTPS
		// if it's HTTP and the url returned HTTPS, remove HTTPS
		if(  isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && strpos(strtolower($baseWikiURL), 'https') !== 0) {
			$baseWikiURL = str_replace('http', 'https', $baseWikiURL);
		} elseif(!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS'] && strpos(strtolower($baseWikiURL), 'https') === 0) {
			$baseWikiURL = str_replace('https', 'http', $baseWikiURL);
		} else {
			// the url is fine and we can continue because it's using the correct encryption
		}
		
		return $baseWikiURL;
	}

	
}