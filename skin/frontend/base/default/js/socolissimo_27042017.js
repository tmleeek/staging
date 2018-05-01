/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

/**
 * Fonction startWith sur l'objet String de javascript
 */
String.prototype.startWith = function(t, i) { if (i==false) { return
	(t == this.substring(0, t.length)); } else { return (t.toLowerCase()
														 == this.substring(0, t.length).toLowerCase()); } }

/**
 * Variables globales
 */

var socolissimoMyPosition;
var socolissimoListRelais=new Array();
var relaisSocolissimoInfowindow=new Array();
var relaisSocolissimoMarker=new Array();
var socolissimoMap;
var socolissimoOpenedInfowindow;
var socolissimoRelaisChoisi;

/**
 * Initialisation au chargement de la page
 */
jQuery(function($) {

	//Cas du onestep checkout, si on change l'adresse de livraison après avoir choisi socolissimo
	jQuery(document).on("change",'.onestepcheckout-index-index .address-select', function() {
		if(jQuery('#socolissimo-location').size() <= 0 ){
			$("#attentionSoColissimo").remove();
			$("label[for=\"billing-address-select\"]").parent().before('<p id="attentionSoColissimo" style="font-weight:bold;color:red;text-align:justify; padding-right:5px;">Suite à la modification de votre adresse et si votre mode de livraison est Colissimo, veuillez séléctionner votre point de retrait en cliquant sur le mode de livraison.</p>');
		}
	});

	/**
	 * Sur l'événement change des radios boutons de choix de mode de livraison
	 */
	$(document).on( 'click',"input[id^=s_method_socolissimo_]",function() {
		shippingRadioCheck(this);
	});

	initSocolissimoLogos();

});

/**
 * Initialise les logos, descriptions, style sur le radio bouttons socolissimo
 * ceci est fait en javascript pour ne pas surcharger le template available.phtml et ainsi éviter des conflits avec d'autres modules.
 * (appelé au chargement du DOM mais aussi au rechargement ajax (voir Checkout.prototype.setStepResponse dans  socolissimo\additional.phtml)
 */
function initSocolissimoLogos() {



	jQuery('input[id^=s_method_socolissimo_]').each(function(index, element){

		if(!jQuery("body").hasClass("onestepcheckout-index-index")) {
			jQuery(element).parents("dd").addClass("s_method_socolissimo");
		} else {
			jQuery("input[id^=\"s_method_socolissimo\"]").parents("dt").addClass("s_method_socolissimo");
			var dd = jQuery("input[id^=\"s_method_socolissimo\"]").eq(0).parents("dt").prev().addClass("s_method_socolissimo-title");
		}

		jQuery(element).prop("checked", "");
		var colissimoMethodName = jQuery(element).attr("value");
		var typeSocolissimoArray = colissimoMethodName.split("_");
		var typeSocolissimo = typeSocolissimoArray[1];
		var typeDescr = typeSocolissimoArray[2];
		var ship_country_code = (typeDescr=='fr') ? 'fr' : 'be';

		if(typeSocolissimo){
			var radioParent = jQuery(element).parent();
			var ulElt = radioParent.parent();
			//descripiton pour les livraisons à domicile
			if(typeSocolissimo == 'domicile' && !ulElt.find("#socolissimo_description_domicile").length){
				jQuery("#socolissimo_description_domicile").clone().insertBefore(radioParent).attr("style", "display:block;");
			}
			//descripiton pour les livraisons en poste ou commercant (texte en fct du pays FR ou BE)
			if((typeSocolissimo == 'poste' || typeSocolissimo == 'commercant') && !ulElt.find("#socolissimo_description_poste_" + ship_country_code).length){
				jQuery("#socolissimo_description_poste_" + ship_country_code).clone().insertBefore(radioParent).attr("style", "display:block;");
			}
		}
	});

	var logoColissimo = document.createElement('img');
	logoColissimo.setAttribute('src', '/skin/frontend/base/default/images/socolissimo/colissimo.png');

	if(!jQuery("body").hasClass("onestepcheckout-index-index")) {
		if (jQuery(".s_method_socolissimo").prev().children('img').size() == 0) {
			jQuery(".s_method_socolissimo").prev().addClass("s_method_socolissimo-title").append(logoColissimo);
		}
	} else {
		if (jQuery(".s_method_socolissimo-title").children('img').size() == 0) {
			jQuery(".s_method_socolissimo-title").append(logoColissimo);
		}
	}

	logoColissimo.style.width="104px";
	logoColissimo.style.height="auto";
}

