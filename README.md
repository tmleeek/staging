Magento Module For la Poste SoColissimo Shipping
================================================


This extension is compatible with Magento Community since v1.6

License
-------

OSL 3.0



Documentation
-------------



System Requirements
-------------------

PHP 5.3 or higher

Magento CE1.4.x-1.9.x/EE1.9.x-1.14.x



Installation
------------

This repository contains also a .modman file in order to allow a modman installation https://github.com/colinmollenhour/modman :

```
modman clone git@git.jetpulp.hosting:php/mg_mod_SoColissimo.git
```

If the module is already checkouted on the server you can use the link method of modman :

```
modman init
modman link /path/to/mg_mod_SoColissimo
```

Packaging
---------

The magentoconnect.php generate a magentoconnect archive file, it need a complete magento path as a parameter

```
php -f  magentoconnect.php /path/to/magento
```

The archive file is created in the /path/to/magento/var/connect directory


Deploy on http://www.modules-ecommerce.fr/
-------------------------------------------

Rename the archive 649fc61ecfb0b4d4a3bc613c07a61f75 and upload it on server in out/downloads/64
Rename pdf doc 226c3e23abf2b6e0f0bd1bf453b3b644 and upload it on server in out/downloads/22

See DB :

`select * from oxfiles where oxfiles.OXARTID  in ('hph2b5ffd9c878e94e4a6c5c5b1add94','hphabfc274ac539972d9e7b2462fd9dc','nrv452f14f516cf6667a894c0b3fc9d8','nrv857f0790010ef1b80e29ed9e36f16','hph9c3dc9eb846251803469c649b3f3d',  'nrvb3edd4353667222c61949f7b89854');`
`select * from oxarticles where oxid in in ('hph2b5ffd9c878e94e4a6c5c5b1add94','hphabfc274ac539972d9e7b2462fd9dc','nrv452f14f516cf6667a894c0b3fc9d8','nrv857f0790010ef1b80e29ed9e36f16','hph9c3dc9eb846251803469c649b3f3d',  'nrvb3edd4353667222c61949f7b89854');`

UPDATE OXFILENAME in OXFILES if the name of file change.

`UPDATE oxorderfiles set OXFILENAME=(SELECT OXFILENAME from oxfiles where oxfiles.oxid= oxorderfiles.oxfileid);`

FAQ
---

Changelog
---------

[2.2.1 - 13 juin 2016]
Mise à jour de la documentation
Utilisation d'une API pour les codes postaux
Bouton accès direct à Colissimo Box (Back Office)
Bug Fix : Vérification de license
Optimisation paramétrage du Back Office (cacher les champs inutiles)
Bug Fix : Cas un seul relais
Gestion de l'offre Europe Colissimo (Relais Colis)
Bug Fix : Enregistrement de l'adresse de livraison
Bug Fix : Code postaux avec espaces

[2.2.0 - 15 décembre 2015]
Gestion des étiquettes via Webservice Colissimo

[2.1.4 - 4 novembre 2015]
Correctifs suite à l'application du patch SUPEE-6788 (APPSEC-1034)

[2.1.3 - 03 août 2015]
Suppression des modes Cityssimo et RDV

[2.1.2 - 14 novembre 2014]
Préfixage des classes CSS socolissimo pour éviter les conflits

[2.1.1 - 13 octobre 2014]
Correction de bug sur la méthode isFlexibilite()

[2.1.0 - 28 avril 2014]
Version thème mobile

[2.0.8 - 8 avril 2014]
Correction erreur en mode compilation
Suppression WS geaoname au profit d'un stockage des noms de ville en db locale
Frais de port offerts via une règle promo panier
correction https
correction compatibilité onestepcheckout

[2.0.7 - 3 janvier 2014]
Compatibilité magento 1.8
Compatibilité autre module livraison addonline (GLS)
URL webservice socolissimo en https

[2.0.6 - 8 août 2013]
correction expéditions commandes passées avant migration module v2
correction recherche villes avec accents
bug relais colis fermés

[2.0.5 - 26 juin 2013]
bug reset à la fermeture du layer

[2.0.4 - 20 juin 2013]
Correction module SoColissimo pour https

[2.0.3 - 25 mai 2013]
Socolissimo : ajout bouton lancement batch import manuel
compatibilité onestepcheckout
correction message header already send socolissimo
gérer le cas de la compilation
compatibilité magento < 1.6

[2.0.2 - 19 avril 2013]
Version 2 du module SoColissimo : 
fusion module Liberté et Flexibilité
livraison en Belgique
compatibilité autres modules (owebia, etc...)



