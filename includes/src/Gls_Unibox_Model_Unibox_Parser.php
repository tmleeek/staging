<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Model_Unibox_Parser {

    protected $_paketNumber = false;

	public function create($service,$shipment,$weight,$shipfrom,$notiz,$extra) {
		$sendString = $this->createGlsSubmitTag($service,$shipment,$weight,$shipfrom,$extra);
		$returnedTag = $this->sendViaSocket($sendString);
        if($returnedTag === false){
			return false; 
		} else 	{ 
			$tags = $this->parseIncomingTag($returnedTag);
			if(is_Array($tags)){
				if ($service == "business") {
					$glsService = Mage::getModel('glsbox/label_gls_business');
				}
				elseif ($service == "express") { 
					$glsService = Mage::getModel('glsbox/label_gls_express');
				}
		
				if($glsService != null) {
					//check if wrong $service is submitted
					/*	No Error => Save Info in Database.	*/
					$glsCustomerId = $shipfrom->getCustomerid();
					$glsContactId = $shipfrom->getContactid();
					$glsDepotCode = $shipfrom->getDepotcode();
					$glsKundennummer = $shipfrom->getKundennummer();
					$glsDepotnummer = $shipfrom->getDepotnummer();
						
					$glsSave = Mage::getModel('glsbox/shipment');
					$glsSave->setService($service)
							->setShipmentId($shipment->getId())
							->setGlsMessage($returnedTag)
							->setWeight($weight)
							->setKundennummer($glsKundennummer)
							->setCustomerid($glsCustomerId)
							->setContactid($glsContactId)
							->setDepotcode($glsDepotCode)
							->setDepotnummer($glsDepotnummer)
							->setNotes($notiz)
							->save();
					$glsService->importValues($tags);
					return $glsService->getData();			
				} else {
					return 'Bitte wählen Sie einen gültigen Versandservice von Gls';
				}
			} else { 
				return $tags;
				// Return, because it includes Errorcode TODO: Errorhandling and Message for different errortypes.
			}
		}
	}

	public function preparePrint($id) {
		$returnedtag = Mage::getModel('glsbox/shipment')->getCollection()->addFieldToFilter('id', $id)->getFirstItem()->getGlsMessage();
		if($returnedtag === false || $returnedtag == "") { 
			return false; 
		} else 	{ 
			$tags = $this->parseIncomingTag($returnedtag);
			if(is_Array($tags)) {
				$service = Mage::getModel('glsbox/shipment')->getCollection()->addFieldToFilter('id', $id)->getFirstItem()->getService();
				if ($service == "business") {
					$glsService = Mage::getModel('glsbox/label_gls_business'); 
				}
				elseif ($service == "express") {
					$glsService = Mage::getModel('glsbox/label_gls_express'); 
				}
				if($glsService != null) { 
					$glsService->importValues($tags);
					return $glsService->getData();				
				} else { 
					return false; 
				}
			} else { 
				return false;
			}		
		}
	}	
	
	public function prepareDelete($id) {
		$item = Mage::getModel('glsbox/shipment')->getCollection()->addFieldToFilter('id', $id)->getFirstItem();
		if($item === false) { 
			return "Konnte Gls Shipment Kollektion nicht holen."; 
		} else 	{ 
			$paketnummer = Mage::helper('glsbox')->getTagValue($item->getGlsMessage(),'400');
			$sendString ='\\\\\\\\\\GLS\\\\\\\\\\T000:'.$paketnummer.'|/////GLS/////';
			$returnedtag = $this->sendViaSocket($sendString);
			if($returnedtag === false) { 
					return "Socketkommunikation fehlgeschlagen"; 
			} else 	{ 
				$tags = $this->parseIncomingTag($returnedtag);
				if(is_Array($tags)){
					$item->setStorniert(1)->save();
					return true;
				} else {
					return $tags;
				}		
			}			
		}
	}	
	
	private function parseIncomingTag($returnedtag) {
		//$returnedtag = '\\\\\\\\\\GLS\\\\\\\\\\T010:|T050:Versandsoftwarename|T051:V 1.5.2|T8700:DE 550|T330:20354|T090:NOPRINT|T400:552502000716|T545:26.01.2009|T8904:001|T8905:001|T800:Absender|T805:12|T810:GLS IT Services GmbH|T811:Project Management|T820:GLS Germany Str. 1-7|T821:DE|T822:36286|T823:Neuenstein / Aua|T851:KD Nr.:|T852:10166|T853:ID No.:|T854:800018406|T859:Company|T860:GLS Germany GmbH & Co.OHG|T861:Depot 20|T863:Pinkertsweg 49|T864:Hamburg|T921:Machine Parts|T8914:27600ABCDE |T8915:2760000000|T080:4.67|T520:21012009|T510:ba|T500:DE 550|T560:DE03|T8797:IBOXCUS|T540:26.01.2009|T541:11:20|T100:DE|CTRNUM:276|CTRA2:DE|T202:|T210:|ARTNO:Standard|T530:16.20|ALTZIP:20354|FLOCCODE:DE 201|OWNER:5|TOURNO:1211|T320:1211|TOURTYPE:21102|SORT1:0|T310:0|T331:20354|T890:2001|ROUTENO:1006634|ROUTE1:R33|T110:R33|FLOCNO:629|T101: 201|T105:DE|T300:27620105|NDI:|T8970:A|T8971:A|T8975:12|T207:|T206:10001|T8980:AA|T8974:|T8916:552502000716|T8950:Tour|T8951:ZipCode|T8952:Your GLS Track ID|T8953:Product|T8954:Service Code|T8955:Delivery Address|T8956:Contact|T8958:Contact|T8957:Customer ID|T8959:Phone|T8960:Note|T8961:Parcel|T8962:Weight|T8963:Notification on damage which is not recognisable from outside had to be submitted to GLS|T8964:on the same Day of Delivery in writing. This Transport is based on GLS terms and conditions|T8913:ZFFX4HDZ|T8972:ZFFX4HDZ|T8902:ADE 550DE 20100000000002760000000ZFFX4HDZAA 0R33121120354 01620000000000000012 |T8903:A¬GLS Germany GmbH & Co.OHG¬Pinkertsweg 49¬Hamburg¬¬¬800018406¬|PRINTINFO:|PRINT1:|RESULT:E000:552502000716|T565:112059|PRINT0:xxGLSintermecpf4i.int01|/////GLS/////';
		//$returnedtag = iconv("ISO-8859-1" ,"UTF-8//TRANSLIT", $returnedtag);
		
		if( stripos($returnedtag ,'\\\\\\\\\\GLS\\\\\\\\\\' ) !== false && stripos($returnedtag ,'/////GLS/////' ) !== false ){
			$returnedtag = str_ireplace ( array('\\\\\\\\\\GLS\\\\\\\\\\','/////GLS/////') ,'', $returnedtag); 
		} else {
			return 'Fehler: Kein gültiger GLS Stream';
		}

		//Sonderzeichen der Datamatrix2 umwandeln in + für die Speicherung in der Datenbank
		$returnedtag = str_replace("¬", "+",$returnedtag);
		$returnedtag = explode('|',$returnedtag);
		$glsTags = array();
		foreach ($returnedtag as $item) {
			if (stripos($item,'T') === 0) {
				$tmp = explode(':',$item,2); $tmp[0] = str_ireplace('T','',$tmp[0]);
				if($tmp[1] != ''){
					$glsTags[$tmp[0]] = $tmp[1] ; 
				}
			}elseif (stripos($item,'RESULT') === 0 && stripos($item,'E000') === false ) {
                /* TODO:
                    return = Message string(25) "RESULT:E002:T8700:1234567" {"error":true,"message":"Unibox-Fehler - Ein falsches Format wurde \u00fcbergeben"}
                */
                if(stripos($item,'E001')){
                    return Mage::helper('core')->__('Unibox-Fehler - Ein Pflichtfeld fehlt');
                }
                elseif(stripos($item,'E002')){
                    return Mage::helper('core')->__('Unibox-Fehler - Ein falsches Format wurde übergeben');
                }
                elseif(stripos($item,'E003')){
                    return Mage::helper('core')->__('Unibox-Fehler - Die Postleitzahl ist nicht korrekt');
                }
                elseif(stripos($item,'E004')){
                    return Mage::helper('core')->__('Unibox-Fehler - Der Nummernkreis ist aufgebraucht');
                }
                elseif(stripos($item,'E005')){
                    return Mage::helper('core')->__('Unibox-Fehler - Es wurde ein Parameter zu wenig übergeben');
                }
                elseif(stripos($item,'E006')){
                    return Mage::helper('core')->__('Unibox-Fehler - Das Routing konnte nicht erstellt werden');
                }
                elseif(stripos($item,'E007')){
                    return Mage::helper('core')->__('Unibox-Fehler - Das Template konnte nicht gefunden werden');
                }
                elseif(stripos($item,'E008')){
                    return Mage::helper('core')->__('Unibox-Fehler - Die Schnittstelle konnte nicht ewrreicht werden');
                }
                elseif(stripos($item,'E009')){
                    return Mage::helper('core')->__('Unibox-Fehler - Das Gewicht ist außerhalb der Toleranz');
                }
                elseif(stripos($item,'E010')){
                    return Mage::helper('core')->__('Unibox-Fehler - Das Gewicht überschreitet 2 kg');
                }
                elseif(stripos($item,'E011')){
                    return Mage::helper('core')->__('Unibox-Fehler - Die Transaktionsdaten konnten nichgt gespeichert werden');
                }
                elseif(stripos($item,'E012')){
                    return Mage::helper('core')->__('Unibox-Fehler - Die Prüfziffer ist falsch');
                }
                elseif(stripos($item,'E013')){
                    return Mage::helper('core')->__('Unibox-Fehler - Das Produktkennzeichen ist im Empfangsland nicht erlaubt');
                }
                elseif(stripos($item,'E014')){
                    return Mage::helper('core')->__('Unibox-Fehler - Das Versanddatum ist nicht das heutige Datum');
                }
                elseif(stripos($item,'E015')){
                    return Mage::helper('core')->__('Unibox-Fehler - Express kann nach 15:25 nicht mehr für den heutigen Tag erstellt werden');
                }
                elseif(stripos($item,'E016')){
                    return Mage::helper('core')->__('Unibox-Fehler - Eine 8 Uhr-Zustellung ist bei dieser PLZ nicht möglich');
                }
                elseif(stripos($item,'E017')){
                    return Mage::helper('core')->__('Unibox-Fehler - S1 oder SE ist nur an dem jeweils letzten Werktag vor einem Samstag möglich');
                }
                elseif(stripos($item,'E018')){
                    return Mage::helper('core')->__('Unibox-Fehler - Diese Paketnummer wurde schon verwendet');
                }
                elseif(stripos($item,'E019')){
                    return Mage::helper('core')->__('Unibox-Fehler - Falsche Paketnummer für dieses Produkt oder die Depotkennung ist falsch');
                }
			}
			$tmp = null;
		}
		return $glsTags;
	}
	
	private function sendViaSocket($broadcast_string){
		//$broadcast_string = '\\\\\\\\\\GLS\\\\\\\\\\T090:NOPRINT,NOSAVE|T050:Versandsoftwarename|T051:V 1.5.2|T100:DE|T8700: DE 550|T330:20354|T400:552502000723|T530:16,2|T545:26.01.2009|T8904:001|T8905:001|T800:Absender|T805:12|T810:GLS IT Services GmbH|T811:Project Management|T820:GLS-Germany-Str. 1-7|T821:DE|T822:36286|T823:Neuenstein / Aua|T851:KD-Nr.:|T852:10166|T853:ID-No.:|T854:800018406|T859:Company|T860:GLS Germany GmbH & Co.OHG|T861:Depot 20|T863:Pinkertsweg 49|T864:Hamburg|T921:Machine Parts|T8914:27600ABCDE| T8915:2760000000|/////GLS/////';
		/* Port for socket. */
		$service_port = (int)Mage::getStoreConfig('glsbox/account/glsport'); //Test : 3030;
		/* IP for Socket. */
		$address = Mage::getStoreConfig('glsbox/account/glsip'); //Test : "217.7.25.136";
		/* Create TCP/IP Socket. */
		
		
		
		if(!filter_var($address, FILTER_VALIDATE_IP) || $service_port <= 0) { 
			return false;
		}
		
		if( ($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0 ){
			#error hier...
			#die("socket_create() failed, reason: ".socket_strerror($this->master));
		}
		#dann kann das hier weg
		if ($socket === false) {
			return false; //Fehler : socket_strerror(socket_last_error());
		}

		socket_set_option( $socket, SOL_SOCKET, SO_BROADCAST, 1);
		socket_set_option( $socket, SOL_SOCKET, SO_SNDTIMEO, array( "sec"=>1, "usec"=>0 ));
		socket_set_option( $socket, SOL_SOCKET, SO_RCVTIMEO, array( "sec"=>6, "usec"=>0 ));

		$result = socket_connect($socket, $address, $service_port);
		if ($result === false) {	
			return false; //Fehler : socket_strerror(socket_last_error($socket));
		}

		$in = $broadcast_string;
		$out = '';

		$written = socket_send($socket, $in, strlen($in),MSG_DONTROUTE);
		//socket_shutdown($socket, 1);

		$buf = '';

		while ($out = socket_read($socket, 2048*4)) {
			$buf .= $out;
		}
		socket_close($socket);
		return $buf;
	}
	
	private function createGlsSubmitTag($service,$shipment,$weight,$shipfrom,$extra){
	
	   try{
            $createdPaketNumber = $this->generateNewGlsLabelNumber($service,$shipfrom);
        }catch (Exception $e){
            Mage::throwException($this->__('Paketnummer konnte nicht erzeugt werden: ').$e->getMessage());
        }
	   //generate order no 	
		$numeric_val = $shipfrom->getNumcircleStandardStart()+1;
		$shipfrom->setNumcircleStandardStart($numeric_val);
		
		$shipfrom->save();

		$shipmentIncId = $shipment->getIncrementId();
		
		$glsCustomerId = $shipfrom->getCustomerid();
		$glsContactId = $shipfrom->getContactid();
		$glsDepotCode = $shipfrom->getDepotcode();

		$versenderName1 = Mage::getStoreConfig('glsbox/versender/name1');
		$versenderName2 = Mage::getStoreConfig('glsbox/versender/name2');
		$versenderStrasse = Mage::getStoreConfig('glsbox/versender/strasse');
		$versenderLandeskennzeichen = Mage::getStoreConfig('glsbox/versender/landkennzeichen');
		$versenderPlz = Mage::getStoreConfig('glsbox/versender/plz');
		$versenderOrt = Mage::getStoreConfig('glsbox/versender/ort');
		
		//var_dump($shipment->getOrder()->getShippingAddress());die();
		$customerName = $this->Replace_data($shipment->getOrder()->getShippingAddress()->getFirstname().' '.$shipment->getOrder()->getShippingAddress()->getLastname());
		
		$customerStrasse = $this->Replace_data($shipment->getOrder()->getShippingAddress()->getStreetFull());
		$customerOrt = $this->Replace_data($shipment->getOrder()->getShippingAddress()->getCity());
		$customerPlz = $shipment->getOrder()->getShippingAddress()->getPostcode();
		$customerCountryId = $shipment->getOrder()->getShippingAddress()->getCountryId();
        $customerCompany = $this->Replace_data($shipment->getOrder()->getShippingAddress()->getCompany());
        $customerNumber = $shipment->getCustomerId();
		$country = $shipment->getOrder()->getShippingAddress()->getCountry();
		$telephone = $shipment->getOrder()->getShippingAddress()->getTelephone();
		$paketNotiz = $extra['notiz'];
		$prd_code = '01';
	    if($country == 'FR')
	    {
	     $prd_code = '02';
	    }

		$starttag = '\\\\\\\\\\GLS\\\\\\\\\\';
		
		$tags=array(																			//Parameter f. Sonderfunktionen
			'T540' => date('Ymd'),																					//Parameter f. Sonderfunktionen
            'T859' => $OrderId,
			//'T860' => $customerName,
			'T8914' => $glsContactId,
			'T8975'	=> $prd_code.$numeric_val.'0000'.$country.'    ',	
			'T530' => $weight,
			'T8973'	=> 1,
			'T8904' => 1,
			'T8702' => 1,
			'T8905' => 1,
			'T863' => $customerStrasse,
			'T100' => $country,
			'T330' => $customerPlz, 
			'T864' => $customerOrt,
			'T090' => 'NOSAVE',
			'T8700' => 'FR0098',
			'T8915' => $glsCustomerId,
			'T810' => $versenderName1,
			'T821' => 'FR',
			'T823' => $versenderOrt,
			'T820' => $versenderStrasse,
			'T821' => $versenderLandeskennzeichen,																	
			'T822' => $versenderPlz,
			'T823' => $versenderOrt,
			'T871' => $telephone,
			//'T854' => 'Notetest',
		);
		
		if($customerCompany){
            
            $tags['T860'] = $customerCompany;
			$tags['T861'] = $customerName;
            
        }else{
            
            $tags['T860'] = $customerName;
        }
		if($country == 'FR')
		   {
		     $tags['T082'] = 'UNIQUENO';
		   }
		$endtag = '/////GLS/////';

		$finalstring = '';
		foreach($tags as $t => $value){
			$finalstring .= $t.':'.$value.'|';
		}

		$finalstring = $starttag.$finalstring.$endtag;
		return $finalstring;
	}

    private function Replace_data($prd_name)
		{
		    $vowels = array("â", "Ä", "à", "á", "â", "ã", "ä", "ã¨" );
		    $prd_name = str_replace($vowels, "a", $prd_name);

		    $vowels = array("ê","é","è" ,"ê" ,"ë");
		    $prd_name = str_replace($vowels, "e", $prd_name);

		    $vowels = array("ò", "ó", "ô", "õ" ,"ö", "Ò" ,"Ó","Ô","Õ","Ö");
		    $prd_name = str_replace($vowels, "o", $prd_name);

		    $vowels = array("ù","ú","û","ü","Ù","Ú","Û","Ü" );
		    $prd_name = str_replace($vowels, "u", $prd_name);

		    $vowels = array("Ç","ç");
		    $prd_name = str_replace($vowels, "c", $prd_name);

		    $vowels = array("ñ");
		    $prd_name = str_replace($vowels, "n", $prd_name);

		    $vowels = array("Ì","","Î","ì","í","î","ï","");
		    $prd_name = str_replace($vowels, "i", $prd_name);

		    $vowels = array("ý","ÿ");
		    $prd_name = str_replace($vowels, "y", $prd_name);

		    $vowels = array("œ" );
		    $prd_name = str_replace($vowels, "oe", $prd_name);

		    $vowels = array("Ø" );
		    $prd_name = str_replace($vowels, "", $prd_name);

		    return $prd_name;
		}
		
	private function generateNewGlsLabelNumber($service, $client)
	{
		//(string)$versandDepotnummer = $client->getDepotnummer();
		
		//$laufendePaketnummer = $this->nextAvailableLabelDevNumber();
		$laufendePaketnummer = $this->nextAvailableLabelNumber($service,$client);
		$laufendePaketnummer = $this->addCheckDigit($laufendePaketnummer);

		return $laufendePaketnummer;
	}

	private function nextAvailableLabelNumber($service,$client)
	{
		//Hole für customerId und service art letzte Nummer
		$_packetNumberStart = ($service == "express") ? (float)$client->getNumcircleExpressStart() : (float)$client->getNumcircleStandardStart();
		$_packetNumberEnd = ($service == "express") ? (float)$client->getNumcircleExpressEnd() : (float)$client->getNumcircleStandardEnd();
		$_packetNumber = $_packetNumberStart+1;

		//Prüfe, ob Nummer+1 < numcircle service end
		//Wenn JA
		if($_packetNumber <= $_packetNumberEnd){
			//speicher Nummer+1 für customerId und service
			if($service == "express"){
				$client->setNumcircleExpressStart($_packetNumber);
			}
			if($service == "business"){
				$client->setNumcircleStandardStart($_packetNumber);
			}
			$client->save();
			//Wenn Nummer+1 == numcircle_service_end
			if($_packetNumber == $_packetNumberEnd){
				//Meldung ERFOLG, neuen Nummernkreis anfordern service client
                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('glsbox')->__('The shipment was processed, but your numcicle is expired by now!'));
			}
			//return Nummer+1
            $this->_paketNumber = $_packetNumber;
			return $_packetNumber;
		//Wenn NEIN ->
		//Wenn Nummer+1 > numcircle_service_end
		}elseif($_packetNumber > $_packetNumberEnd){
			//Meldung Fehler, neuen Nummernkreis für service client
            Mage::getSingleton('customer/session')->addError(Mage::helper('glsbox')->__('Your numcicle is expired!'));
		}
	}
	
	private function addCheckDigit($number)
	{
		$_digitArray = str_split($number);
		$_digitArray = array_reverse($_digitArray);
		$sum = 0;
		foreach($_digitArray as $key => $value){
			if($key%2 == 0){
				$sum += 3*$value;
			}else{
				$sum += $value;
			}
		}
		$diff = (int)(ceil($sum/10)*10)-($sum+1);
		if($diff == 10){
			return 0;
		}
		return $number.$diff;
	}


	/*
	private function nextAvailableLabelDevNumber()
	{
		(int)$current = Mage::getModel('glsbox/shipment')->getCollection()->getLastItem()->getPaketnummer(); //Wenn keine Einträge existieren, so wird aus false 0 werden
		$current++;
		$next = str_pad($current, 7 ,'0', STR_PAD_LEFT);
		return $next;
	}
	*/
}