function getTypeSocolissimoFromRadio(radio) {
	var shippingMethod = radio.attr("value");
	var typeSocolissimo = shippingMethod.replace("socolissimo_","");
	if (typeSocolissimo.startWith("poste")) {
		return 'poste';
	} else if (typeSocolissimo.startWith("commercant")){
		return 'commercant';
	} else if (typeSocolissimo.startWith("domicile")) {
		return 'domicile';
	} else {
		//Sinon c'est un type de livraison inconnu
		alert("Mauvaise configuration du module Socolissimo : dans le champ configuration le code doit commencer par domicile, domicile_sign, poste ou commercant ");
		return false;
	}
}

function shippingRadioCheck(element) {
	var socoRadio = jQuery(element);

	//on affiche le picto de chargement étape suivante du opc
	jQuery("#shipping-method-please-wait").show();

	//on charge en ajax le layer socolissimo (carte choix relais et/ou saisie numéro de téléphone)
	socoUrl = socolissimoBaseUrl + "selector/type/";
	var typeSocolissimo =  getTypeSocolissimoFromRadio(socoRadio);
	if (typeSocolissimo) {
		socoUrl = socoUrl + typeSocolissimo;
	} else {
		return;
	}

	jQuery.ajax({
					url: socoUrl,
					success: function(data){

						//une fois chargé, on cache le picto de chargement, on ouvre un layer et on met de résultat dedans:
						jQuery("#shipping-method-please-wait").hide();

						if (jQuery("#socolissimo-hook").size()==0) {
							socoRadio.parent().parent().append("<div id=\"socolissimo-hook\" ></div>");
						}
						jQuery("#layer_socolissimo #layer_socolissimo_wrapper").html(data);
						//on supprime les filtres si le mode de livraison correspondant n'est pas proposé
						if (jQuery("input[id^=\"s_method_socolissimo_poste\"]").length == 0) {
							jQuery("#filtre_poste").remove();
						}
						if (jQuery("input[id^=\"s_method_socolissimo_commercant\"]").length == 0) {
							jQuery("#filtre_commercant").remove();
						}
						//on affiche le layer
						jQuery("#layer_socolissimo").popup({
															   beforeopen: function(){
																   if( socoIsMobile ) {
																	   var height = jQuery( window ).height();
																	   jQuery('#layer_socolissimo').css({top: jQuery( window ).scrollTop()+'px',height:jQuery( window ).height()+'px'});
																	   mapHeight = jQuery( window ).height() - jQuery('#layer_socolissimo .soco_map-header').height();
																	   jQuery('#map_canvas,.soco_adresses').css({height: mapHeight+'px'});
																   }
															   },
																onopen: function() {
																	_loadPostCode();
																},
															   onclose: function(){
																   //si on n'a pas choisi de type de livraison socolissimo, on décoche le mode de livraison socolissimo
																   var telephoneElt = jQuery("#socolissimo-hook input[name='tel_socolissimo']");
																   if (!telephoneElt || telephoneElt.val() == undefined) {
																	   resetShippingMethod();
																   } else {
																	   var shippingMethod = jQuery("input[name='shipping_method']:checked").val();
																	   if (shippingMethod.startWith("socolissimo_poste") || shippingMethod.startWith("socolissimo_commercant")) {
																		   var relaisElt = jQuery("#socolissimo-hook input[name='relais_socolissimo']");
																		   if (!relaisElt || relaisElt.val() == undefined) {
																			   resetShippingMethod();
																		   }
																	   }
																   }
															   },
															   transition: 'all 0.3s',
														   });

						jQuery("#layer_socolissimo").popup('show');

						if (typeSocolissimo.startWith("poste") || typeSocolissimo.startWith("commercant")){

							//initialisation des champs "adresse" du layer qui peuvent être vides dans le cas du onestepcheckout avec un nouveau client sans adresse enregistrée
							if (jQuery("#socolissimo_postcode").val() == '') {
								jQuery("#socolissimo_street").val(jQuery("input[name='billing[street][]']").val());
								jQuery("#socolissimo_postcode").val(jQuery("input[name='billing[postcode]']").val());
								jQuery("#socolissimo_city").text(jQuery("input[name='billing[city]']").val());
								jQuery("#socolissimo_country").val(jQuery("select[name='billing[country_id]']").find("option:selected").val());
							}
							jQuery("#socolissimo_city_select").val(jQuery("#socolissimo_city").text());

							//initialisation de la liste déroulantes des villes "personnalisée"
							jQuery("#socolissimo_city_select").change(function() {
								jQuery(this).prevAll("span").eq(0).text(jQuery(this).find("option:selected").text());
							});

							//initilisation du rechargement de la liste déroulante des villes
							jQuery("#socolissimo_postcode").change(_loadPostCode);

							//on localise l'adresse qui est préchargée (adresse de livraison par défaut du compte client)
							if (jQuery("#socolissimo_postcode").val() != "") {
								geocodeAdresse();
							}
						}


					},
					error : function(jqXHR, textStatus){
						alert("Erreur de chargement des données "+textStatus);
					}
				});



}

