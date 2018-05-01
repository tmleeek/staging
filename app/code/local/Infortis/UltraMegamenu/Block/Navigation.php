<?php
class Infortis_UltraMegamenu_Block_Navigation extends Mage_Catalog_Block_Navigation
{
    protected $x0b = FALSE;
    protected $x0c = FALSE;
    protected $x0d = NULL;


     /**
     * Set cache data
     */
    protected function _construct()
    {

        $this->addData(array('cache_lifetime' => 604800));
        $this->addCacheTag(array(
            Mage_Catalog_Model_Category::CACHE_TAG,
            Mage_Core_Model_Store_Group::CACHE_TAG
        ));
    }


    public function getCacheKeyInfo()
    {
        $x0e                   = array(
            'CATALOG_NAVIGATION',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getCurrenCategoryKey(),
            Mage::helper('ultramegamenu')->getIsOnHome(),
            (int) Mage::app()->getStore()->isCurrentlySecure()
        );
        $x0f                   = $x0e;
        $x0e                   = array_values($x0e);
        $x0e                   = implode('|', $x0e);
        $x0e                   = md5($x0e);
        $x0f['category_path']  = $this->getCurrenCategoryKey();
        $x0f['short_cache_id'] = $x0e;
        //Mage::log("cache_key:".print_r($x0f,true),null,"cache_menu_key.log");
        return $x0f;
    }
    protected function _renderCategoryMenuItemHtml($x10, $x11 = 0, $x12 = false, $x13 = false, $x14 = false, $x15 = '', $x16 = '', $x17 = false)
    {
        if (!$x10->getIsActive()) {
            return '';
        }
        $x18 = array();
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $x19 = (array) $x10->getChildrenNodes();
            $x1a = count($x19);
        } else {
            $x19 = $x10->getChildren();
            $x1a = $x19->count();
        }
        $x1b = ($x19 && $x1a);
        $x1c = array();
        foreach ($x19 as $x1d) {
            if ($x1d->getIsActive()) {
                $x1c[] = $x1d;
            }
        }
        $x1e = count($x1c);
        $x1f = ($x1e > 0);
        $x20 = Mage::helper('ultramegamenu');
        $x21 = Mage::getModel('catalog/category')->load($x10->getId());
        $x22 = FALSE;
        if ($this->_isWide) {
            $x22 = $x1f;
            if ($x20->getCfg('widemenu/show_if_no_children')) {
                $x22 = true;
            }
        }
        $x23 = array();
        $x24 = array();
        $x25 = false;
        $x26 = 0;
        if ($x11 == 0 && $this->_isAccordion == FALSE && $x22) {
            $x27 = $this->_getCatBlock($x21, 'umm_cat_block_right');
            $x28 = 6;
            if ($x29 = $x21->getData('umm_cat_block_proportions')) {
                $x29 = explode("/", $x29);
                $x2a = $x29[0];
                $x2b = $x29[1];
            } else {
                $x2a = 4;
                $x2b = 2;
            }
            $x26 = $x2a + $x2b;
            if (empty($x27)) {
                $x2a += $x2b;
                $x2b = 0;
                $x2c = 'grid12-12';
            } elseif (!$x1f) {
                $x2b += $x2a;
                $x2a = 0;
                $x2d = 'grid12-12';
            } else {
                $x2e = 12 / $x26;
                $x2c = 'grid12-' . ($x2a * $x2e);
                $x2d = 'grid12-' . ($x2b * $x2e);
            }
            $x26 = $x2a + $x2b;
            $x2f = '';
            if ($x2f = $this->_getCatBlock($x21, 'umm_cat_block_top')) {
                $x23[] = '<div class="nav-block nav-block-top grid-full std">';
                $x23[] = $x2f;
                $x23[] = '</div>';
            }
            if ($x1f) {
                $x30   = 'itemgrid itemgrid-' . $x2a . 'col';
                $x23[] = '<div class="nav-block nav-block-center ' . $x2c . ' ' . $x30 . '">';
                $x24[] = '</div>';
            }
            if ($x27) {
                $x24[] = '<div class="nav-block nav-block-right std ' . $x2d . '">';
                $x24[] = $x27;
                $x24[] = '</div>';
            }
            if ($x2f = $this->_getCatBlock($x21, 'umm_cat_block_bottom')) {
                $x24[] = '<div class="nav-block nav-block-bottom grid-full std">';
                $x24[] = $x2f;
                $x24[] = '</div>';
            }
            if (!empty($x23) || !empty($x24))
                $x25 = true;
        }
        $x31   = array();
        $x31[] = 'level' . $x11;
        $x31[] = 'nav-' . $this->_getItemPosition($x11);
           $x31[] = '';
       /* if ($this->isCategoryActive($x10)) {
            $x31[] = 'active';
        }*/
        $x32 = '';
        if ($x14 && $x15) {
            $x31[] = $x15;
            $x32   = ' class="' . $x15 . '"';
        }
        if ($x13) {
            $x31[] = 'first';
        }
        if ($x12) {
            $x31[] = 'last';
        }
        $x33 = ($x1f || $x25) ? true : false;
        if ($x33) {
            $x31[] = 'parent';
        }
        if ($x11 == 1 && $this->_isAccordion == FALSE && $this->_isWide) {
            $x31[] = 'item';
        }
        $x34 = array();
        if (count($x31) > 0) {
            $x34['class'] = implode(' ', $x31);
        }
        if ($x1f && !$x17) {
            $x34['onmouseover'] = 'toggleMenu(this,1)';
            $x34['onmouseout']  = 'toggleMenu(this,0)';
        }
        $x35 = '<li';
        foreach ($x34 as $x36 => $x37) {
            $x35 .= ' ' . $x36 . '="' . str_replace('"', '\"', $x37) . '"';
        }
        $x35 .= '>';
        $x18[] = $x35;
        if ($x11 == 1 && $this->_isAccordion == FALSE && $this->_isWide) {
            if ($x2f = $this->_getCatBlock($x21, 'umm_cat_block_top')) {
                $x18[] = '<div class="nav-block nav-block-level1-top std">';
                $x18[] = $x2f;
                $x18[] = '</div>';
            }
        }
        $x38 = $this->_getCategoryLabelHtml($x21, $x11);
        $x39 = '';
        if ($x33 && $x11 == 0 && $this->_isAccordion == FALSE) {
            $x39 = '<span class="caret">&nbsp;</span>';
        }
        $x18[] = '<a href="' . $this->getCategoryUrl($x10) . '"' . $x32 . '>';
        $x18[] = '<span>' . $this->escapeHtml($x10->getName()) . $x38 . '</span>' . $x39;
        $x18[] = '</a>';
        $x3a   = '';
        $x3b   = 0;
        foreach ($x1c as $x1d) {
            $x3a .= $this->_renderCategoryMenuItemHtml($x1d, ($x11 + 1), ($x3b == $x1e - 1), ($x3b == 0), false, $x15, $x16, $x17);
            $x3b++;
        }
        if ($x11 == 0 && $this->_isAccordion == FALSE && $this->_isWide) {
            $x16 = 'level0-wrapper dropdown-' . $x26 . 'col';
        }
        if (!empty($x3a) || $x25) {
            if ($this->_isAccordion == TRUE)
                $x18[] = '<span class="opener">&nbsp;</span>';
            if ($x16) {
                $x18[] = '<div class="' . $x16 . '"><div class="level0-wrapper2">';
            }
            $x18[] = implode("", $x23);
            if (!empty($x3a)) {
                $x18[] = '<ul class="level' . $x11 . '">';
                $x18[] = $x3a;
                $x18[] = '</ul>';
            }
            $x18[] = implode("", $x24);
            if ($x16) {
                $x18[] = '</div></div>';
            }
        }
        if ($x11 == 1 && $this->_isAccordion == FALSE && $this->_isWide) {
            if ($x2f = $this->_getCatBlock($x21, 'umm_cat_block_bottom')) {
                $x18[] = '<div class="nav-block nav-block-level1-bottom std">';
                $x18[] = $x2f;
                $x18[] = '</div>';
            }
        }
        $x18[] = '</li>';
        $x18   = implode("\n", $x18);
        return $x18;
    }
    public function renderCategoriesMenuHtml($x3c = FALSE, $x11 = 0, $x15 = '', $x16 = '')
    {
        $this->_isAccordion = $x3c;
        $this->_isWide      = Mage::helper('ultramegamenu')->getCfg('mainmenu/wide_menu');
        $x3d                = array();
        foreach ($this->getStoreCategories() as $x1d) {
            if ($x1d->getIsActive()) {
                $x3d[] = $x1d;
            }
        }
        $x3e = count($x3d);
        $x3f = ($x3e > 0);
        if (!$x3f) {
            return '';
        }
        $x18 = '';
        $x3b = 0;
        foreach ($x3d as $x10) {
            $x18 .= $this->_renderCategoryMenuItemHtml($x10, $x11, ($x3b == $x3e - 1), ($x3b == 0), true, $x15, $x16, true);
            $x3b++;
        }
        return $x18;
    }
    public function renderMe($x3c, $x40 = 0, $x41 = 0)
    {
        $x42 = '';
        $x43 = '';
        if ($x40 === 'parent_no_siblings') {
            if ($x44 = Mage::registry('current_category')) {
                $x42 = $x44->getId();
                $x43 = $x44->getLevel();
            }
        }
        $this->_isAccordion = $x3c;
        $this->_isWide      = Mage::helper('ultramegamenu')->getCfg('mainmenu/wide_menu');
        $x11                = 0;
        $x15                = '';
        $x16                = '';
        $x45                = $this->_getParentCategoryId($x40);
        $x46                = $this->_getCategoriesByParent($x45, $x41);
        $x3d                = array();
        /*foreach ($x46 as $x1d) {
            if ($x1d->getIsActive()) {
                if ($x40 === 'parent_no_siblings') {
                    if ($x43 !== '' && $x1d->getLevel() == $x43 && $x1d->getId() != $x42) {
                        continue;
                    }
                }
                $x3d[] = $x1d;
            }
        }*/
        $x3e = count($x3d);
        $x3f = ($x3e > 0);
        if (!$x3f) {
            return '';
        }
        $x18 = '';
        $x3b = 0;
        foreach ($x3d as $x10) {
            $x18 .= $this->_renderCategoryMenuItemHtml($x10, $x11, ($x3b == $x3e - 1), ($x3b == 0), true, $x15, $x16, true);
            $x3b++;
        }
        return $x18;
    }
    protected function _getCategoriesByParent($x45 = 0, $x41 = 0, $x47 = false, $x48 = false, $x49 = true)
    {
        $x10 = Mage::getModel('catalog/category');
        if ($x45 === NULL || !$x10->checkId($x45)) {
            return array();
        }
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $x46 = $this->_getCategoriesByParentFlat($x45, $x41, $x47, $x48, $x49);
        } else {
            $x46 = $x10->getCategories($x45, $x41, $x47, $x48, $x49);
        }
        return $x46;
    }
    protected function _getCategoriesByParentFlat($x45 = 0, $x41 = 0, $x47 = false, $x48 = false, $x49 = true)
    {
        $x4a = Mage::getResourceModel('catalog/category_flat');
        return $x4a->getCategories($x45, $x41, $x47, $x48, $x49);
    }
    protected function _getParentCategoryId($x40)
    {
        $x45 = NULL;
        /*if ($x40 === 'current') {
            $x44 = Mage::registry('current_category');
            if ($x44) {
                $x45 = $x44->getId();
            }
        } elseif ($x40 === 'parent') {
            $x44 = Mage::registry('current_category');
            if ($x44) {
                $x45 = $x44->getParentId();
            }
        } elseif ($x40 === 'parent_no_siblings') {
            $x44 = Mage::registry('current_category');
            if ($x44) {
                $x45 = $x44->getParentId();
            }
        } elseif ($x40 === 'root' || !$x40) {
            $x45 = Mage::app()->getStore()->getRootCategoryId();
        } elseif (is_numeric($x40)) {
            $x45 = intval($x40);
        }
        $x4b = Mage::helper('ultramegamenu')->getCfg('sidemenu/fallback');
        if ($x45 === NULL && $x4b) {
            $x45 = Mage::app()->getStore()->getRootCategoryId();
        }*/
        return $x45;
    }
    protected function _getNumberOfProducts($x10)
    {
        return Mage::getModel('catalog/layer')->setCurrentCategory($x10->getID())->getProductCollection()->getSize();
    }
    public function renderBlockTitle()
    {
        $x20 = Mage::helper('ultramegamenu');
        $x44 = Mage::registry('current_category');
        if (!$x44) {
            $x4b = $x20->getCfg('sidemenu/fallback');
            if ($x4b) {
                $x4c = $x20->getCfg('sidemenu/block_name_fallback');
                if ($x4c) {
                    return $x4c;
                }
            }
        }
        $x4d = $this->getBlockName();
        if ($x4d === NULL) {
            $x4d = $x20->getCfg('sidemenu/block_name');
        }
        $x4e = '';
        if ($x44) {
            $x4e = $x44->getName();
        }
        $x4d = str_replace('[current_category]', $x4e, $x4d);
        return $x4d;
    }
    protected function _getCatBlock($x21, $x4f)
    {
        if (!$this->_tplProcessor) {
            $this->_tplProcessor = Mage::helper('cms')->getBlockTemplateProcessor();
        }
        return $this->_tplProcessor->filter(trim($x21->getData($x4f)));
    }
    protected function _getCategoryLabelHtml($x21, $x11)
    {
        $x50 = $x21->getData('umm_cat_label');
        if ($x50) {
            $x51 = trim(Mage::helper('ultramegamenu')->getCfg('category_labels/' . $x50));
            if ($x51) {
                if ($x11 == 0) {
                    return ' <span class="cat-label cat-label-' . $x50 . ' pin-bottom">' . $x51 . '</span>';
                } else {
                    return ' <span class="cat-label cat-label-' . $x50 . '">' . $x51 . '</span>';
                }
            }
        }
        return '';
    }
}