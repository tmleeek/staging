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


class Mirasvit_EmailDesign_Model_Design extends Mage_Core_Model_Abstract
{
    const TEMPLATE_TYPE_HTML = 'html';
    const TEMPLATE_TYPE_TEXT = 'text';

    protected function _construct()
    {
        $this->_init('emaildesign/design');
    }

    public function getAreas()
    {
        $areas   = array();
        $matches = array();

        preg_match_all('/var area\.([a-zA-z_0-9]*)/', $this->getTemplate(), $matches);

        foreach ($matches[1] as $code) {
            $label = $code;
            $label = str_replace('_', ' ', $label);
            $label = ucwords($label);
            $areas[$code]['label'] = $label;
        }

        foreach (array(
            Mirasvit_EmailDesign_Model_Template_Filter::CONSTRUCTION_IVAR_PATTERN_COM => 'ivarDirective',
            Mirasvit_EmailDesign_Model_Template_Filter::CONSTRUCTION_IVAR_PATTERN     => 'ivarDirective',
            ) as $pattern => $directive) {
            if (preg_match_all($pattern, $this->getTemplate(), $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $index => $construction) {
                    $code = $construction[1];
                    $code = str_replace('area.', '', $code);
                    $example = $construction[2];
                    $spaceLen = 2;
                    $char = ' ';

                    while($char == ' ') {
                        $char = substr($example, $spaceLen, 1);
                        $spaceLen++;
                    }

                    if ($spaceLen > 1) {
                        $example = str_replace(str_repeat(' ', $spaceLen), '', $example);
                    }

                    if (!isset($areas[$code]['example'])) {
                        $areas[$code]['example'] = $example;
                    }
                }
            }
        }

        return $areas;
    }

    public function getPreviewContent()
    {
        $template = $this->getTemplate();
        if ($this->getTemplateType() == self::TEMPLATE_TYPE_TEXT) {
            $template = nl2br($template);
        }

        return $template;
    }

    public function export()
    {
        $this->setTemplate64(base64_encode($this->getTemplate()));
        $this->setStyles64(base64_encode($this->getStyles()));

        $xml = $this->toXml(array('title', 'description', 'template_type', 'styles64', 'template64'));

        $path = Mage::getSingleton('emaildesign/config')->getDesignPath().DS.$this->getTitle().'.xml';

        file_put_contents($path, $xml);

        return $path;
    }

    public function import($path)
    {
        $content = file_get_contents($path);
        $xml     = new Varien_Simplexml_Element($content);
        $design  = $xml->asArray();

        if (isset($design['styles64'])) {
            $design['styles'] = base64_decode($design['styles64']);
        }

        if (isset($design['template64'])) {
            $design['template'] = base64_decode($design['template64']);
        }

        $model = $this->getCollection()
            ->addFieldToFilter('title', $design['title'])
            ->getFirstItem();
        $model->addData($design)
            ->save();

        return $model;
    }

    public function importMailchimp($path)
    {
        $content = file_get_contents($path);

        $content = Mage::helper('emaildesign/mailchimp')->convert($content);

        $info = pathinfo($path);

        $title = $info['filename'];

        $model = $this->getCollection()
            ->addFieldToFilter('title', $title)
            ->getFirstItem();

        $model->setTitle($title)
            ->setDescription('Imported Design')
            ->setTemplate($content)
            ->save();

        return $model;
    }
}