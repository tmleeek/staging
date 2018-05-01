<?php

class Tatva_Shipping_Model_Tempcolissimowebservicedata extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('tatvashipping/tempcolissimowebservicedata');
    }

    public function getWebserviceData($params)
    {

        $client = new SoapClient(Mage::getStoreConfig('tatva/tatva_settings/webservice'));
        $res = '';
        //$res   = $client->findRDVPointRetraitAcheminement($params);

        foreach($res as $point_relais ) { //echo $i.'=='.$point_relais->errorCode;
                     	//echo '<pre>';print_r($point_relais->errorCode);echo '</pre>';exit;
                         if($point_relais->errorCode == 0)
                         {

                         if(isset($point_relais->listePointRetraitAcheminement))
                         {
                              if(count($point_relais->listePointRetraitAcheminement) > 1)
								{
								  foreach($point_relais->listePointRetraitAcheminement as $point_list)
		                              {
		                              if($point_list->congesPartiel == '' && $point_list->congesTotal == '')
								      {
		                              $type = '';

		                              $latitude = $point_list->coordGeolocalisationLatitude;

		                              $langitude = $point_list->coordGeolocalisationLongitude;
		                              if($point_list->typeDePoint == 'A2P' || $point_list->typeDePoint == 'BDP')
		                              {
		                                $type = 'local store';

		                              }
		                              else if($point_list->typeDePoint == 'BPR' || $point_list->typeDePoint == 'CDI' || $point_list->typeDePoint == 'ACP')
		                              {
		                                $type = 'post office';
		                              }
		                              else if($point_list->typeDePoint == 'CIT')
		                              {
		                                $type = 'cityssimo space';
		                              }
		                              $mobality = 0;
		                              if($point_list->accesPersonneMobiliteReduite == 1)
		                              {
		                                $mobality = 1;
		                              }

		                              if($point_list->adresse2 == ' ')
		                              {
		                                $point_list->adresse2 = '';
		                              }
		                              if($point_list->adresse3 == ' ')
		                              {
		                                $point_list->adresse3 = '';
		                              }
		                              if(eregi(",",$point_list->coordGeolocalisationLatitude))
		                              {
		                                $latitude = eregi_replace(",",".",$point_list->coordGeolocalisationLatitude);
		                              }
		                              if(eregi(",",$point_list->coordGeolocalisationLongitude))
		                              {
		                                $langitude = eregi_replace(",",".",$point_list->coordGeolocalisationLongitude);
		                              }

		                              $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		                              $model = Mage::getModel('tatvashipping/tempcolissimowebservicedata');
		                              $model->setdistance($point_list->distanceEnMetre/1000);
		                              $model->settype($type);
		                              $model->setname(str_replace('"',"",$point_list->nom));
		                              $model->setaddress(str_replace('"',"",$point_list->adresse1));
		                              $model->setaddress2(str_replace('"',"",$point_list->adresse2));
		                              $model->setaddress3(str_replace('"',"",$point_list->adresse3));
		                              $model->setcity($point_list->localite);
		                              $model->setpostalcode($point_list->codePostal);
		                              $model->setlatitude($latitude);
		                              $model->setlongitude($langitude);
		                              $model->setdimanche($point_list->horairesOuvertureDimanche);
		                              $model->setjeudi($point_list->horairesOuvertureJeudi);
		                              $model->setlundi($point_list->horairesOuvertureLundi);
		                              $model->setmardi($point_list->horairesOuvertureMardi);
		                              $model->setmercredi($point_list->horairesOuvertureMercredi);
		                              $model->setsamedi($point_list->horairesOuvertureSamedi);
		                              $model->setvendredi($point_list->horairesOuvertureVendredi);
		                              $model->setidentifiant($point_list->identifiant);
		                              $model->setcustomerId($customer_id);
		                              $model->setaccespersonnemobilitereduite($mobality);
		                              $model->setrelaycode($point_list->typeDePoint);


		                              $model->save();
                                    }
                                   }
								}
								else
								{

								if($point_relais->listePointRetraitAcheminement->congesPartiel == '' && $point_relais->listePointRetraitAcheminement->congesTotal == '')
								      {
								      $type = '';

		                              $latitude = $point_relais->listePointRetraitAcheminement->coordGeolocalisationLatitude;

		                              $langitude = $point_relais->listePointRetraitAcheminement->coordGeolocalisationLongitude;
		                              if($point_relais->listePointRetraitAcheminement->typeDePoint == 'A2P' || $point_relais->listePointRetraitAcheminement->typeDePoint == 'BDP')
		                              {
		                                $type = 'local store';

		                              }
		                              else if($point_relais->listePointRetraitAcheminement->typeDePoint == 'BPR' || $point_relais->listePointRetraitAcheminement->typeDePoint == 'CDI' || $point_relais->listePointRetraitAcheminement->typeDePoint == 'ACP')
		                              {
		                                $type = 'post office';
		                              }
		                              else if($point_relais->listePointRetraitAcheminement->typeDePoint == 'CIT')
		                              {
		                                $type = 'cityssimo space';
		                              }
		                              $mobality = 0;
		                              if($point_relais->listePointRetraitAcheminement->accesPersonneMobiliteReduite == 1)
		                              {
		                                $mobality = 1;
		                              }

		                              if($point_relais->listePointRetraitAcheminement->adresse2 == ' ')
		                              {
		                                $point_relais->listePointRetraitAcheminement->adresse2 = '';
		                              }
		                              if($point_relais->listePointRetraitAcheminement->adresse3 == ' ')
		                              {
		                                $point_relais->listePointRetraitAcheminement->adresse3 = '';
		                              }
		                              if(eregi(",",$point_relais->listePointRetraitAcheminement->coordGeolocalisationLatitude))
		                              {
		                                $latitude = eregi_replace(",",".",$point_relais->listePointRetraitAcheminement->coordGeolocalisationLatitude);
		                              }
		                              if(eregi(",",$point_relais->listePointRetraitAcheminement->coordGeolocalisationLongitude))
		                              {
		                                $langitude = eregi_replace(",",".",$point_relais->listePointRetraitAcheminement->coordGeolocalisationLongitude);
		                              }

		                              $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		                              $model = Mage::getModel('tatvashipping/tempcolissimowebservicedata');
		                              $model->setdistance($point_relais->listePointRetraitAcheminement->distanceEnMetre/1000);
		                              $model->settype($type);
		                              $model->setname(str_replace('"',"",$point_relais->listePointRetraitAcheminement->nom));
		                              $model->setaddress(str_replace('"',"",$point_relais->listePointRetraitAcheminement->adresse1));
		                              $model->setaddress2(str_replace('"',"",$point_relais->listePointRetraitAcheminement->adresse2));
		                              $model->setaddress3(str_replace('"',"",$point_relais->listePointRetraitAcheminement->adresse3));
		                              $model->setcity($point_relais->listePointRetraitAcheminement->localite);
		                              $model->setpostalcode($point_relais->listePointRetraitAcheminement->codePostal);
		                              $model->setlatitude($latitude);
		                              $model->setlongitude($langitude);
		                              $model->setdimanche($point_relais->listePointRetraitAcheminement->horairesOuvertureDimanche);
		                              $model->setjeudi($point_relais->listePointRetraitAcheminement->horairesOuvertureJeudi);
		                              $model->setlundi($point_relais->listePointRetraitAcheminement->horairesOuvertureLundi);
		                              $model->setmardi($point_relais->listePointRetraitAcheminement->horairesOuvertureMardi);
		                              $model->setmercredi($point_relais->listePointRetraitAcheminement->horairesOuvertureMercredi);
		                              $model->setsamedi($point_relais->listePointRetraitAcheminement->horairesOuvertureSamedi);
		                              $model->setvendredi($point_relais->listePointRetraitAcheminement->horairesOuvertureVendredi);
		                              $model->setidentifiant($point_relais->listePointRetraitAcheminement->identifiant);
		                              $model->setcustomerId($customer_id);
		                              $model->setaccespersonnemobilitereduite($mobality);
		                              $model->setrelaycode($point_relais->listePointRetraitAcheminement->typeDePoint);


		                              $model->save();
									 }
								}


                            }
                }
			   //$i++;
            }
    }
	
	public function insertMondialRelay($params)
    {
               $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");

				// Et on effectue la requète
				$points_relais = $client->WSI2_RecherchePointRelais($params)->WSI2_RecherchePointRelaisResult;

				// On cr?une m?ode de livraison par point relais
				foreach( $points_relais as $point_relais ) { 
					if ( is_object($point_relais) && trim($point_relais->Num) != '' ) {

                          $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
                          $params_detail = array(
      					   'Enseigne'  => $params['Enseigne'],
      					   'Num'       => $point_relais->Num,
      					   'Pays'      => $params['Pays']
      		              );
                       
                  		//On crée le code de sécurité
                  		$code_1 = implode("",$params_detail);
                  		$code_1 .= Mage::getStoreConfig('carriers/pointsrelais/cle');

                  		//On le rajoute aux paramètres
                  		$params_detail["Security"] = strtoupper(md5($code_1));

                  		// On se connecte
                  		//nisha $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");

		                  // Et on effectue la requète
		                  $detail_pointrelais = $client->WSI2_DetailPointRelais($params_detail)->WSI2_DetailPointRelaisResult;


                          $Lundi = substr($detail_pointrelais->Horaires_Lundi->string[0],-4,2) . ":". substr($detail_pointrelais->Horaires_Lundi->string[0],-2) . "-".substr($detail_pointrelais->Horaires_Lundi->string[1],-4,2) . ":". substr($detail_pointrelais->Horaires_Lundi->string[1],-2)." ".substr($detail_pointrelais->Horaires_Lundi->string[2],-4,2) . ":". substr($detail_pointrelais->Horaires_Lundi->string[2],-2) . "-".substr($detail_pointrelais->Horaires_Lundi->string[3],-4,2) . ":". substr($detail_pointrelais->Horaires_Lundi->string[3],-2);

                          $Mardi = substr($detail_pointrelais->Horaires_Mardi->string[0],-4,2) . ":". substr($detail_pointrelais->Horaires_Mardi->string[0],-2) . "-".substr($detail_pointrelais->Horaires_Mardi->string[1],-4,2) . ":". substr($detail_pointrelais->Horaires_Mardi->string[1],-2)." ".substr($detail_pointrelais->Horaires_Mardi->string[2],-4,2) . ":". substr($detail_pointrelais->Horaires_Mardi->string[2],-2) . "-".substr($detail_pointrelais->Horaires_Mardi->string[3],-4,2) . ":". substr($detail_pointrelais->Horaires_Mardi->string[3],-2);

                          $Mercredi = substr($detail_pointrelais->Horaires_Mercredi->string[0],-4,2) . ":". substr($detail_pointrelais->Horaires_Mercredi->string[0],-2) . "-".substr($detail_pointrelais->Horaires_Mercredi->string[1],-4,2) . ":". substr($detail_pointrelais->Horaires_Mercredi->string[1],-2)." ".substr($detail_pointrelais->Horaires_Mercredi->string[2],-4,2) . ":". substr($detail_pointrelais->Horaires_Mercredi->string[2],-2) . "-".substr($detail_pointrelais->Horaires_Mercredi->string[3],-4,2) . ":". substr($detail_pointrelais->Horaires_Mercredi->string[3],-2);

                          $Jeudi = substr($detail_pointrelais->Horaires_Jeudi->string[0],-4,2) . ":". substr($detail_pointrelais->Horaires_Jeudi->string[0],-2) . "-".substr($detail_pointrelais->Horaires_Jeudi->string[1],-4,2) . ":". substr($detail_pointrelais->Horaires_Jeudi->string[1],-2)." ".substr($detail_pointrelais->Horaires_Jeudi->string[2],-4,2) . ":". substr($detail_pointrelais->Horaires_Jeudi->string[2],-2) . "-".substr($detail_pointrelais->Horaires_Jeudi->string[3],-4,2) . ":". substr($detail_pointrelais->Horaires_Jeudi->string[3],-2);

                         $Vendredi = substr($detail_pointrelais->Horaires_Vendredi->string[0],-4,2) . ":". substr($detail_pointrelais->Horaires_Vendredi->string[0],-2) . "-".substr($detail_pointrelais->Horaires_Vendredi->string[1],-4,2) . ":". substr($detail_pointrelais->Horaires_Vendredi->string[1],-2)." ".substr($detail_pointrelais->Horaires_Vendredi->string[2],-4,2) . ":". substr($detail_pointrelais->Horaires_Vendredi->string[2],-2) . "-".substr($detail_pointrelais->Horaires_Vendredi->string[3],-4,2) . ":". substr($detail_pointrelais->Horaires_Vendredi->string[3],-2);

                        $Samedi = substr($detail_pointrelais->Horaires_Samedi->string[0],-4,2) . ":". substr($detail_pointrelais->Horaires_Samedi->string[0],-2) . "-".substr($detail_pointrelais->Horaires_Samedi->string[1],-4,2) . ":". substr($detail_pointrelais->Horaires_Samedi->string[1],-2)." ".substr($detail_pointrelais->Horaires_Samedi->string[2],-4,2) . ":". substr($detail_pointrelais->Horaires_Samedi->string[2],-2) . "-".substr($detail_pointrelais->Horaires_Samedi->string[3],-4,2) . ":". substr($detail_pointrelais->Horaires_Samedi->string[3],-2);

                        $Dimanche = substr($detail_pointrelais->Horaires_Dimanche->string[0],-4,2) . ":". substr($detail_pointrelais->Horaires_Dimanche->string[0],-2) . "-".substr($detail_pointrelais->Horaires_Dimanche->string[1],-4,2) . ":". substr($detail_pointrelais->Horaires_Dimanche->string[1],-2)." ".substr($detail_pointrelais->Horaires_Dimanche->string[2],-4,2) . ":". substr($detail_pointrelais->Horaires_Dimanche->string[2],-2) . "-".substr($detail_pointrelais->Horaires_Dimanche->string[3],-4,2) . ":". substr($detail_pointrelais->Horaires_Dimanche->string[3],-2);


                              $model = Mage::getModel('tatvashipping/tempcolissimowebservicedata');

                              $model->settype("mondial relay");
                              $model->setname(str_replace('"',"",$point_relais->LgAdr1));
                              $model->setaddress(trim(str_replace('"',"",$point_relais->LgAdr2)));
                              $model->setaddress2(trim(str_replace('"',"",$point_relais->LgAdr3)));
                              $model->setaddress3(trim(str_replace('"',"",$point_relais->LgAdr4)));
                              $model->setcity(trim($point_relais->Ville));
                              $model->setpostalcode($point_relais->CP);
                              $model->setidentifiant($point_relais->Num);
                              $model->setdimanche($Dimanche);
                              $model->setjeudi($Jeudi);
                              $model->setlundi($Lundi);
                              $model->setmardi($Mardi);
                              $model->setmercredi($Mercredi);
                              $model->setsamedi($Samedi);
                              $model->setvendredi($Vendredi);
                              $model->setcustomerId($customer_id);
                              $model->save();

                    }
                }



    }

    public function getWebserviceRdvData($params)
    {

        $client = new SoapClient(Mage::getStoreConfig('tatva/tatva_settings/webservice'));
        $res = '';
        //$res   = $client->findRDVPointRetraitAcheminement($params);

            foreach($res as $point_relais ) {
                    return $point_relais->rdv;
            }
    }



}