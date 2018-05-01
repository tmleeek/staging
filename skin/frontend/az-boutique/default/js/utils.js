var paragAffiche = null;

function augmenterQuantite(idChamp) {
	var champ = document.getElementById(idChamp);
	if (isNaN(champ.value)) {
		return false;
	}
	champ.value = parseInt(champ.value)+1;
	return false;
}
function diminuerQuantite(idChamp) {
	var champ = document.getElementById(idChamp);
	if (isNaN(champ.value)) {
		return false;
	}
	if (parseInt(champ.value) > 0) {
		champ.value = parseInt(champ.value)-1;
	}
	return false;
}
function afficherCacherFAQ(paragAAfficher) {
	if (paragAffiche != null && paragAffiche != paragAAfficher){
		paragAffiche.style.display = "none";
		paragAffiche.parentNode.className="";
	}
	if (paragAAfficher.style.display != "none") {
		paragAAfficher.style.display = "none";
		paragAAfficher.parentNode.className="";
		paragAffiche = null;
	} else {
		paragAAfficher.style.display = "block";
		paragAAfficher.parentNode.className="lien-fleche-vers-bas";
		paragAffiche = paragAAfficher;
	}
	return false;
}
function afficherPopin(idPopin) {
	document.getElementById(idPopin).style.display="block";
	return false;
}
function cacherPopin(idPopin) {
	document.getElementById(idPopin).style.display="none";
	return false;
}
function switchOngletMenuGauche(idOnglet) {
	document.getElementById('lien-onglet-articles').style.display="none";
	document.getElementById('lien-onglet-marques').style.display="none";
	document.getElementById('contenu-onglet-articles').style.display="none";
	document.getElementById('contenu-onglet-marques').style.display="none";

	document.getElementById('lien-onglet-'+idOnglet).style.display="block";
	document.getElementById('contenu-onglet-'+idOnglet).style.display="block";

	return false;
}
function afficherSousMenu(sousMenuAAfficher) {
	sousMenuAAfficher.style.display = "block";
	sousMenuAAfficher.parentNode.style.position = "relative";
}
function cacherSousMenu(sousMenuAAfficher) {
	sousMenuAAfficher.style.display = "none";
	sousMenuAAfficher.parentNode.style.position = "static";
}
function afficherSousMenu4(sousMenuAAfficher) {
	sousMenuAAfficher.style.display = "block";
	sousMenuAAfficher.parentNode.style.position = "relative";
	sousMenuAAfficher.parentNode.className = "active";
}
function cacherSousMenu4(sousMenuAAfficher) {
	sousMenuAAfficher.style.display = "none";
	sousMenuAAfficher.parentNode.style.position = "static";
	sousMenuAAfficher.parentNode.className = "";
}

function afficherSousMenu2(sousMenuAAfficher) {
	document.getElementById('menu-principal').style.position="static";
	sousMenuAAfficher.style.display = "block";
}
function cacherSousMenu2(sousMenuAAfficher) {
	document.getElementById('menu-principal').style.position="relative";
	sousMenuAAfficher.style.display = "none";
}

function afficherSousMenu5(sousMenuAAfficher) {
    //alert(sousMenuAAfficher.id);
	sousMenuAAfficher.style.display = "block";
	sousMenuAAfficher.parentNode.style.position = "relative";
	sousMenuAAfficher.parentNode.className = "active";
}
function cacherSousMenu5(sousMenuAAfficher) {
     //alert(sousMenuAAfficher.id);
	sousMenuAAfficher.style.display = "none";
	sousMenuAAfficher.parentNode.style.position = "static";
	sousMenuAAfficher.parentNode.className = "";
}


function afficherImageZoomee(lienZoom) {
     jQuery(lienZoom.parentNode).next().show();
	//lienZoom.parentNode.nextSibling.style.display="block";
}
function cacherImageZoomee(lienZoom) {
    jQuery(lienZoom.parentNode).next().hide();
	//lienZoom.parentNode.nextSibling.style.display="none";
}
var menuGaucheFicheProduitAffiche = false;

/*
function afficherMenuGaucheFicheProduit() {
	if (menuGaucheFicheProduitAffiche){
		menuGaucheFicheProduitAffiche = false;
		document.getElementById('gauche-page-fiche-produit-m').style.width="0";
		document.getElementById('gauche-page-fiche-produit-m').className="gauche-page-fiche-produit-m";
		document.getElementById('gauche-page').style.marginLeft="-150px";
	} else {
		menuGaucheFicheProduitAffiche = true;
		document.getElementById('gauche-page-fiche-produit-m').style.width="149px";
		document.getElementById('gauche-page-fiche-produit-m').className="gauche-page-fiche-produit-m gauche-page-fiche-produit-m-ouvert";
		document.getElementById('gauche-page').style.marginLeft="0";
	}
	
	return false;
}
*/

function afficherMenuGaucheFicheProduit(country) {

	if (menuGaucheFicheProduitAffiche){
		menuGaucheFicheProduitAffiche = false;
		document.getElementById('gauche-page-fiche-produit-m').style.width="0";
                if(country=="en" || country=="proen")
                    document.getElementById('gauche-page-fiche-produit-m').className="gauche-page-fiche-produit-m";
		else if(country=="fr" || country=="profr")
                    document.getElementById('gauche-page-fiche-produit-m').className="gauche-page-fiche-produit-m-fr";


		document.getElementById('gauche-page').style.marginLeft="-150px";
	} else {
		menuGaucheFicheProduitAffiche = true;
		document.getElementById('gauche-page-fiche-produit-m').style.width="149px";
                if(country=="en" || country=="proen")
                    document.getElementById('gauche-page-fiche-produit-m').className="gauche-page-fiche-produit-m gauche-page-fiche-produit-m-ouvert";
                else if(country=="fr" || country=="profr")
                    document.getElementById('gauche-page-fiche-produit-m').className="gauche-page-fiche-produit-m-fr gauche-page-fiche-produit-m-ouvert-fr";

		document.getElementById('gauche-page').style.marginLeft="0";
	}

	return false;
}
var lienCategorieNiveau2Courante = null;
function afficherCacherCategorieNiveau3(lienCategorieNiveau2) {
	if (lienCategorieNiveau2Courante != null && lienCategorieNiveau2.parentNode.className != "active") {
		lienCategorieNiveau2Courante.parentNode.className = "";
	}
	if (lienCategorieNiveau2.parentNode.className != "active") {
		lienCategorieNiveau2.parentNode.className = "active";
		lienCategorieNiveau2Courante = lienCategorieNiveau2;
	} else {
		lienCategorieNiveau2.parentNode.className = "";
		lienCategorieNiveau2Courante = null;
	}
	
	return false;
}


function CreateBookmarkLink() {

	title = document.title; 
	
	url = document.location.href;
	
	if (window.sidebar) { // Mozilla Firefox Bookmark
		window.sidebar.addPanel(title, url,"");
	} else if( window.external ) { // IE Favorite
		window.external.AddFavorite( url, title); }
	else if(window.opera && window.print) { // Opera Hotlist
		return false; }
	return false;
}