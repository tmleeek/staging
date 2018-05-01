<?php
/**
 * FPC Placeholder container for Sweet Tooth's customer points balance in the header
 */
class TBT_Rewards_Model_PageCache_Container_HeaderCustomerBalance extends Enterprise_PageCache_Model_Container_Abstract
{
    const CACHE_TAG_PREFIX = 'rewards_header';

    /**
     * Get container individual cache id
     *
     * @return string
     */
    protected function _getCacheId()
    {
        $cacheId = 'REWARDS_HEADER_BALANCE_' . md5($this->_getIdentifier());
        return $cacheId;
    }

    /**
     * Return unique Customer cookie
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Get container individual cache id
     *
     * @return string
     */
    public function getCacheId()
    {
        return $this->_getCacheId();
    }

    /**
     * Render block content from placeholder
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $blockClass = $this->_placeholder->getAttribute('block');
        $template = $this->_placeholder->getAttribute('template');

        $block = new $blockClass();
        $block->setTemplate($template);

        return $block->toHtml();
    }

    /**
     * Save rendered block content to cache storage
     *
     * @param string $blockContent
     * @return $this
     */
    public function saveCache($blockContent, $tags = array())
    {
        $cacheId = $this->_getCacheId();
        if ($cacheId !== false) {
            // set a cache tag so we can easily clean cache entry (class Enterprise_PageCache_Model_Cache through
            // which you can easily remove cache entry by ID is not present before MEE 1.11)
            $cacheTag = md5(self::CACHE_TAG_PREFIX . Mage::getSingleton('customer/session')->getCustomerId());
            $this->_saveCache($blockContent, $cacheId, array($cacheTag));
        }
        return $this;
    }

}