function _loadPostCode(){
	const postcode = jQuery('#socolissimo_postcode').val();
	if(postcode == undefined) return false;
	const country = jQuery('#socolissimo_country').val();
	if(country == undefined) return false;

	const socoUrlCities = "https://api.zippopotam.us/"+country+"/"+postcode;
	jQuery.ajax({
		url: socoUrlCities,
		dataType:'json',
		success: function(json){
			var options = '<option selected >Choisissez une commune</option>';
			for (i=0; i<json.places.length; i++){
				commune = json.places[i]['place name'];
				options += '<option value="' + commune + '">' + commune + '</option>';
			}
			jQuery("#socolissimo_city_select").html(options);
			jQuery("#socolissimo_city").text("Choisissez une commune");
		}
	});
};

function resetShippingMethod() {
	jQuery("input[name='shipping_method']:checked").prop("checked","");
}

function geocodeAdresse() {

	if (jQuery("#socolissimo_city_select option").length > 0 && jQuery("#socolissimo_city_select")[0].selectedIndex == 0) {
		alert("Veuillez sélectionner une commune");
		return;
	}
	if (jQuery("#socolissimo_postcode").val() == "") {
		alert("Veuillez saisir un code postal");
		return;
	}

	if ((typeof google) != "undefined") {
		var geocoder = new google.maps.Geocoder();
		var searchAdress = jQuery("#socolissimo_street").val() + ' ' + jQuery("#socolissimo_postcode").val() + ' ' + jQuery("#socolissimo_city").text() + ', ' + jQuery('#socolissimo_country').val();
		//alert('Search adresse : ' + searchAdress);
		geocoder.geocode({'address': searchAdress}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				socolissimoMyPosition = results[0].geometry.location;
				//on met à jour la carte avec cette position
				changeMap();
			} else {
				alert('Adresse invalide '+searchAdress);
			}
		});
	} else {
		alert("Géolocalisation de l'adresse impossible, vérifiez votre connexion internet (Google inaccessible).");
		//pour tester quand même sans géolocalisation :
		var socolissimoMyPositionClass = Class.create();
		socolissimoMyPositionClass.prototype.lat = function() { return 5; }
		socolissimoMyPositionClass.prototype.lng = function() { return 60; }
		socolissimoMyPosition = new socolissimoMyPositionClass();
		changeMap();
	}
}

function changeMap() {
	if (socolissimoMyPosition!=undefined) {
		loadListeRelais();
	}
}

