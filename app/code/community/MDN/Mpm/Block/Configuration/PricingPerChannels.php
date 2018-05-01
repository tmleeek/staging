<?php

class MDN_Mpm_Block_Configuration_PricingPerChannels extends Mage_Adminhtml_Block_Widget_Container {

    public function getPrePopulate($channelCode){

        $prePopulate = array();
        $sellerId = Mage::getStoreConfig('mpm/repricing/seller_id_'.$channelCode);

        if(!empty($sellerId)){

            $sellers = json_decode(Mage::Helper('Mpm/Seller')->getSellersAsJson(), true);
            foreach($sellers as $seller){
                if($seller['id'] == $sellerId){

                    $prePopulate[] = $seller;

                }

            }

        }

        return json_encode($prePopulate);

    }

    public function getChannelsSubscribed() {

        try {
            $channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed();
        }catch(Exception $e){
            $channels = $e->getMessage();
        }

        return $channels;

    }

    public function getStores(){

        $retour = array();
        $stores = mage::getModel('Core/Store')
            ->getCollection();

        foreach ($stores as $store) {
            $retour[$store->getstore_id()] = $store->getname();
        }

        return $retour;

    }

}