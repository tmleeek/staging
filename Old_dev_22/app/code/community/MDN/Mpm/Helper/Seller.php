<?php

class MDN_Mpm_Helper_Seller extends Mage_Core_Helper_Abstract {

    public function getSellersAsJson(){

        ini_set('memory_limit', '1024M');

        $key = 'sellers';
        $sellers = Mage::Helper('Mpm/Cache')->load($key);

        if($sellers === false){

            $sellers = Mage::Helper('Mpm/Carl')->jsonListSellers();
            Mage::Helper('Mpm/Cache')->add($key, $sellers);

        }

        return $sellers;
    }

    public function searchFromTokenInput($input){

        $hits = array();
        $sellers = json_decode($this->getSellersAsJson(), true);

        foreach($sellers as $seller){

            if(preg_match('#'.$input.'#', $seller['name'])){

                $hits[] = $seller;

            }

        }

        return json_encode($hits);

    }

    public function getCompetitorsData(){

        ini_set('memory_limit', '1024M');

        $competitorsData = array();
        $json = $this->getSellersAsJson();
        $competitors = json_decode($json, true);
        foreach($competitors as $competitor) {
            $competitorsData[$competitor['id']] = $competitor['name'];
        }
        return $competitorsData;

    }

    public function getPrePopulateAsJson(){

        ini_set('memory_limit', '1024M');

        $prePopulate = array();

        $values = Mage::registry('ignore_sellers_values');
        if(!empty($values)) {

            $competitorsData = $this->getCompetitorsData();

            $sellersValues = json_decode($values);
            foreach($sellersValues as $channel => $sellers) {
                foreach($sellers as $seller) {
                    if(isset($competitorsData[$channel.':'.$seller])) {
                        $prePopulate[] = array(
                            'id' => $channel.':'.$seller,
                            'name' => $competitorsData[$channel.':'.$seller],
                        );
                    }
                }
            }
        }

        return json_encode($prePopulate);

    }

}