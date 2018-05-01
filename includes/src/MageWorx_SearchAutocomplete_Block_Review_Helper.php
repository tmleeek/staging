<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

class MageWorx_SearchAutocomplete_Block_Review_Helper extends Mage_Review_Block_Helper {

    public function getSummaryHtml($product, $templateType, $displayIfNoReviews) {
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }

        $actionName = Mage::app()->getRequest()->getActionName();
        $controllerName = Mage::app()->getRequest()->getControllerName();

        if ($actionName == 'suggest' && $controllerName == 'ajax') {
            $this->setTemplate('searchautocomplete/summary_short.phtml');
        } else {
            $this->setTemplate($this->_availableTemplates[$templateType]);
        }

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$product->getRatingSummary()) {
            Mage::getModel('review/review')
                    ->getEntitySummary($product, Mage::app()->getStore()->getId());
        }
        $this->setProduct($product);

        return $this->toHtml();
    }

}
