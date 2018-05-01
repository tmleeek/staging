<?php

class MondialRelay_Pointsrelais_Model_Observer
{
	public function getConfigData($field)
	{
        $path = 'carriers/pointsrelais/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
	}
    
    public function changeAddress()
    {
        $address = Mage::helper('checkout/cart')->getQuote()->getShippingAddress();
        $_shippingMethod = explode("_",$address->getShippingMethod());
        
        if ($_shippingMethod[0] == 'pointsrelais')
        {
            //On récupère l'identifiant du point relais
            $Num = $_shippingMethod[1];
            
            // On met en place les paramètres de la requète
            $params = array(
                           'Enseigne'  => $this->getConfigData('enseigne'),
                           'Num'       => $Num,
                           'Pays'      => $address->getCountryId()
            );
            
            //On crée le code de sécurité
            $code = implode("",$params);
            $code .= $this->getConfigData('cle');
            
            //On le rajoute aux paramètres
            $params["Security"] = strtoupper(md5($code));
            
            // On se connecte
            $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");
            
            // Et on effectue la requète
            $detail_pointrelais = $client->WSI2_DetailPointRelais($params)->WSI2_DetailPointRelaisResult;
            
            $address->setCompany($detail_pointrelais->LgAdr1)
                    ->setStreet(strtolower($detail_pointrelais->LgAdr2) . strtolower($detail_pointrelais->LgAdr3) . strtolower($detail_pointrelais->LgAdr4) )
                    ->setPostcode($detail_pointrelais->CP)
                    ->setCity($detail_pointrelais->Ville);
                    
            Mage::helper('checkout/cart')->getQuote()->setShippingAddress($address);

        }
    }
}