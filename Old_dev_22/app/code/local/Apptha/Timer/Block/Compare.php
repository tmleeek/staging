<?php
/**
 * Contus Support Interactive.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file PRICE COUNTDOWN-LICENSE.txt.
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento 1.4.x, 1.5.x, 1.6.x and 1.7.x COMMUNITY edition
 * Contus Support does not guarantee correct work of this package
 * on any other Magento edition except Magento 1.4.x, 1.5.x, 1.6.x and 1.7.x COMMUNITY edition.
 * =================================================================
 */
class Apptha_Timer_Block_Compare extends Mage_Catalog_Block_Product_Compare_List
{
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->getBlock('catalog.compare.list');
        if ($block) {
            $block->setTemplate('timer/compare.phtml');
        }
    }
}
?>