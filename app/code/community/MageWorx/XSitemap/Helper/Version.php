<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Helper_Version extends Mage_Core_Helper_Abstract
{
    protected $_enterpriseSince113 = null;

    public function isEnterpriseSince113()
    {
        if (is_null($this->_enterpriseSince113)) {
            $mage = new Mage();
            if (is_callable(array($mage, 'getEdition')) && Mage::getEdition() == Mage::EDITION_ENTERPRISE
                && version_compare(Mage::getVersion(), '1.13.0.0', '>=')) {
                $this->_enterpriseSince113 = true;
            }
            else {
                $this->_enterpriseSince113 = false;
            }
        }
        return $this->_enterpriseSince113;
    }
}