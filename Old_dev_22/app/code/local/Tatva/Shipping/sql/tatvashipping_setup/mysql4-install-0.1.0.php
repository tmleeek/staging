<?php

$installer = $this;

$installer->startSetup();


// EXIG TRA-001
// REG  BO-710


$data = array(
    array('FR', 'GP', 'Guadeloupe'),
    array('FR', 'MQ', 'Martinique'),
    array('FR', 'GF', 'Guyane française'),
    array('FR', 'RE', 'Réunion'),
    array('FR', 'PM', 'Saint-Pierre-et-Miquelon'),
    array('FR', 'YT', 'Mayotte'),
    array('FR', 'TF', 'Terres australes françaises'),
    array('FR', 'WF', 'Wallis-et-Futuna'),
    array('FR', 'PF', 'Polynésie française'),
    array('FR', 'NC', 'Nouvelle-Calédonie'),
    array('FR', 'MC', 'Monaco')
);

foreach ($data as $row) {
    $bind = array(
        'country_id'    => $row[0],
        'code'          => $row[1],
        'default_name'  => $row[2],
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region'), $bind);
    $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory/country_region'));

    $bind = array(
        'locale'    => 'en_US',
        'region_id' => $regionId,
        'name'      => $row[2]
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);
}

$config = new Mage_Core_Model_Config();
$config->saveConfig('general/country/allow',"AF,ZA,AL,DZ,DE,AD,AO,AI,AQ,AG,AN,SA,AR,AM,AW,AU,AT,AZ,BS,BH,BD,BB,BE,BZ,BM,BT,BO,BA,BW,BN,BR,BG,BF,BI,BY,BJ,KH,CM,CA,CV,CL,CN,CY,CO,KM,CG,KP,KR,CR,HR,CU,CI,DK,DJ,DM,SV,ES,EE,FJ,FI,FR,GA,GM,GH,GI,GD,GL,GR,GU,GT,GG,GN,GQ,GW,GY,GE,GS,HT,HN,HU,IN,ID,IQ,IR,IE,IS,IL,IT,JM,JP,JE,JO,KZ,KE,KG,KI,KW,LA,LS,LV,LB,LY,LR,LI,LT,LU,MK,MG,MY,MW,MV,ML,MT,MA,MU,MR,MX,MD,MN,MS,ME,MZ,MM,NA,NR,NI,NE,NG,NU,NO,NZ,NP,OM,UG,UZ,PK,PW,PA,PG,PY,NL,PH,PN,PL,PR,PT,PE,QA,HK,MO,RO,GB,RU,RW,CF,DO,CD,CZ,EH,BL,KN,SM,MF,VC,SH,LC,WS,AS,ST,RS,SC,SL,SG,SK,SI,SO,SD,LK,CH,SR,SE,SJ,SZ,SY,SN,TJ,TZ,TW,TD,IO,PS,TH,TL,TG,TK,TO,TT,TN,TM,TR,TV,UA,UY,VU,VE,VN,YE,ZM,ZW,EG,AE,EC,ER,VA,FM,US,ET,BV,CX,NF,IM,KY,CC,CK,FO,HM,FK,MP,MH,UM,SB,TC,VG,VI,AX", 'default', 0);
$config->saveConfig('general/country/default',"FR", 'default', 0);
$config->saveConfig('general/locale/code',"fr_FR", 'default', 0);
$config->saveConfig('currency/options/base',"EUR", 'default', 0);
$config->saveConfig('currency/options/default',"EUR", 'default', 0);
$config->saveConfig('currency/options/allow',"EUR", 'default', 0);



$installer->endSetup();