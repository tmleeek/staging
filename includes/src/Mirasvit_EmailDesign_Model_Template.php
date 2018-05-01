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


class Mirasvit_EmailDesign_Model_Template extends Mage_Newsletter_Model_Template
{
    protected $_areas  = null;
    protected $_design = null;

    protected function _construct()
    {
        $this->_init('emaildesign/template');
    }

    public function getAreas()
    {
        if ($this->_areas == null) {
            if ($this->getDesign()) {
                $this->_areas = $this->getDesign()->getAreas();
            } else {
                $this->_areas = array('content' => 'Content');
            }
        }

        return $this->_areas;
    }

    public function getDesign()
    {
        if ($this->_design == null && $this->getDesignId()) {
            $this->_design = Mage::getModel('emaildesign/design')->load($this->getDesignId());
        }

        return $this->_design;
    }

    public function getAreaContent($code)
    {
        $areas = $this->getAreasContent();
        if (isset($areas[$code])) {
            return $areas[$code];
        }

        return false;
    }

    public function setAreaContent($code, $content)
    {
        $areas = $this->getAreasContent();
        $areas[$code] = $content;
        $this->setAreasContent($areas);

        return $this;
    }

    public function getPreviewContent()
    {
        $variables             = Mage::helper('email/event')->getRandomEventArgs();
        $variables['order']    = Mage::getModel('sales/order')->load($variables['order_id']);
        $variables['quote']    = Mage::getModel('sales/quote')->setSharedStoreIds(array_keys(Mage::app()->getStores()))
            ->load($variables['quote_id']);
        $variables['customer'] = Mage::getModel('customer/customer')->load($variables['customer_id']);

        $result =  $this->getProcessedTemplate($variables);

        if ($this->getDesign()->getTemplateType() == Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_TEXT) {
            $result = nl2br($result);
        }

        return $result;
    }

    public function getProcessedTemplate(array $variables = array(), $usePreprocess = false)
    {
        $areas = new Varien_Object();
        foreach ($this->getDesign()->getAreas() as $code => $name) {
            $content = $this->getAreaContent($code);
            $content = $this->getProcessedText($content, $variables, $usePreprocess);
            if (trim($content)) {
                $areas->setData($code, $content);
            }
        }
        $variables['area'] = $areas;

        $result = $this->getProcessedText($this->getDesign()->getTemplate(), $variables, $usePreprocess);

        if ($this->getDesign()->getTemplateType() == Mirasvit_EmailDesign_Model_Design::TEMPLATE_TYPE_HTML) {
            $result = Mage::helper('emaildesign')->styleHtml($result);
        }

        return $result;
    }

    public function getProcessedText($text, array $variables = array(), $usePreprocess = false)
    {
        $processor = Mage::getModel('emaildesign/template_filter');
        $processor->setStoreId(Mage::app()->getStore());

        if (!$this->_preprocessFlag) {
            $variables['this'] = $this;
        }

        $variables = array_merge($variables, Mage::helper('emaildesign/variable')->getStoreVariables($processor->getStoreId()));

        if (!isset($variables['store'])) {
            $variables['store'] = Mage::app()->getStore();
        }

        $variables['subject']  = $this->getProcessedTemplateSubject($variables);

        $processor
            ->setIncludeProcessor(array($this, 'getInclude'))
            ->setVariables($variables);

        return $processor->filter($text);
    }

    public function getTemplateSubject()
    {
        return $this->getSubject();
    }

    public function validate()
    {

    }

    public function getInclude($templateCode, array $variables)
    {
        $processor = Mage::getModel('emaildesign/template_filter');

        $processor->setVariables($variables);
        $template = $this->_getTemplate($templateCode);

        return $processor->filter($template['template_text']);
    }

    public function export()
    {
        $this->setAreasContent64(base64_encode(serialize($this->getAreasContent())));
        $this->setDesignTitle($this->getDesign()->getTitle());

        $xml = $this->toXml(array('title', 'description', 'subject', 'design_title', 'areas_content64'));

        $path = Mage::getSingleton('emaildesign/config')->getTemplatePath().DS.$this->getTitle().'.xml';

        file_put_contents($path, $xml);

        return $path;
    }

    public function import($path)
    {
        $content   = file_get_contents($path);
        $xml       = new Varien_Simplexml_Element($content);
        $template  = $xml->asArray();

        $template['areas_content'] = base64_decode($template['areas_content64']);

        $model = $this->getCollection()
            ->addFieldToFilter('title', $template['title'])
            ->getFirstItem();
        $model->addData($template);

        $design = Mage::getModel('emaildesign/design')->getCollection()
            ->addFieldToFilter('title', $template['design_title'])
            ->getFirstItem();

        $model->setDesignId($design->getId())
            ->save();

        return $model;
    }
}