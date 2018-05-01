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
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

class MageWorx_SearchAutocomplete_Model_Highlight {

    public function productAttribute($obj, $result, $params) {
        if (isset($params['attribute'])) {
            $helper = Mage::helper('searchautocomplete');
            $query = $helper->getLastQueryText();
            $attribute = $params['attribute'];
            if (strpos($attribute, 'meta') !== 0
                    && strpos($attribute, 'url') !== 0
                    && $attribute != 'image'
                    && $attribute != 'small_image'
                    && $attribute != 'thumbnail') {
                $result = $helper->highlightText($result, $query);
            }
        }
        return $result;
    }

}