<?php

// Chargement des 4 stores
$storeFR1 = Mage::getModel('core/store')->load('az_fr_part');
$storeFR2 = Mage::getModel('core/store')->load('az_fr_pro');
$storeEN1 = Mage::getModel('core/store')->load('az_en_part');
$storeEN2 = Mage::getModel('core/store')->load('az_en_pro');

$now = date('Y-m-d H:i:s');

// Création des templates de mails

$this->getConnection()->delete($this->getTable('core_email_template'), "template_code='2xmoinscher - Annulation commande'");

$_content = <<<TXT
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
											<a href="{{store url=''}}" style="text-decoration:none;color:#C40C48"><strong>www.az-boutique.fr</strong></a><br /><br />
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
									<a href="{{store url=''}}" style="text-decoration:none;color:#C40C48">www.az-boutique.fr</a>
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
TXT;

$email = Mage::getModel('core/email_template')
	->setTemplateCode('2xmoinscher - Annulation commande')
	->setTemplateText($_content)
	->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
	->setTemplateSubject('AZ Boutique - Annulation Commande  N°{{var partner_order}}')
	->setAddedAt($now)
	->setModifiedAt($now)
	->save();

$configEmail = Mage::getModel('core/config_data')
	->setScope('default')
	->setScopeId(0)
	->load('tatvamarketplaces_2xmoinscher/orders/email_send_template_order_canceled','path');
if($configEmail->getId()){
	$configEmail->setValue($email->getId());
	$configEmail->save();
}else{
	Mage::getModel('core/config_data')
		->setScope('default')
		->setScopeId(0)
		->setPath('tatvamarketplaces_2xmoinscher/orders/email_send_template_order_canceled')
		->setValue($email->getId())
		->save();
}
