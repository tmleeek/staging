<?php

class MDN_Mpm_Block_Configuration_WebservicesCredentials extends Mage_Adminhtml_Block_Widget_Container {

    public function getSubscribedChannelsWithApi(){

        return Mage::Helper('Mpm/Configuration')->getSubscribedChannelsWithApi();

    }


}