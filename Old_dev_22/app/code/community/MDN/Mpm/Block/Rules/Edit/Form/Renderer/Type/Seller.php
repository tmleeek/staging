<?php

class MDN_Mpm_Block_Rules_Edit_Form_Renderer_Type_Seller extends Varien_Data_Form_Element_Abstract
{

    public function getElementHtml()
    {

        $url = Mage::Helper('adminhtml')->getUrl('adminhtml/Mpm_Seller/tokenInput');
        $prePopulate = Mage::Helper('Mpm/Seller')->getPrePopulateAsJson();

        return <<<HTML
        <input type="text" id="ignore-seller-input" name="ignore_sellers" />
        <script type="text/javascript">
            jQuery().ready(function($){
                $("#ignore-seller-input").tokenInput("$url", {
                    theme: "facebook",
                    prePopulate: $prePopulate,
                    hintText: "",
                    "preventDuplicate": true
                });
            });
        </script>
HTML;
    }
}