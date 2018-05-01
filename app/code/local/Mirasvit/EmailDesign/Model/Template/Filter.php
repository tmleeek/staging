<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Model_Template_Filter extends Mage_Core_Model_Email_Template_Filter
{
    const CONSTRUCTION_FOREACH_PATTERN   = '/{{foreach\s*(.*?)}}(.*?){{\\/foreach\s*}}/si';
    const CONSTRUCTION_IVAR_PATTERN      = '/{{ivar\s*(.*?)}}(.*?){{\\/ivar\s*}}/si';
    const CONSTRUCTION_IVAR_PATTERN_COM  = '/<!--{{ivar\s*(.*?)}}-->(.*?)<!--{{\\/ivar\s*}}-->/si';

    public function filter($value)
    {
        foreach (array(
            self::CONSTRUCTION_DEPEND_PATTERN   => 'dependDirective',
            self::CONSTRUCTION_IF_PATTERN       => 'ifDirective',
            self::CONSTRUCTION_FOREACH_PATTERN  => 'foreachDirective',
            self::CONSTRUCTION_IVAR_PATTERN_COM => 'ivarDirective',
            self::CONSTRUCTION_IVAR_PATTERN     => 'ivarDirective',
            ) as $pattern => $directive) {

            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $index => $construction) {
                    $replacedValue = '';
                    $callback      = array($this, $directive);

                    if(!is_callable($callback)) {
                        continue;
                    }

                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (Exception $e) {
                        throw $e;
                    }

                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        if (preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach($constructions as $index => $construction) {
                $replacedValue = '';
                $callback      = array($this, $construction[1].'Directive');

                if(!is_callable($callback)) {
                    continue;
                }

                try {
                    $replacedValue = call_user_func($callback, $construction);
                } catch (Exception $e) {
                    throw $e;
                }

                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }

        return $value;
    }

    public function varDirective($construction)
    {
        if (count($this->_templateVars)==0) {
            return $construction[0];
        }

        $replacedValue = $this->_getVariable($construction[2], '');

        return $replacedValue;
    }

    protected function _getVariable($value, $default = '{no_value_defined}')
    {
        $result     = parent::_getVariable($value, $default);
        $formatters = explode('|', $value);

        for ($i = 1; $i < count($formatters); $i++) {
            $formatters[$i] = explode(':', $formatters[$i]);
            $modifier       = 'modifier'.trim(ucfirst($formatters[$i][0]));
            $callback       = array($this, $modifier);
            $result         = call_user_func($callback, $result, $formatters[$i]);
        }

        return $result;
    }

    public function foreachDirective($construction)
    {
        $result = '';
        $params = $this->_getIncludeParameters(' '.$construction[1]);
        $var    = $params['var'];

        if (!is_array($var)) {
            return $result;
        }

        if (isset($params['param'])) {
            $param = $params['param'];

            foreach ($var as $varItem) {
                if ($varItem instanceof Mage_Sales_Model_Order_Item
                    && $varItem->getData('qty_refunded') > 0) {
                    continue;
                }
                $this->_templateVars[$param] = $varItem;
                $this->setVariables($this->_templateVars);
                $result .= $this->filter($construction[2]);
            }

            unset($this->_templateVars[$param]);
        } elseif (isset($params['template'])) {
            $template = $params['template'];

            foreach ($var as $varItem) {
                $this->_templateVars['row_item'] = $varItem;
                $this->setVariables($this->_templateVars);
                $result .= $this->includeDirective(array('', '', ' template="' . $template . '"'));
            }

            unset($this->_templateVars['row_item']);
        }

        return $result;
    }

    public function ivarDirective($construction)
    {
        $replacedValue = $this->_getVariable($construction[1], '');

        return $replacedValue;
    }

    public function thumbnailDirective($construction)
    {
        return $this->getImage('thumbnail', $construction);
    }

    public function smallimageDirective($construction)
    {
        return $this->getImage('small_image', $construction);
    }

    public function imageDirective($construction)
    {
        return $this->getImage('image', $construction);
    }

    public function getImage($imageType, $construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (isset($params['source']) && $params['source']) {
            if (!$source = $this->_getVariable($params['source'], false)) {
                return false;
            }

            if (is_object($source)) {
                if ($source instanceof Mage_Catalog_Model_Product) {
                    $product = $source;
                } else {
                    return false;
                }
            } elseif (is_scalar($source)) {
                $product = Mage::getModel('catalog/product')->load($source);
                if ($source != $product->getId()) {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }

        if (!$thumbnail = $product->getData($imageType)) {
            return false;
        }


        $imgDimension = isset($params['size']) ? $params['size'] : 56;

        try {
            $url = Mage::helper('catalog/image')
                ->init($product, $imageType)
                ->resize($imgDimension);
        } catch (Exception $e) {
            return false;
        }

        return $url;
    }

    public function modifierFormatPrice($value)
    {
        return Mage::app()->getStore($this->getStoreId())->formatPrice($value, false);
    }

    public function modifierFormatDateTime($value)
    {
        $params = func_get_args();
        array_shift($params[1]);
        $formatStr = implode(':', $params[1]);
        switch ($formatStr) {
            case 'full':
                $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_FULL;
                break;
            case 'long':
                $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_LONG;
                break;
            case 'medium':
                $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM;
                break;
            case 'short':
                $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT;
                break;
            default:
                $formatType = null;
                break;
        }

        if ($formatType) {
            return Mage::helper('core')->formatDate($value, $formatType);
        }

        return $value;
    }
}