function loadListeRelais() {
	jQuery(".soco_loader-wrapper").fadeTo(300, 1);
	socoUrl = socolissimoBaseUrl + "listrelais?"
	jQuery("#layer_socolissimo input:checkbox").each(function(index, element){
		check = jQuery(element);
		socoUrl = socoUrl + check.val() + "=" + check.is(":checked") + "&";
	});

	if( socoIsMobile ) {
		socoUrl = socoUrl + "adresse=0&zipcode=" + jQuery("#socolissimo_postcode").val()+ "&ville=0&country=" + jQuery("#socolissimo_country").val();
	} else {
		socoUrl = socoUrl + "adresse=" + jQuery("#socolissimo_street").val() + "&zipcode=" + jQuery("#socolissimo_postcode").val()+ "&ville=" + jQuery.trim(jQuery("#socolissimo_city").text()) + "&country=" + jQuery("#socolissimo_country").val();
	}

	socoUrl = socoUrl + "&latitude=" + socolissimoMyPosition.lat() + "&longitude=" + socolissimoMyPosition.lng();
	socoUrl = encodeURI(socoUrl);
	jQuery.getJSON( socoUrl, function(response) {
		if (!response.error) {
			socolissimoListRelais = response.items;
			jQuery("#adresses_socolissimo").html(response.html);
		} else {
			socolissimoListRelais = new Array();
			jQuery("#adresses_socolissimo").html('');
			alert(response.error);
		}
		showMap();
		jQuery(".soco_loader-wrapper").fadeTo(300, 0).hide();
	});


}
var indexMarker = 0;
function showMap() {
	if ((typeof google)!="undefined") {
		indexMarker = 0;

		var myOptions = {
			zoom: 15,
			center: socolissimoMyPosition,
			disableDefaultUI: socoIsMobile,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		socolissimoMap = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		/*
		 iconUrl = jQuery("#layer_socolissimo .soco_ligne1").css("background-image");
		 iconMatch = iconUrl.match("url\\(\"(.*)\"\\)");
		 if (iconMatch == null) {
		 //chrome
		 iconMatch = iconUrl.match("url\\((.*)\\)");
		 }
		 iconUrl = iconMatch[1];
		 */

		var marker = new google.maps.Marker({
			map: socolissimoMap,
			position: socolissimoMyPosition,
			icon : homeIconUrl
		});

		var init = false;
		google.maps.event.addListener(socolissimoMap, 'tilesloaded', function () {
			if (!init){
				for (icounter=0; icounter<socolissimoListRelais.length; icounter++) {
					relaisSocolissimo = socolissimoListRelais[icounter];
					var relaisPosition =  new google.maps.LatLng(relaisSocolissimo.latitude, relaisSocolissimo.longitude);
					marker = new google.maps.Marker({
						map: socolissimoMap,
						position: relaisPosition,
						title : relaisSocolissimo.libelle,
						icon : relaisSocolissimo.urlPicto
					});
					if (!socolissimoMap.getBounds().contains(relaisPosition)){
						newBounds = socolissimoMap.getBounds().extend(relaisPosition);
						socolissimoMap.fitBounds(newBounds);
					}
					infowindow=infoBulleGenerator(relaisSocolissimo);
					relaisSocolissimoInfowindow[icounter]=infowindow;
					relaisSocolissimoMarker[icounter]=marker;
					attachClick(marker,infowindow, icounter);
				}
				jQuery(document).on('click','#map_canvas span[data-id=commercant]',function(){
					choix = jQuery(this).attr('data-index');
					choisirRelais(choix);
				});
			}
			init=true;
		});
	}
}

//générateur d'infobulle
function infoBulleGenerator(relaisSocolissimo) {
	contentString = '<div class="soco_adresse">';
	if(relaisSocolissimo.type=='commercant') {
		contentString +='<div class="soco_entete '+relaisSocolissimo.type+'">Commerçant</div>';
	}
	if(relaisSocolissimo.type=='poste') {
		contentString +='<div class="soco_entete '+relaisSocolissimo.type+'">Bureau de poste</div>';
	}


	if (relaisSocolissimo.distance) {
		contentString += '<div class="soco_distance">à '+relaisSocolissimo.distance+' km</div>';
	}
	contentString += '<div class="soco_libelle">'+relaisSocolissimo.libelle+ '</div>'+
					 '<div class="soco_adresse-data">'+relaisSocolissimo.adresse+ '<br/>' + relaisSocolissimo.code_postal + ' ' + relaisSocolissimo.commune + '</div>';
	contentString += '<div class="soco_horaires">';
	if (relaisSocolissimo.conges_total) {
		contentString += '<b>En congés </b>';

	} else {
		contentString += '<div class="soco_title">Horaires d\'ouverture : ';
		if (relaisSocolissimo.deb_periode_horaire) {
			contentString += '<br/>valables du ' + relaisSocolissimo.deb_periode_horaire + ' au '+ relaisSocolissimo.fin_periode_horaire;
		}
		contentString += '</div>';
		if (relaisSocolissimo.horaire_lundi!='00:00-00:00 00:00-00:00') {contentString += '<b>Lundi:</b> '+ relaisSocolissimo.horaire_lundi + '<br/>'}
		if (relaisSocolissimo.horaire_mardi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mardi:</b> '+ relaisSocolissimo.horaire_mardi + '<br/>'}
		if (relaisSocolissimo.horaire_mercredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mercredi:</b> '+ relaisSocolissimo.horaire_mercredi + '<br/>'}
		if (relaisSocolissimo.horaire_jeudi!='00:00-00:00 00:00-00:00') {contentString += '<b>Jeudi:</b> '+ relaisSocolissimo.horaire_jeudi + '<br/>'}
		if (relaisSocolissimo.horaire_vendredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Vendredi:</b> '+ relaisSocolissimo.horaire_vendredi + '<br/>'}
		if (relaisSocolissimo.horaire_samedi!='00:00-00:00 00:00-00:00') {contentString += '<b>Samedi:</b> '+ relaisSocolissimo.horaire_samedi + '<br/>'}
		if (relaisSocolissimo.horaire_dimanche!='00:00-00:00 00:00-00:00') {contentString += '<b>Dimanche:</b> '+ relaisSocolissimo.horaire_dimanche+ '<br/>'}
	}
	contentString += '</div>';
	if (relaisSocolissimo.parking==1) {
		contentString += '<img src="/skin/frontend/base/default/images/socolissimo/picto_parking.jpg" />';
	}
	if (relaisSocolissimo.manutention==1) {
		contentString += '<img src="/skin/frontend/base/default/images/socolissimo/picto_manutention.jpg" />';
	}
	if (relaisSocolissimo.indicateur_acces==1) {
		contentString += '<img src="/skin/frontend/base/default/images/socolissimo/picto_mobilite_reduite.jpg" />';
	}
	if (relaisSocolissimo.fermetures.totalRecords>0) { contentString += '<div class="soco_title">Periodes de fermeture :</div>';
		for (i=0; i<relaisSocolissimo.fermetures.items.length; i++) {
			fermeture = relaisSocolissimo.fermetures.items[i];
			datedu = fermeture.deb_periode_fermeture;
			dateau = fermeture.fin_periode_fermeture;
			contentString += '<br/>du ' + datedu.substring(8,10) + '/' + datedu.substring(5,7) + '/' + datedu.substring(0,4) + ' au ' + dateau.substring(8,10) + '/' + dateau.substring(5,7) + '/' + dateau.substring(0,4);
		}
	}


	//contentString += '</p>';

	if (!relaisSocolissimo.conges_total){
		contentString += '<div class="soco_bouton"><span data-id="commercant" data-index="' + indexMarker + '"​>​Choisir ce lieu</span>​</div>';
	}
	contentString += '</div>';

	contentString = contentString.replace(new RegExp(' 00:00-00:00', 'g'),''); //on enlève les horaires de l'après midi si ils sont vides

	infowindow = new google.maps.InfoWindow({
		content: contentString
	});
	indexMarker++;

	return infowindow;
}

function attachClick(marker,infowindow, index){
	//Clic sur le relais dans la colonne de gauche
	jQuery("#point_retrait_"+index).click(function() {
		//fermer la derniere infobulle ouverte
		if(socolissimoOpenedInfowindow) {
			socolissimoOpenedInfowindow.close();
		}
		//ouvrir l'infobulle
		infowindow.open(socolissimoMap,marker);
		socolissimoOpenedInfowindow=infowindow;

	});

	//Clic sur le marqueur du relais dans la carte
	google.maps.event.addListener(marker, 'click', function() {
		//fermer la derniere infobulle ouverte
		if(socolissimoOpenedInfowindow) {
			socolissimoOpenedInfowindow.close();
		}
		socolissimoMap.panTo(marker.getPosition());
		//ouvrir l'infobulle
		infowindow.open(socolissimoMap,marker);
		socolissimoOpenedInfowindow=infowindow;

	});
}

function choisirRelais(index) {

	socolissimoRelaisChoisi = socolissimoListRelais[index];
	jQuery("#socolissimo-hook").html('<input type="hidden" name="relais_socolissimo" value="'+socolissimoRelaisChoisi.identifiant+'" />'+
									 '<input type="hidden" name="reseau_socolissimo" value="'+socolissimoRelaisChoisi.code_reseau+'" />');

	jQuery("input[id^=\"s_method_socolissimo\"]").each(function(index, element){
		//on sélectionne le bon radio, si on a changé de type de relais sur la carte, et on change le texte du numéro de téléphone
		var radio = jQuery(element);
		var types = new Array('poste','commercant');
		var len=types.length;
		for (var index=0; index<len; index++) {
			var socolissimoType = types[index];
			if (radio.val().startWith("socolissimo_"+socolissimoType)) {
				if (socolissimoRelaisChoisi.type==socolissimoType) {
					radio.prop("checked", "checked");	//on utilise prop au lieu de attr pour que le radio soit bien mis à jour
					jQuery("#socolissimo-telephone span.soco_"+socolissimoType).attr("style","display:block;");
				} else {
					radio.prop("checked", "");	//on utilise prop au lieu de attr pour que le radio soit bien mis à jour
					jQuery("#socolissimo-telephone span.soco_"+socolissimoType).attr("style","display:none;");
				}
			}
		}
	});

	if( socoIsMobile ) {
		jQuery('#map_canvas .soco_adresse').html(jQuery("#socolissimo-telephone").html());
		relaisSocolissimoInfowindow[index].open(socolissimoMap, relaisSocolissimoMarker[index]);
		jQuery('#map_canvas .soco_adresse').html(jQuery("#socolissimo-telephone").html());


		jQuery('#map_canvas .soco_adresse form').attr('id',jQuery('#map_canvas .soco_adresse form').attr('id')+'_po');
		socolissimoTelephoneForm = new VarienForm('socolissimo-telephone-form_po');
	} else {
		jQuery("#socolissimo-map").hide();
		jQuery("#socolissimo-telephone").show();
	}



	return;
}


function validerTelephone() {

	if(socolissimoTelephoneForm.validator && socolissimoTelephoneForm.validator.validate()){
		var telephone = jQuery("#socolissimo-telephone input[name='tel_socolissimo']").val();
		jQuery("#socolissimo-hook").append('<input type="hidden" name="tel_socolissimo" value="'+telephone+'" />');
		jQuery("#layer_socolissimo").popup('hide');
	}
	return false;
}

/** ajout de la fonction de validation numéro de téléphone portable */
Validation.add('valid-telephone-portable', 'Veuillez saisir un numéro de téléphone portable correct', function(v) {
	return (/^0(6|7)\d{8}$/.test(v) && !(/^0(6|7)(0{8}|1{8}|2{8}|3{8}|4{8}|5{8}|6{8}|7{8}|8{8}|9{8}|12345678)$/.test(v)));
});

Validation.add('valid-telephone-portable-belgique', 'Veuillez saisir un numéro de téléphone portable correct (en Belgique : +32400000000)', function(v) {
	//Pour les destinataires belges, le numéro de téléphone portable doit commencer par le caractère + suivi de 324, suivi de 8 chiffres
	return (/^\+324\d{8}$/.test(v) && !(/^\+324(0{8}|1{8}|2{8}|3{8}|4{8}|5{8}|6{8}|7{8}|8{8}|9{8}|12345678)$/.test(v)));
});
