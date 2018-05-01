<?php

class TBT_Milestone_Block_Widget_Form_Element_Sectiontoggle extends Varien_Data_Form_Element_Abstract
{
    /**
     * @see Varien_Data_Form_Element_Abstract::getHtml()
     */
    public function getHtml()
    {
        $expandIcon = Mage::getDesign()->getSkinUrl('images/rule_component_add.gif');
        $collapseIcon = Mage::getDesign()->getSkinUrl('images/rule_component_remove.gif');
        $isExpanded = $this->getIsExpanded() ? 'true' : 'false';
        $disableOnCollapse= $this->getDisableOnCollapse() ? 'true' : 'false';

        return <<<HTML
            <span class="rule-param" id="{$this->getHtmlId()}">
                <a href="javascript://" class="rule-param-add">
                    <img src="{$expandIcon}" class="v-middle icon-expand" style="display: none;" />
                    <img src="{$collapseIcon}" class="v-middle icon-collapse" style="display: none;" />
                </a>
                <a href="javascript://" class="label v-middle">
                    <span class="label-expand" style="display: none;">
                        {$this->getLabelExpand()}
                    </span>
                    <span class="label-collapse" style="display: none;">
                        {$this->getLabelCollapse()}
                    </span>
                </a>
            </span>

            <script type="text/javascript">
                Event.observe(document, 'dom:loaded', function() {
                    var i = 0,
                        links = $$('#{$this->getHtmlId()} a'),
                        sections = $$('{$this->getSectionSelector()}'),
                        sectionInputs = $$('{$this->getSectionSelector()} :input'),
                        isExpanded = {$isExpanded},
                        disableOnCollapse = {$disableOnCollapse},
                        elementsForExpanding = $$('#{$this->getHtmlId()} .icon-expand, #{$this->getHtmlId()} .label-expand'),
                        elementsForCollapsing = $$('#{$this->getHtmlId()} .icon-collapse, #{$this->getHtmlId()} .label-collapse');

                    if (isExpanded) {
                        // Show elements that should be used to collapse the section.
                        for (i = 0; i < elementsForCollapsing.length; i += 1) {
                            elementsForCollapsing[i].show();
                        }
                    } else {
                        // Show elements that should be used to expand the section.
                        for (i = 0; i < elementsForExpanding.length; i += 1) {
                            elementsForExpanding[i].show();
                        }

                        // Hide the section and optionally disable any input fields within it.
                        for (i = 0; i < sections.length; i += 1) {
                            sections[i].hide();
                        }
                        if (disableOnCollapse) {
                            for (j = 0; j < sectionInputs.length; j += 1) {
                                sectionInputs[j].disabled = 'disabled';
                            }
                        }
                    }

                    for (i = 0; i < links.length; i += 1) {
                        Event.observe(links[i], 'click', function(e) {
                            var j;
                            if (isExpanded) {
                                isExpanded = false;
                                for (j = 0; j < elementsForCollapsing.length; j += 1) {
                                    elementsForCollapsing[j].hide();
                                }
                                for (j = 0; j < elementsForExpanding.length; j += 1) {
                                    elementsForExpanding[j].show();
                                }
                                for (j = 0; j < sections.length; j += 1) {
                                    sections[j].hide();
                                }
                                if (disableOnCollapse) {
                                    for (j = 0; j < sectionInputs.length; j += 1) {
                                        sectionInputs[j].disabled = 'disabled';
                                    }
                                }
                            } else {
                                isExpanded = true;
                                for (j = 0; j < elementsForExpanding.length; j += 1) {
                                    elementsForExpanding[j].hide();
                                }
                                for (j = 0; j < elementsForCollapsing.length; j += 1) {
                                    elementsForCollapsing[j].show();
                                }
                                for (j = 0; j < sections.length; j += 1) {
                                    sections[j].show();
                                }
                                if (disableOnCollapse) {
                                    for (j = 0; j < sectionInputs.length; j += 1) {
                                        sectionInputs[j].disabled = '';
                                    }
                                }
                            }
                        });
                    }
                });
            </script>
HTML;
    }
}
