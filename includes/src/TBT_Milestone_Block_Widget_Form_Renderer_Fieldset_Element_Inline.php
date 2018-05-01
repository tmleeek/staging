<?php

class TBT_Milestone_Block_Widget_Form_Renderer_Fieldset_Element_Inline extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    protected function _construct()
    {
        $this->setTemplate('tbtmilestone/widget/form/renderer/fieldset/element/inline.phtml');
    }

    /**
     * Insert elements into a string using {{child_name}} notation.
     * @param string $subject The format string containing {{child_name}} placeholders.
     * @param array $elements An array of elements' HTML, keyed by child_name.
     * @return string
     */
    public function insertElements($subject, $elements)
    {
        foreach ($elements as $key => $element) {
            $subject = str_replace('{{' . $key . '}}', $element, $subject);
        }
        return $subject;
    }
}
