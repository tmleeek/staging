<?php
class MondialRelay_Pointsrelais_Block_Info extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	
	public $enseigne = 'test';
	
	public function getConfigData($field)
	{
        $path = 'carriers/pointsrelais/'.$field;
        return Mage::getStoreConfig($path, $this->getStore());
	}
	
	public function getDetailPointRelais()
	{		
		// On met en place les paramètres de la requète
		$params = array(
					   'Enseigne'  => $this->getConfigData('enseigne'),
					   'Num'       => $this->getRequest()->getPost('Id_Relais'),
					   'Pays'      => $this->getRequest()->getPost('Pays')
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
		return $detail_pointrelais;
	}
	
    
}