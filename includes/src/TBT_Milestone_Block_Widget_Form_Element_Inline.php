<?php

class TBT_Milestone_Block_Widget_Form_Element_Inline extends Varien_Data_Form_Element_Abstract
{
    /**
     * Sort child elements by specified data key
     * @var string
     */
    protected $_sortChildrenByKey = '';

    /**
     * Children sort direction
     * @var int
     */
    protected $_sortChildrenDirection = SORT_ASC;

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $renderer = Mage::app()->getLayout()->createBlock('tbtmilestone/widget_form_renderer_fieldset_element_inline');
        $this->setRenderer($renderer);
    }

    /**
     * @see Varien_Data_Form_Abstract::addField()
     */
    public function addField($elementId, $type, $config, $after=false)
    {
        $config['no_span'] = true;

        if (isset($config['name'])) {
            $config['local_name'] = $config['name'];
            $config['name'] = $this->getName() . '[' . $config['name'] . ']';
        }

        if (!isset($config['style'])) {
            $config['style'] =  "";
        }

        if (isset($config['width'])) {
            $config['style'] .= " width: {$config['width']};";
        }

        if (isset($config['align'])) {
            $config['style'] .= " text-align: {$config['align']};";
        }

        return parent::addField($elementId, $type, $config, $after);
    }

    /**
     * Return html of each child element in an array, keyed by the child's given name.
     * @return array
     */
    public function getChildrenHtmlArray()
    {
        $childrenHtml = array();
        foreach ($this->getSortedElements() as $element) {
            $childrenHtml[$element->getLocalName()] = $element->toHtml();
        }
        return $childrenHtml;
    }

    /**
     * Commence sorting elements by values by specified data key
     *
     * @param string $key
     * @param int $direction
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function setSortElementsByAttribute($key, $direction = SORT_ASC)
    {
        $this->_sortChildrenByKey = $key;
        $this->_sortDirection = $direction;
        return $this;
    }

    /**
     * Get sorted elements as array
     *
     * @return array
     */
    public function getSortedElements()
    {
        $elements = array();
        // sort children by value by specified key
        if ($this->_sortChildrenByKey) {
            $sortKey = $this->_sortChildrenByKey;
            $uniqueIncrement = 0; // in case if there are elements with same values
            foreach ($this->getElements() as $e) {
                $key = '_' . $uniqueIncrement;
                if ($e->hasData($sortKey)) {
                    $key = $e->getDataUsingMethod($sortKey) . $key;
                }
                $elements[$key] = $e;
                $uniqueIncrement++;
            }
            ksort($elements, $this->_sortChildrenDirection);
            $elements = array_values($elements);
        }
        else {
            foreach ($this->getElements() as $element) {
                $elements[] = $element;
            }
        }
        return $elements;
    }
}
