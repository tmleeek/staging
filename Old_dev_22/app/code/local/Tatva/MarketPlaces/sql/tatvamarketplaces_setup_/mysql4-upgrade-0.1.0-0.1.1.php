<?php
// -- Mapping des modes de transport de 2xmoinscher
//REG MARK-32404
$shipping = array(
	array('shipping_code' => 'Normal' , 'shipping_mapping' => 'colissimo'),
	array('shipping_code' => 'Distinguo/Colis Suivi' , 'shipping_mapping' => 'colissimo'),
	array('shipping_code' => 'Recommandé R1' , 'shipping_mapping' => 'colissimo'),
	array('shipping_code' => 'Recommandé R2' , 'shipping_mapping' => 'colissimo'),
	array('shipping_code' => 'Recommandé R3' , 'shipping_mapping' => 'colissimo'),
);

$serializeShipping = serialize($shipping);

Mage::getModel('core/config_data')
	->setScope('default')
	->setScopeId(0)
	->setPath('tatvamarketplaces_2xmoinscher/shipping_methods/mapping')
	->setValue($serializeShipping)
	->save();

// -- Valeurs par défaut des ventes multi-canal
Mage::getModel('core/config_data')
	->setScope('default')
	->setScopeId(0)
	->setPath('tatvamarketplaces_orders/configuration/sender_errors')
	->setValue('sales')
	->save();

Mage::getModel('core/config_data')
	->setScope('default')
	->setScopeId(0)
	->setPath('tatvamarketplaces_orders/configuration/receiver_errors')
	->setValue('sales')
	->save();

$now = date('Y-m-d H:i:s');


$txt = <<<TXT
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<title>Commande &ndash; AZ Boutique</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<style type="text/css">
			a:hover {text-decoration:underline !important}
		</style>
		<table width="100%" height="100%">

			<tr>
				<td bgcolor="#DCD0D2">
					<center>
						<table width="608" cellspacing="0" cellpadding="0">
							<tr height="30">
								<td></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr height="12">
								<td></td>
							</tr>
							<tr>
								<td bgcolor="white">
									<table width="608" cellspacing="0" cellpadding="0" style="background:url({{skin url="img/milieu_tableau_newsletter.png" _area='adminhtml'}}) repeat-y 50% 0">
										<tr>
											<td colspan="3" height="3"><img src="{{skin url="img/haut_tableau_newsletter.png" _area='adminhtml'}}" alt="" /></td>

										</tr>
										<tr>
											<td rowspan="6" width="7"><div style="width:7px">&nbsp;</div></td>
											<td height="1"></td>
											<td rowspan="6" width="7"><div style="width:7px">&nbsp;</div></td>
										</tr>							
										<tr>
											<td><img src="{{skin url="img/logo_image_az_newsletter.png" _area='adminhtml'}}" alt="" /></td>
										</tr>

										<tr>
											<td><img src="{{skin url="img/bandeau_commande_fournisseur_mail.png" _area='adminhtml'}}" alt="" /></td>
										</tr>
										<tr>
											<td style="text-align:justify">
												<font style="font-size:13px;" face="verdana,Helvetica,sans-serif">
													<br />
													Cher partenaire,<br /><br />

													Nous vous prions de bien vouloir prendre en compte l'annulation de la commande suivante : 
                                                                                                         <ul style="margin-top: 0px;">
														<li><strong>Commande N° : </strong>{{var partner_order}} </li>
<li><strong>Client : </strong>{{var partner_customer_name}} </li>
<li><strong>Date: </strong>{{var partner_order_date}} </li>

<br /><br />
													Sinc&egrave;res salutations,<br /><br />
													<strong>La logistique</strong><br />
													<a href="http://www.az-boutique.fr" style="text-decoration:none;color:#C40C48"><strong>www.az-boutique.fr</strong></a><br /><br />
												</font>
											</td>
										</tr>

										<tr>
											<td><img src="{{skin url="img/bandeau_bas_modification_compte_newsletter.png" _area='adminhtml'}}" alt="" /></td>
										</tr>
										<tr>
											<td height="3"></td>
										</tr>
										<tr>
											<td colspan="3"><img src="{{skin url="img/bas_tableau_newsletter.png" _area='adminhtml'}}" alt="" /></td>
										</tr>

									</table>
								</td>
							</tr>
						</table>
						<table width="628" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<center>
										<font style="font-size:9px" face="Verdana,Helvetica,sans-serif">

											<br />
											AZ BOUTIQUE FRANCE - Le Quartz, 170 rte de la Font de Cine - 06225 Vallauris Cedex<br />
											<a href="http://www.az-boutique.fr" style="text-decoration:none;color:#C40C48">www.az-boutique.fr</a>
										</font>
									</center>
								</td>
							</tr>

							<tr height="30">
								<td></td>
							</tr>
						</table>
					</center>
				</td>
			</tr>
		</table>
	</body>

</html>
TXT;
//EXIG REF-005
//REG MARK-32403
$email = Mage::getModel('core/email_template')
	->setTemplateCode('2xmoinscher - Annulation commande')
	->setTemplateText($txt)
	->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
	->setTemplateSubject("AZ Boutique - Annulation Commande  N°{{var partner_order}}")
	->setAddedAt($now)
	->setModifiedAt($now)
	->save();		
	
Mage::getModel('core/config_data')
	->setScope('default')
	->setScopeId(0)
	->setPath('tatvamarketplaces_2xmoinscher/orders/email_send_template_order_canceled')
	->setValue($email->getId())
	->save();