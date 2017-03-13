<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: register.php.t,v 1.144 2005/03/21 13:55:03 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function tmpl_draw_select_opt($values, $names, $selected)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (count($vls) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values inside a select<br>\n");
	}

	$options = '';
	foreach ($vls as $k => $v) {
		$options .= '<option value="'.$v.'"'.($v == $selected ? ' selected' : '' )  .'>'.$nms[$k].'</option>';
	}

	return $options;
}function tmpl_draw_radio_opt($name, $values, $names, $selected, $sep)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (count($vls) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values<br>\n");
	}

	$checkboxes = '';
	foreach ($vls as $k => $v) {
		$checkboxes .= '<input type="radio" name="'.$name.'" value="'.$v.'" '.($v == $selected ? 'checked ' : '' )  .'>'.$nms[$k].$sep;
	}

	return $checkboxes;
}if (strpos(PHP_OS, 'AIX') !== false) {
	$GLOBALS['tz_names'] = "Coordinated Universal Time\nUnited Kingdom\nAzores, Cape Verde\nFalkland Islands\nGreenland, East Brazil\nCentral Brazil\nEastern United States, Colombia\nCentral United States, Honduras\nMountain United States\nPacific United States, Yukon\nAlaska\nHawaii, Aleutian Islands\nBering Strait\nNew Zealand\nSolomon Islands\nEastern Australia\nJapan\nKorea\nWestern Australia\nTaiwan\nThailand\nCentral Asia\nPakistan\nGorki, Central Asia, Oman\nTurkey\nSaudi Arabia\nFinland\nSouth Africa\nNorway";
	$GLOBALS['tz_values'] = "CUT0GDT\nGMT0BST\nAZOREST1AZOREDT\nFALKST2FALKDT\nGRNLNDST3GRNLNDDT\nAST4ADT\nEST5EDT\nCST6CDT\nMST7MDT\nPST8PDT\nAST9ADT\nHST10HDT\nBST11BDT\nNZST-12NZDT\nMET-11METDT\nEET-10EETDT\nJST-9JSTDT\nKORST-9KORDT\nWAUST-8WAUDT\nTAIST-8TAIDT\nTHAIST-7THAIDT\nTASHST-6TASHDT\nPAKST-5PAKDT\nWST-4WDT\nMEST-3MEDT\nSAUST-3SAUDT\nWET-2WET\nUSAST-2USADT\nNFT-1DFT";
} else {
	$GLOBALS['tz_names'] = "\nAfghanistan/Kabul\nAlbania/Tirane\nAlgeria/Algiers\nAndorra/Andorra\nAngola/Luanda\nAnguilla/Anguilla\nAntarctica/Casey Casey Station, Bailey Peninsula\nAntarctica/Davis Davis Station, Vestfold Hills\nAntarctica/DumontDUrville Dumont-d'Urville Base, Terre Adelie\nAntarctica/Mawson Mawson Station, Holme Bay\nAntarctica/McMurdo McMurdo Station, Ross Island\nAntarctica/Palmer Palmer Station, Anvers Island\nAntarctica/South Pole Amundsen-Scott Station, South Pole\nAntarctica/Syowa Syowa Station, E Ongul I\nAntigua & Barbuda/Antigua\nArgentina/Buenos Aires E Argentina (BA, DF, SC, TF)\nArgentina/Catamarca Catamarca (CT)\nArgentina/Cordoba W Argentina (CB, SA, TM, LR, SJ, SL, NQ, RN)\nArgentina/Jujuy Jujuy (JY)\nArgentina/Mendoza Mendoza (MZ)\nArgentina/Rosario NE Argentina (SF, ER, CN, MN, CC, FM, LP, CH)\nArmenia/Yerevan\nAruba/Aruba\nAustralia/Adelaide South Australia\nAustralia/Brisbane Queensland - most locations\nAustralia/Broken Hill New South Wales - Broken Hill\nAustralia/Darwin Northern Territory\nAustralia/Hobart Tasmania\nAustralia/Lindeman Queensland - Holiday Islands\nAustralia/Lord Howe Lord Howe Island\nAustralia/Melbourne Victoria\nAustralia/Perth Western Australia\nAustralia/Sydney New South Wales - most locations\nAustria/Vienna\nAzerbaijan/Baku\nBahamas/Nassau\nBahrain/Bahrain\nBangladesh/Dhaka\nBarbados/Barbados\nBelarus/Minsk\nBelgium/Brussels\nBelize/Belize\nBenin/Porto-Novo\nBermuda/Bermuda\nBhutan/Thimphu\nBolivia/La Paz\nBosnia & Herzegovina/Sarajevo\nBotswana/Gaborone\nBrazil/Araguaina Tocantins\nBrazil/Belem Amapa, E Para\nBrazil/Boa Vista Roraima\nBrazil/Cuiaba Mato Grosso, Mato Grosso do Sul\nBrazil/Eirunepe W Amazonas\nBrazil/Fortaleza NE Brazil (MA, PI, CE, RN, PR)\nBrazil/Maceio Alagoas, Sergipe\nBrazil/Manaus E Amazonas\nBrazil/Noronha Atlantic islands\nBrazil/Porto Acre Acre\nBrazil/Porto Velho W Para, Rondonia\nBrazil/Recife Pernambuco\nBrazil/Sao Paulo S & SE Brazil (BA, GO, DF, MG, ES, RJ, SP, PR, SC, RS)\nBritain (UK)/Belfast Northern Ireland\nBritain (UK)/London Great Britain\nBritish Indian Ocean Territory/Chagos\nBrunei/Brunei\nBulgaria/Sofia\nBurkina Faso/Ouagadougou\nBurundi/Bujumbura\nCambodia/Phnom Penh\nCameroon/Douala\nCanada/Cambridge Bay Central Time - west Nunavut\nCanada/Dawson Pacific Time - north Yukon\nCanada/Dawson Creek Mountain Standard Time - Dawson Creek & Fort Saint John, British Columbia\nCanada/Edmonton Mountain Time - Alberta, east British Columbia & west Saskatchewan\nCanada/Glace Bay Atlantic Time - Nova Scotia - places that did not observe DST 1966-1971\nCanada/Goose Bay Atlantic Time - E Labrador\nCanada/Halifax Atlantic Time - Nova Scotia (most places), NB, W Labrador, E Quebec & PEI\nCanada/Inuvik Mountain Time - west Northwest Territories\nCanada/Iqaluit Eastern Standard Time - east Nunavut\nCanada/Montreal Eastern Time - Ontario & Quebec - most locations\nCanada/Nipigon Eastern Time - Ontario & Quebec - places that did not observe DST 1967-1973\nCanada/Pangnirtung Eastern Standard Time - Pangnirtung, Nunavut\nCanada/Rainy River Central Time - Rainy River & Fort Frances, Ontario\nCanada/Rankin Inlet Eastern Standard Time - central Nunavut\nCanada/Regina Central Standard Time - Saskatchewan - most locations\nCanada/St Johns Newfoundland Island\nCanada/Swift Current Central Standard Time - Saskatchewan - midwest\nCanada/Thunder Bay Eastern Time - Thunder Bay, Ontario\nCanada/Vancouver Pacific Time - west British Columbia\nCanada/Whitehorse Pacific Time - south Yukon\nCanada/Winnipeg Central Time - Manitoba & west Ontario\nCanada/Yellowknife Mountain Time - central Northwest Territories\nCape Verde/Cape Verde\nCayman Islands/Cayman\nCentral African Rep./Bangui\nChad/Ndjamena\nChile/Easter Easter Island\nChile/Santiago mainland\nChina/Chungking China mountains\nChina/Harbin north Manchuria\nChina/Kashgar Eastern Turkestan\nChina/Shanghai China coast\nChina/Urumqi Tibet & Xinjiang\nChristmas Island/Christmas\nCocos (Keeling) Islands/Cocos\nColombia/Bogota\nComoros/Comoro\nCongo (Dem. Rep.)/Kinshasa west Dem. Rep. of Congo\nCongo (Dem. Rep.)/Lubumbashi east Dem. Rep. of Congo\nCongo (Rep.)/Brazzaville\nCook Islands/Rarotonga\nCosta Rica/Costa Rica\nCote d'Ivoire/Abidjan\nCroatia/Zagreb\nCuba/Havana\nCyprus/Nicosia\nCzech Republic/Prague\nDenmark/Copenhagen\nDjibouti/Djibouti\nDominica/Dominica\nDominican Republic/Santo Domingo\nEast Timor/Dili\nEcuador/Galapagos Galapagos Islands\nEcuador/Guayaquil mainland\nEgypt/Cairo\nEl Salvador/El Salvador\nEquatorial Guinea/Malabo\nEritrea/Asmera\nEstonia/Tallinn\nEthiopia/Addis Ababa\nFaeroe Islands/Faeroe\nFalkland Islands/Stanley\nFiji/Fiji\nFinland/Helsinki\nFrance/Paris\nFrench Guiana/Cayenne\nFrench Polynesia/Gambier Gambier Islands\nFrench Polynesia/Marquesas Marquesas Islands\nFrench Polynesia/Tahiti Society Islands\nFrench Southern & Antarctic Lands/Kerguelen\nGabon/Libreville\nGambia/Banjul\nGeorgia/Tbilisi\nGermany/Berlin\nGhana/Accra\nGibraltar/Gibraltar\nGreece/Athens\nGreenland/Godthab southwest Greenland\nGreenland/Scoresbysund east Greenland\nGreenland/Thule northwest Greenland\nGrenada/Grenada\nGuadeloupe/Guadeloupe\nGuam/Guam\nGuatemala/Guatemala\nGuinea/Conakry\nGuinea-Bissau/Bissau\nGuyana/Guyana\nHaiti/Port-au-Prince\nHonduras/Tegucigalpa\nHong Kong/Hong Kong\nHungary/Budapest\nIceland/Reykjavik\nIndia/Calcutta\nIndonesia/Jakarta Java & Sumatra\nIndonesia/Jayapura Irian Jaya & the Moluccas\nIndonesia/Ujung Pandang Borneo & Celebes\nIran/Tehran\nIraq/Baghdad\nIreland/Dublin\nIsrael/Jerusalem\nItaly/Rome\nJamaica/Jamaica\nJapan/Tokyo\nJordan/Amman\nKazakhstan/Almaty east Kazakhstan\nKazakhstan/Aqtau west Kazakhstan\nKazakhstan/Aqtobe central Kazakhstan\nKenya/Nairobi\nKiribati/Enderbury Phoenix Islands\nKiribati/Kiritimati Line Islands\nKiribati/Tarawa Gilbert Islands\nKorea (North)/Pyongyang\nKorea (South)/Seoul\nKuwait/Kuwait\nKyrgyzstan/Bishkek\nLaos/Vientiane\nLatvia/Riga\nLebanon/Beirut\nLesotho/Maseru\nLiberia/Monrovia\nLibya/Tripoli\nLiechtenstein/Vaduz\nLithuania/Vilnius\nLuxembourg/Luxembourg\nMacao/Macao\nMacedonia/Skopje\nMadagascar/Antananarivo\nMalawi/Blantyre\nMalaysia/Kuala Lumpur peninsular Malaysia\nMalaysia/Kuching Sabah & Sarawak\nMaldives/Maldives\nMali/Bamako southwest Mali\nMali/Timbuktu northeast Mali\nMalta/Malta\nMarshall Islands/Kwajalein Kwajalein\nMarshall Islands/Majuro most locations\nMartinique/Martinique\nMauritania/Nouakchott\nMauritius/Mauritius\nMayotte/Mayotte\nMexico/Cancun Central Time - Quintana Roo\nMexico/Chihuahua Mountain Time - Chihuahua\nMexico/Hermosillo Mountain Standard Time - Sonora\nMexico/Mazatlan Mountain Time - S Baja, Nayarit, Sinaloa\nMexico/Merida Central Time - Campeche, Yucatan\nMexico/Mexico City Central Time - most locations\nMexico/Monterrey Central Time - Coahuila, Durango, Nuevo Leon, Tamaulipas\nMexico/Tijuana Pacific Time\nMicronesia/Kosrae Kosrae\nMicronesia/Ponape Ponape (Pohnpei)\nMicronesia/Truk Truk (Chuuk)\nMicronesia/Yap Yap\nMoldova/Chisinau most locations\nMoldova/Tiraspol Transdniestria\nMonaco/Monaco\nMongolia/Hovd Bayan-Olgiy, Hovd, Uvs\nMongolia/Ulaanbaatar most locations\nMontserrat/Montserrat\nMorocco/Casablanca\nMozambique/Maputo\nMyanmar (Burma)/Rangoon\nNamibia/Windhoek\nNauru/Nauru\nNepal/Katmandu\nNetherlands/Amsterdam\nNetherlands Antilles/Curacao\nNew Caledonia/Noumea\nNew Zealand/Auckland most locations\nNew Zealand/Chatham Chatham Islands\nNicaragua/Managua\nNiger/Niamey\nNigeria/Lagos\nNiue/Niue\nNorfolk Island/Norfolk\nNorthern Mariana Islands/Saipan\nNorway/Oslo\nOman/Muscat\nPakistan/Karachi\nPalau/Palau\nPalestine/Gaza\nPanama/Panama\nPapua New Guinea/Port Moresby\nParaguay/Asuncion\nPeru/Lima\nPhilippines/Manila\nPitcairn/Pitcairn\nPoland/Warsaw\nPortugal/Azores Azores\nPortugal/Lisbon mainland\nPortugal/Madeira Madeira Islands\nPuerto Rico/Puerto Rico\nQatar/Qatar\nReunion/Reunion\nRomania/Bucharest\nRussia/Anadyr Moscow+10 - Bering Sea\nRussia/Irkutsk Moscow+05 - Lake Baikal\nRussia/Kaliningrad Moscow-01 - Kaliningrad\nRussia/Kamchatka Moscow+09 - Kamchatka\nRussia/Krasnoyarsk Moscow+04 - Yenisei River\nRussia/Magadan Moscow+08 - Magadan & Sakhalin\nRussia/Moscow Moscow+00 - west Russia\nRussia/Novosibirsk Moscow+03 - Novosibirsk\nRussia/Omsk Moscow+03 - west Siberia\nRussia/Samara Moscow+01 - Caspian Sea\nRussia/Vladivostok Moscow+07 - Amur River\nRussia/Yakutsk Moscow+06 - Lena River\nRussia/Yekaterinburg Moscow+02 - Urals\nRwanda/Kigali\nSamoa (American)/Pago Pago\nSamoa (Western)/Apia\nSan Marino/San Marino\nSao Tome & Principe/Sao Tome\nSaudi Arabia/Riyadh\nSenegal/Dakar\nSeychelles/Mahe\nSierra Leone/Freetown\nSingapore/Singapore\nSlovakia/Bratislava\nSlovenia/Ljubljana\nSolomon Islands/Guadalcanal\nSomalia/Mogadishu\nSouth Africa/Johannesburg\nSouth Georgia & the South Sandwich Islands/South Georgia\nSpain/Canary Canary Islands\nSpain/Ceuta Ceuta & Melilla\nSpain/Madrid mainland\nSri Lanka/Colombo\nSt Helena/St Helena\nSt Kitts & Nevis/St Kitts\nSt Lucia/St Lucia\nSt Pierre & Miquelon/Miquelon\nSt Vincent/St Vincent\nSudan/Khartoum\nSuriname/Paramaribo\nSvalbard & Jan Mayen/Jan Mayen Jan Mayen\nSvalbard & Jan Mayen/Longyearbyen Svalbard\nSwaziland/Mbabane\nSweden/Stockholm\nSwitzerland/Zurich\nSyria/Damascus\nTaiwan/Taipei\nTajikistan/Dushanbe\nTanzania/Dar es Salaam\nThailand/Bangkok\nTogo/Lome\nTokelau/Fakaofo\nTonga/Tongatapu\nTrinidad & Tobago/Port of Spain\nTunisia/Tunis\nTurkey/Istanbul\nTurkmenistan/Ashgabat\nTurks & Caicos Is/Grand Turk\nTuvalu/Funafuti\nUS minor outlying islands/Johnston Johnston Atoll\nUS minor outlying islands/Midway Midway Islands\nUS minor outlying islands/Wake Wake Island\nUganda/Kampala\nUkraine/Kiev most locations\nUkraine/Simferopol central Crimea\nUkraine/Uzhgorod Ruthenia\nUkraine/Zaporozhye Zaporozh'ye, E Lugansk\nUnited Arab Emirates/Dubai\nUnited States/Adak Aleutian Islands\nUnited States/Anchorage Alaska Time\nUnited States/Boise Mountain Time - south Idaho & east Oregon\nUnited States/Chicago Central Time\nUnited States/Denver Mountain Time\nUnited States/Detroit Eastern Time - Michigan - most locations\nUnited States/Honolulu Hawaii\nUnited States/Indiana Eastern Standard Time - Indiana - Crawford County\nUnited States/Indiana Eastern Standard Time - Indiana - Starke County\nUnited States/Indiana Eastern Standard Time - Indiana - Switzerland County\nUnited States/Indianapolis Eastern Standard Time - Indiana - most locations\nUnited States/Juneau Alaska Time - Alaska panhandle\nUnited States/Kentucky Eastern Time - Kentucky - Wayne County\nUnited States/Los Angeles Pacific Time\nUnited States/Louisville Eastern Time - Kentucky - Louisville area\nUnited States/Menominee Central Time - Michigan - Wisconsin border\nUnited States/New York Eastern Time\nUnited States/Nome Alaska Time - west Alaska\nUnited States/Phoenix Mountain Standard Time - Arizona\nUnited States/Shiprock Mountain Time - Navajo\nUnited States/Yakutat Alaska Time - Alaska panhandle neck\nUruguay/Montevideo\nUzbekistan/Samarkand west Uzbekistan\nUzbekistan/Tashkent east Uzbekistan\nVanuatu/Efate\nVatican City/Vatican\nVenezuela/Caracas\nVietnam/Saigon\nVirgin Islands (UK)/Tortola\nVirgin Islands (US)/St Thomas\nWallis & Futuna/Wallis\nWestern Sahara/El Aaiun\nYemen/Aden\nYugoslavia/Belgrade\nZambia/Lusaka\nZimbabwe/Harare";
	$GLOBALS['tz_values'] = "\nAsia/Kabul\nEurope/Tirane\nAfrica/Algiers\nEurope/Andorra\nAfrica/Luanda\nAmerica/Anguilla\nAntarctica/Casey\nAntarctica/Davis\nAntarctica/DumontDUrville\nAntarctica/Mawson\nAntarctica/McMurdo\nAntarctica/Palmer\nAntarctica/South_Pole\nAntarctica/Syowa\nAmerica/Antigua\nAmerica/Buenos_Aires\nAmerica/Catamarca\nAmerica/Cordoba\nAmerica/Jujuy\nAmerica/Mendoza\nAmerica/Rosario\nAsia/Yerevan\nAmerica/Aruba\nAustralia/Adelaide\nAustralia/Brisbane\nAustralia/Broken_Hill\nAustralia/Darwin\nAustralia/Hobart\nAustralia/Lindeman\nAustralia/Lord_Howe\nAustralia/Melbourne\nAustralia/Perth\nAustralia/Sydney\nEurope/Vienna\nAsia/Baku\nAmerica/Nassau\nAsia/Bahrain\nAsia/Dhaka\nAmerica/Barbados\nEurope/Minsk\nEurope/Brussels\nAmerica/Belize\nAfrica/Porto-Novo\nAtlantic/Bermuda\nAsia/Thimphu\nAmerica/La_Paz\nEurope/Sarajevo\nAfrica/Gaborone\nAmerica/Araguaina\nAmerica/Belem\nAmerica/Boa_Vista\nAmerica/Cuiaba\nAmerica/Eirunepe\nAmerica/Fortaleza\nAmerica/Maceio\nAmerica/Manaus\nAmerica/Noronha\nAmerica/Porto_Acre\nAmerica/Porto_Velho\nAmerica/Recife\nAmerica/Sao_Paulo\nEurope/Belfast\nEurope/London\nIndian/Chagos\nAsia/Brunei\nEurope/Sofia\nAfrica/Ouagadougou\nAfrica/Bujumbura\nAsia/Phnom_Penh\nAfrica/Douala\nAmerica/Cambridge_Bay\nAmerica/Dawson\nAmerica/Dawson_Creek\nAmerica/Edmonton\nAmerica/Glace_Bay\nAmerica/Goose_Bay\nAmerica/Halifax\nAmerica/Inuvik\nAmerica/Iqaluit\nAmerica/Montreal\nAmerica/Nipigon\nAmerica/Pangnirtung\nAmerica/Rainy_River\nAmerica/Rankin_Inlet\nAmerica/Regina\nAmerica/St_Johns\nAmerica/Swift_Current\nAmerica/Thunder_Bay\nAmerica/Vancouver\nAmerica/Whitehorse\nAmerica/Winnipeg\nAmerica/Yellowknife\nAtlantic/Cape_Verde\nAmerica/Cayman\nAfrica/Bangui\nAfrica/Ndjamena\nPacific/Easter\nAmerica/Santiago\nAsia/Chungking\nAsia/Harbin\nAsia/Kashgar\nAsia/Shanghai\nAsia/Urumqi\nIndian/Christmas\nIndian/Cocos\nAmerica/Bogota\nIndian/Comoro\nAfrica/Kinshasa\nAfrica/Lubumbashi\nAfrica/Brazzaville\nPacific/Rarotonga\nAmerica/Costa_Rica\nAfrica/Abidjan\nEurope/Zagreb\nAmerica/Havana\nAsia/Nicosia\nEurope/Prague\nEurope/Copenhagen\nAfrica/Djibouti\nAmerica/Dominica\nAmerica/Santo_Domingo\nAsia/Dili\nPacific/Galapagos\nAmerica/Guayaquil\nAfrica/Cairo\nAmerica/El_Salvador\nAfrica/Malabo\nAfrica/Asmera\nEurope/Tallinn\nAfrica/Addis_Ababa\nAtlantic/Faeroe\nAtlantic/Stanley\nPacific/Fiji\nEurope/Helsinki\nEurope/Paris\nAmerica/Cayenne\nPacific/Gambier\nPacific/Marquesas\nPacific/Tahiti\nIndian/Kerguelen\nAfrica/Libreville\nAfrica/Banjul\nAsia/Tbilisi\nEurope/Berlin\nAfrica/Accra\nEurope/Gibraltar\nEurope/Athens\nAmerica/Godthab\nAmerica/Scoresbysund\nAmerica/Thule\nAmerica/Grenada\nAmerica/Guadeloupe\nPacific/Guam\nAmerica/Guatemala\nAfrica/Conakry\nAfrica/Bissau\nAmerica/Guyana\nAmerica/Port-au-Prince\nAmerica/Tegucigalpa\nAsia/Hong_Kong\nEurope/Budapest\nAtlantic/Reykjavik\nAsia/Calcutta\nAsia/Jakarta\nAsia/Jayapura\nAsia/Ujung_Pandang\nAsia/Tehran\nAsia/Baghdad\nEurope/Dublin\nAsia/Jerusalem\nEurope/Rome\nAmerica/Jamaica\nAsia/Tokyo\nAsia/Amman\nAsia/Almaty\nAsia/Aqtau\nAsia/Aqtobe\nAfrica/Nairobi\nPacific/Enderbury\nPacific/Kiritimati\nPacific/Tarawa\nAsia/Pyongyang\nAsia/Seoul\nAsia/Kuwait\nAsia/Bishkek\nAsia/Vientiane\nEurope/Riga\nAsia/Beirut\nAfrica/Maseru\nAfrica/Monrovia\nAfrica/Tripoli\nEurope/Vaduz\nEurope/Vilnius\nEurope/Luxembourg\nAsia/Macao\nEurope/Skopje\nIndian/Antananarivo\nAfrica/Blantyre\nAsia/Kuala_Lumpur\nAsia/Kuching\nIndian/Maldives\nAfrica/Bamako\nAfrica/Timbuktu\nEurope/Malta\nPacific/Kwajalein\nPacific/Majuro\nAmerica/Martinique\nAfrica/Nouakchott\nIndian/Mauritius\nIndian/Mayotte\nAmerica/Cancun\nAmerica/Chihuahua\nAmerica/Hermosillo\nAmerica/Mazatlan\nAmerica/Merida\nAmerica/Mexico_City\nAmerica/Monterrey\nAmerica/Tijuana\nPacific/Kosrae\nPacific/Ponape\nPacific/Truk\nPacific/Yap\nEurope/Chisinau\nEurope/Tiraspol\nEurope/Monaco\nAsia/Hovd\nAsia/Ulaanbaatar\nAmerica/Montserrat\nAfrica/Casablanca\nAfrica/Maputo\nAsia/Rangoon\nAfrica/Windhoek\nPacific/Nauru\nAsia/Katmandu\nEurope/Amsterdam\nAmerica/Curacao\nPacific/Noumea\nPacific/Auckland\nPacific/Chatham\nAmerica/Managua\nAfrica/Niamey\nAfrica/Lagos\nPacific/Niue\nPacific/Norfolk\nPacific/Saipan\nEurope/Oslo\nAsia/Muscat\nAsia/Karachi\nPacific/Palau\nAsia/Gaza\nAmerica/Panama\nPacific/Port_Moresby\nAmerica/Asuncion\nAmerica/Lima\nAsia/Manila\nPacific/Pitcairn\nEurope/Warsaw\nAtlantic/Azores\nEurope/Lisbon\nAtlantic/Madeira\nAmerica/Puerto_Rico\nAsia/Qatar\nIndian/Reunion\nEurope/Bucharest\nAsia/Anadyr\nAsia/Irkutsk\nEurope/Kaliningrad\nAsia/Kamchatka\nAsia/Krasnoyarsk\nAsia/Magadan\nEurope/Moscow\nAsia/Novosibirsk\nAsia/Omsk\nEurope/Samara\nAsia/Vladivostok\nAsia/Yakutsk\nAsia/Yekaterinburg\nAfrica/Kigali\nPacific/Pago_Pago\nPacific/Apia\nEurope/San_Marino\nAfrica/Sao_Tome\nAsia/Riyadh\nAfrica/Dakar\nIndian/Mahe\nAfrica/Freetown\nAsia/Singapore\nEurope/Bratislava\nEurope/Ljubljana\nPacific/Guadalcanal\nAfrica/Mogadishu\nAfrica/Johannesburg\nAtlantic/South_Georgia\nAtlantic/Canary\nAfrica/Ceuta\nEurope/Madrid\nAsia/Colombo\nAtlantic/St_Helena\nAmerica/St_Kitts\nAmerica/St_Lucia\nAmerica/Miquelon\nAmerica/St_Vincent\nAfrica/Khartoum\nAmerica/Paramaribo\nAtlantic/Jan_Mayen\nArctic/Longyearbyen\nAfrica/Mbabane\nEurope/Stockholm\nEurope/Zurich\nAsia/Damascus\nAsia/Taipei\nAsia/Dushanbe\nAfrica/Dar_es_Salaam\nAsia/Bangkok\nAfrica/Lome\nPacific/Fakaofo\nPacific/Tongatapu\nAmerica/Port_of_Spain\nAfrica/Tunis\nEurope/Istanbul\nAsia/Ashgabat\nAmerica/Grand_Turk\nPacific/Funafuti\nPacific/Johnston\nPacific/Midway\nPacific/Wake\nAfrica/Kampala\nEurope/Kiev\nEurope/Simferopol\nEurope/Uzhgorod\nEurope/Zaporozhye\nAsia/Dubai\nAmerica/Adak\nAmerica/Anchorage\nAmerica/Boise\nAmerica/Chicago\nAmerica/Denver\nAmerica/Detroit\nPacific/Honolulu\nAmerica/Indiana/Marengo\nAmerica/Indiana/Knox\nAmerica/Indiana/Vevay\nAmerica/Indianapolis\nAmerica/Juneau\nAmerica/Kentucky/Monticello\nAmerica/Los_Angeles\nAmerica/Louisville\nAmerica/Menominee\nAmerica/New_York\nAmerica/Nome\nAmerica/Phoenix\nAmerica/Shiprock\nAmerica/Yakutat\nAmerica/Montevideo\nAsia/Samarkand\nAsia/Tashkent\nPacific/Efate\nEurope/Vatican\nAmerica/Caracas\nAsia/Saigon\nAmerica/Tortola\nAmerica/St_Thomas\nPacific/Wallis\nAfrica/El_Aaiun\nAsia/Aden\nEurope/Belgrade\nAfrica/Lusaka\nAfrica/Harare";
}function tmpl_post_options($arg, $perms=0)
{
	$post_opt_html		= '<b>HTML</b> code is <b>OFF</b>';
	$post_opt_fud		= '<b>FUDcode</b> is <b>OFF</b>';
	$post_opt_images 	= '<b>Images</b> are <b>OFF</b>';
	$post_opt_smilies	= '<b>Smilies</b> are <b>OFF</b>';
	$edit_time_limit	= '';

	if (is_int($arg)) {
		if ($arg & 16) {
			$post_opt_fud = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($arg & 8)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($perms & 16384) {
			$post_opt_smilies = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
		if ($perms & 32768) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		$edit_time_limit = $GLOBALS['EDIT_TIME_LIMIT'] ? '<br><b>Editing Time Limit</b>: <b>'.$GLOBALS['EDIT_TIME_LIMIT'].'</b> minutes' : '<br><b>Editing Time Limit</b>: <b>Unlimited</b>';
	} else if ($arg == 'private') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 4096) {
			$post_opt_fud = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($o & 2048)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($o & 16384) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		if ($o & 8192) {
			$post_opt_smilies = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
	} else if ($arg == 'sig') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 131072) {
			$post_opt_fud = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($o & 65536)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($o & 524288) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		if ($o & 262144) {
			$post_opt_smilies = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
	}

	return '<span class="SmallText"><b>Forum Options</b><br />
'.$post_opt_html.'<br />
'.$post_opt_fud.'<br />
'.$post_opt_images.'<br />
'.$post_opt_smilies.$edit_time_limit.'</span>';
}class fud_user
{
	var $id, $login, $alias, $passwd, $plaintext_passwd, $name, $email, $location, $occupation, $interests,
	    $icq, $aim, $yahoo, $msnm, $jabber, $affero, $avatar, $avatar_loc, $posts_ppg, $time_zone, $bday, $home_page,
	    $sig, $bio, $posted_msg_count, $last_visit, $last_event, $conf_key, $user_image, $join_date, $theme, $last_read,
	    $mod_list, $mod_cur, $level_id, $u_last_post_id, $users_opt, $cat_collapse_status, $ignore_list, $buddy_list;
}

function make_alias($text)
{
	if (strlen($text) > $GLOBALS['MAX_LOGIN_SHOW']) {
		$text = substr($text, 0, $GLOBALS['MAX_LOGIN_SHOW']);
	}
	return char_fix(htmlspecialchars($text));
}

class fud_user_reg extends fud_user
{
	function html_fields()
	{
		foreach(array('name', 'location', 'occupation', 'interests', 'bio') as $v) {
			if ($this->{$v}) {
				$this->{$v} = char_fix(htmlspecialchars($this->$v));
			}
		}
	}

	function add_user()
	{
		if (isset($_COOKIES['frm_referer_id']) && (int)$_COOKIES['frm_referer_id']) {
			$ref_id = (int)$_COOKIES['frm_referer_id'];
		} else {
			$ref_id = 0;
		}

		$md5pass = md5($this->plaintext_passwd);
		$o2 =& $GLOBALS['FUD_OPT_2'];

		$this->alias = make_alias((!($o2 & 128) || !$this->alias) ? $this->login : $this->alias);

		/* this used when utilities create users (aka nntp/mlist import) */
		if ($this->users_opt == -1) {
			$this->users_opt = 4|16|32|128|256|512|2048|4096|8192|16384|131072|4194304;
			$this->theme = q_singleval("SELECT id FROM fud26_themes WHERE theme_opt>=2 AND (theme_opt & 2) > 0 LIMIT 1");
			$this->time_zone =& $GLOBALS['SERVER_TZ'];
			$this->posts_ppg =& $GLOBALS['POSTS_PER_PAGE'];
			if (!($o2 & 4)) {
				$this->users_opt ^= 128;
			}
			if (!($o2 & 8)) {
				$this->users_opt ^= 256;
			}
			if ($o2 & 1) {
				$o2 ^= 1;
			}
			$reg_ip = "127.0.0.1";
		} else {
			$reg_ip = get_ip();
		}

		if (empty($this->join_date)) {
			$this->join_date = __request_timestamp__;
		}

		if ($o2 & 1) {
			$this->conf_key = md5(implode('', (array)$this) . __request_timestamp__ . getmypid());
		} else {
			$this->conf_key = '';
			$this->users_opt |= 131072;
		}
		$this->icq = (int)$this->icq ? (int)$this->icq : 'NULL';

		$this->html_fields();

		$this->id = db_qid("INSERT INTO
			fud26_users (
				login,
				alias,
				passwd,
				name,
				email,
				icq,
				aim,
				yahoo,
				msnm,
				jabber,
				affero,
				posts_ppg,
				time_zone,
				bday,
				last_visit,
				conf_key,
				user_image,
				join_date,
				location,
				theme,
				occupation,
				interests,
				referer_id,
				last_read,
				sig,
				home_page,
				bio,
				users_opt,
				reg_ip
			) VALUES (
				'".addslashes($this->login)."',
				'".addslashes($this->alias)."',
				'".$md5pass."',
				'".addslashes($this->name)."',
				'".addslashes($this->email)."',
				".$this->icq.",
				".ssn(urlencode($this->aim)).",
				".ssn(urlencode($this->yahoo)).",
				".ssn(urlencode($this->msnm)).",
				".ssn(htmlspecialchars($this->jabber)).",
				".ssn(urlencode($this->affero)).",
				".(int)$this->posts_ppg.",
				'".addslashes($this->time_zone)."',
				".(int)$this->bday.",
				".__request_timestamp__.",
				'".$this->conf_key."',
				".ssn(htmlspecialchars($this->user_image)).",
				".$this->join_date.",
				".ssn($this->location).",
				".(int)$this->theme.",
				".ssn($this->occupation).",
				".ssn($this->interests).",
				".(int)$ref_id.",
				".__request_timestamp__.",
				".ssn($this->sig).",
				".ssn(htmlspecialchars($this->home_page)).",
				".ssn($this->bio).",
				".$this->users_opt.",
				".ip2long($reg_ip)."
			)
		");

		return $this->id;
	}

	function sync_user()
	{
		$passwd = !empty($this->plaintext_passwd) ? "passwd='".md5($this->plaintext_passwd)."'," : '';

		$this->alias = make_alias((!($GLOBALS['FUD_OPT_2'] & 128) || !$this->alias) ? $this->login : $this->alias);
		$this->icq = (int)$this->icq ? (int)$this->icq : 'NULL';

		$rb_mod_list = (!($this->users_opt & 524288) && ($is_mod = q_singleval("SELECT id FROM fud26_mod WHERE user_id={$this->id}")) && (q_singleval("SELECT alias FROM fud26_users WHERE id={$this->id}") == $this->alias));

		$this->html_fields();

		q("UPDATE fud26_users SET ".$passwd."
			name='".addslashes($this->name)."',
			alias='".addslashes($this->alias)."',
			email='".addslashes($this->email)."',
			icq=".$this->icq.",
			aim=".ssn(urlencode($this->aim)).",
			yahoo=".ssn(urlencode($this->yahoo)).",
			msnm=".ssn(urlencode($this->msnm)).",
			jabber=".ssn(htmlspecialchars($this->jabber)).",
			affero=".ssn(urlencode($this->affero)).",
			posts_ppg='".(int)$this->posts_ppg."',
			time_zone='".addslashes($this->time_zone)."',
			bday=".(int)$this->bday.",
			user_image=".ssn(htmlspecialchars($this->user_image)).",
			location=".ssn($this->location).",
			occupation=".ssn($this->occupation).",
			interests=".ssn($this->interests).",
			avatar=".(int)$this->avatar.",
			theme=".(int)$this->theme.",
			avatar_loc=".ssn($this->avatar_loc).",
			sig=".ssn($this->sig).",
			home_page=".ssn(htmlspecialchars($this->home_page)).",
			bio=".ssn($this->bio).",
			users_opt=".$this->users_opt."
		WHERE id=".$this->id);

		if ($rb_mod_list) {
			rebuildmodlist();
		}
	}
}

function get_id_by_email($email)
{
	return q_singleval("SELECT id FROM fud26_users WHERE email='".addslashes($email)."'");
}

function get_id_by_login($login)
{
	return q_singleval("SELECT id FROM fud26_users WHERE login='".addslashes($login)."'");
}

function usr_email_unconfirm($id)
{
	$conf_key = md5(__request_timestamp__ . $id . get_random_value());
	q("UPDATE fud26_users SET users_opt=users_opt & ~ 131072, conf_key='".$conf_key."' WHERE id=".$id);
	return $conf_key;
}

function &usr_reg_get_full($id)
{
	if (($r = db_sab('SELECT * FROM fud26_users WHERE id='.$id))) {
		if (!extension_loaded("overload")) {
			$o = new fud_user_reg;
			foreach ($r as $k => $v) {
				$o->{$k} = $v;
			}
			$r = $o;
		} else {
			aggregate_methods($r, 'fud_user_reg');
		}
	}
	return $r;
}

function user_login($id, $cur_ses_id, $use_cookies)
{
	if (!$use_cookies && isset($_COOKIE[$GLOBALS['COOKIE_NAME']])) {
		/* remove cookie so it does not confuse us */
		setcookie($GLOBALS['COOKIE_NAME'], '', __request_timestamp__-100000, $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);
	}
	if ($GLOBALS['FUD_OPT_2'] & 256 && ($s = db_saq('SELECT ses_id, sys_id FROM fud26_ses WHERE user_id='.$id))) {
		if ($use_cookies) {
			setcookie($GLOBALS['COOKIE_NAME'], $s[0], __request_timestamp__+$GLOBALS['COOKIE_TIMEOUT'], $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);
		}
		if ($s[1]) {
			q("UPDATE fud26_ses SET sys_id='' WHERE ses_id='".$s[0]."'");
		}
		return $s[0];
	}

	/* if we can only have 1 login per account, 'remove' all other logins */
	q("DELETE FROM fud26_ses WHERE user_id=".$id." AND ses_id!='".$cur_ses_id."'");
	q("UPDATE fud26_ses SET user_id=".$id.", sys_id='".ses_make_sysid()."' WHERE ses_id='".$cur_ses_id."'");
	$GLOBALS['new_sq'] = regen_sq();
	q("UPDATE fud26_users SET sq='".$GLOBALS['new_sq']."' WHERE id=".$id);

	return $cur_ses_id;
}

function rebuildmodlist()
{
	$tbl =& $GLOBALS['DBHOST_TBL_PREFIX'];
	$lmt =& $GLOBALS['SHOW_N_MODS'];
	$c = uq('SELECT u.id, u.alias, f.id FROM '.$tbl.'mod mm INNER JOIN '.$tbl.'users u ON mm.user_id=u.id INNER JOIN '.$tbl.'forum f ON f.id=mm.forum_id ORDER BY f.id,u.alias');
	$u = $ar = array();
	
	while ($r = db_rowarr($c)) {
		$u[] = $r[0];
		if ($lmt < 1 || (isset($ar[$r[2]]) && count($ar[$r[2]]) >= $lmt)) {
			continue;
		}
		$ar[$r[2]][$r[0]] = $r[1];
	}

	q('UPDATE '.$tbl.'forum SET moderators=NULL');
	foreach ($ar as $k => $v) {
		q('UPDATE '.$tbl.'forum SET moderators='.strnull(addslashes(serialize($v))).' WHERE id='.$k);
	}
	q('UPDATE '.$tbl.'users SET users_opt=users_opt & ~ 524288 WHERE users_opt>=524288 AND (users_opt & 524288) > 0');
	if ($u) {
		q('UPDATE '.$tbl.'users SET users_opt=users_opt|524288 WHERE id IN('.implode(',', $u).') AND (users_opt & 1048576)=0');
	}
}$GLOBALS['seps'] = array(' '=>' ', "\n"=>"\n", "\r"=>"\r", "'"=>"'", '"'=>'"', '['=>'[', ']'=>']', '('=>'(', ';'=>';', ')'=>')', "\t"=>"\t", '='=>'=', '>'=>'>', '<'=>'<');

function fud_substr_replace($str, $newstr, $pos, $len)
{
        return substr($str, 0, $pos).$newstr.substr($str, $pos+$len);
}

function tags_to_html($str, $allow_img=1, $no_char=0)
{
	if (!$no_char) {
		$str = htmlspecialchars($str);
	}

	$str = nl2br($str);

	$ostr = '';
	$pos = $old_pos = 0;

	while (($pos = strpos($str, '[', $pos)) !== false) {
		if (isset($GLOBALS['seps'][$str[$pos + 1]])) {
			++$pos;
			continue;
		}

		if (($epos = strpos($str, ']', $pos)) === false) {
			break;
		}
		if (!($epos-$pos-1)) {
			$pos = $epos + 1;
			continue;
		}
		$tag = substr($str, $pos+1, $epos-$pos-1);
		if (($pparms = strpos($tag, '=')) !== false) {
			$parms = substr($tag, $pparms+1);
			if (!$pparms) { /*[= exception */
				$pos = $epos+1;
				continue;
			}
			$tag = substr($tag, 0, $pparms);
		} else {
			$parms = '';
		}

		$tag = strtolower($tag);

		switch ($tag) {
			case 'quote title':
				$tag = 'quote';
				break;
			case 'list type':
				$tag = 'list';
				break;
		}

		if ($tag[0] == '/') {
			if (isset($end_tag[$pos])) {
				if( ($pos-$old_pos) ) $ostr .= substr($str, $old_pos, $pos-$old_pos);
				$ostr .= $end_tag[$pos];
				$pos = $old_pos = $epos+1;
			} else {
				$pos = $epos+1;
			}

			continue;
		}

		$cpos = $epos;
		$ctag = '[/'.$tag.']';
		$ctag_l = strlen($ctag);
		$otag = '['.$tag;
		$otag_l = strlen($otag);
		$rf = 1;
		$nt_tag = 0;
		while (($cpos = strpos($str, '[', $cpos)) !== false) {
			if (isset($end_tag[$cpos]) || isset($GLOBALS['seps'][$str[$cpos + 1]])) {
				++$cpos;
				continue;
			}

			if (($cepos = strpos($str, ']', $cpos)) === false) {
				if (!$nt_tag) {
					break 2;
				} else {
					break;
				}
			}

			if (strcasecmp(substr($str, $cpos, $ctag_l), $ctag) == 0) {
				--$rf;
			} else if (strcasecmp(substr($str, $cpos, $otag_l), $otag) == 0) {
				++$rf;
			} else {
				$nt_tag++;
				++$cpos;
				continue;
			}

			if (!$rf) {
				break;
			}
			$cpos = $cepos;
		}

		if (!$cpos || ($rf && $str[$cpos] == '<')) { /* left over [ handler */
			++$pos;
			continue;
		}

		if ($cpos !== false) {
			if (($pos-$old_pos)) {
				$ostr .= substr($str, $old_pos, $pos-$old_pos);
			}
			switch ($tag) {
				case 'notag':
					$ostr .= '<span name="notag">'.substr($str, $epos+1, $cpos-1-$epos).'</span>';
					$epos = $cepos;
					break;
				case 'url':
					if (!$parms) {
						$url = substr($str, $epos+1, ($cpos-$epos)-1);
					} else {
						$url = $parms;
					}

					if (!strncasecmp($url, 'www.', 4)) {
						$url = 'http&#58;&#47;&#47;'. $url;
					} else if (strpos(strtolower($url), 'javascript:') !== false) {
						$ostr .= substr($str, $pos, $cepos - $pos + 1);
						$epos = $cepos;
						$str[$cpos] = '<';
						break;
					} else {
						$url = str_replace('://', '&#58;&#47;&#47;', $url);
					}

					$end_tag[$cpos] = '</a>';
					$ostr .= '<a href="'.$url.'" target="_blank">';
					break;
				case 'i':
				case 'u':
				case 'b':
				case 's':
				case 'sub':
				case 'sup':
				case 'del':
					$end_tag[$cpos] = '</'.$tag.'>';
					$ostr .= '<'.$tag.'>';
					break;
				case 'email':
					if (!$parms) {
						$parms = str_replace('@', '&#64;', substr($str, $epos+1, ($cpos-$epos)-1));
						$ostr .= '<a href="mailto:'.$parms.'" target="_blank">'.$parms.'</a>';
						$epos = $cepos;
						$str[$cpos] = '<';
					} else {
						$end_tag[$cpos] = '</a>';
						$ostr .= '<a href="mailto:'.str_replace('@', '&#64;', $parms).'" target="_blank">';
					}
					break;
				case 'color':
				case 'size':
				case 'font':
					if ($tag == 'font') {
						$tag = 'face';
					}
					$end_tag[$cpos] = '</font>';
					$ostr .= '<font '.$tag.'="'.$parms.'">';
					break;
				case 'code':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);

					$ostr .= '<div class="pre"><pre>'.reverse_nl2br($param).'</pre></div>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'pre':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);

					$ostr .= '<pre>'.reverse_nl2br($param).'</pre>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'php':
					$param = trim(reverse_fmt(reverse_nl2br(substr($str, $epos+1, ($cpos-$epos)-1))));

					if (strncmp($param, '<?php', 5)) {
						if (strncmp($param, '<?', 2)) {
							$param = "<?php\n" . $param;
						} else {
							$param = "<?php\n" . substr($param, 3);
						}
					}
					if (substr($param, -2) != '?>') {
						$param .= "\n?>";
					}

					$ostr .= '<span name="php">'.trim(@highlight_string($param, true)).'</span>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'img':
				case 'imgl':
				case 'imgr':
					if (!$allow_img) {
						$ostr .= substr($str, $pos, ($cepos-$pos)+1);
					} else {
						$class = ($tag == 'img') ? '' : 'class="'.$tag{3}.'" ';

						if (!$parms) {
							$parms = substr($str, $epos+1, ($cpos-$epos)-1);
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img '.$class.'src="'.$parms.'" border=0 alt="'.$parms.'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						} else {
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img '.$class.'src="'.$parms.'" border=0 alt="'.substr($str, $epos+1, ($cpos-$epos)-1).'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						}
					}
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'quote':
					if (!$parms) {
						$parms = 'Quote:';
					}
					$ostr .= '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>'.$parms.'</b></td></tr><tr><td class="quote"><br />';
					$end_tag[$cpos] = '<br /></td></tr></table>';
					break;
				case 'align':
					$end_tag[$cpos] = '</div>';
					$ostr .= '<div align="'.$parms.'">';
					break;
				case 'list':
					$tmp = substr($str, $epos, ($cpos-$epos));
					$tmp_l = strlen($tmp);
					$tmp2 = str_replace(array('[*]', '<br />'), array('<li>', ''), $tmp);
					$tmp2_l = strlen($tmp2);
					$str = str_replace($tmp, $tmp2, $str);

					$diff = $tmp2_l - $tmp_l;
					$cpos += $diff;

					if (isset($end_tag)) {
						foreach($end_tag as $key => $val) {
							if ($key < $epos) {
								continue;
							}

							$end_tag[$key+$diff] = $val;
						}
					}

					switch (strtolower($parms)) {
						case '1':
						case 'a':
							$end_tag[$cpos] = '</ol>';
							$ostr .= '<ol type="'.$parms.'">';
							break;
						case 'square':
						case 'circle':
						case 'disc':
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul type="'.$parms.'">';
							break;
						default:
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul>';
					}
					break;
				case 'spoiler':
					$rnd = rand();
					$end_tag[$cpos] = '</div></div>';
					$ostr .= '<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis(\''.$rnd.'\', 1);">'
						.($parms ? $parms : 'Toggle Spoiler').'</a><div align="left" id="'.$rnd.'" style="display: none;">';
					break;
				case 'acronym':
					$end_tag[$cpos] = '</acronym>';
					$ostr .= '<acronym title="'.($parms ? $parms : ' ').'">';
					break;
			}

			$str[$pos] = '<';
			$pos = $old_pos = $epos+1;
		} else {
			$pos = $epos+1;
		}
	}
	$ostr .= substr($str, $old_pos, strlen($str)-$old_pos);

	/* url paser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '://', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}
		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i > $ppos) {
			if ($ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if (!$pos || $ostr[$i] == '<') {
			$pos += 3;
			continue;
		}

		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the span tag
		if (($ts = strpos($ostr, '<span>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</span>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		$us = $pos;
		$l = strlen($ostr);
		while (1) {
			--$us;
			if ($ppos > $us || $us >= $l || isset($GLOBALS['seps'][$ostr[$us]])) {
				break;
			}
		}

		unset($GLOBALS['seps']['=']);
		$ue = $pos;
		while (1) {
			++$ue;
			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}

			if ($ostr[$ue] == '&') {
				if ($ostr[$ue+4] == ';') {
					$ue += 4;
					continue;
				}
				if ($ostr[$ue+3] == ';' || $ostr[$ue+5] == ';') {
					break;
				}
			}

			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}
		}
		$GLOBALS['seps']['='] = '=';

		$url = substr($ostr, $us+1, $ue-$us-1);
		if (!strncasecmp($url, 'javascript', strlen('javascript')) || ($ue - $us - 1) < 9) {
			$pos = $ue;
			continue;
		}
		$html_url = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		$html_url_l = strlen($html_url);
		$ostr = fud_substr_replace($ostr, $html_url, $us+1, $ue-$us-1);
		$ppos = $pos;
		$pos = $us+$html_url_l;
	}

	/* email parser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '@', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}

		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i>$ppos) {
			if ( $ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($i < 0 || $ostr[$i]=='<') {
			++$pos;
			continue;
		}


		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<div class="pre"><pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre></div>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		for ($es = ($pos - 1); $es > ($ppos - 1); $es--) {
			if (
				( ord($ostr[$es]) >= ord('A') && ord($ostr[$es]) <= ord('z') ) ||
				( ord($ostr[$es]) >= ord(0) && ord($ostr[$es]) <= ord(9) ) ||
				( $ostr[$es] == '.' || $ostr[$es] == '-' || $ostr[$es] == '\'')
			) { continue; }
			++$es;
			break;
		}
		if ($es == $pos) {
			$ppos = $pos += 1;
			continue;
		}
		if ($es < 0) {
			$es = 0;
		}

		for ($ee = ($pos + 1); @isset($ostr[$ee]); $ee++) {
			if (
				( ord($ostr[$ee]) >= ord('A') && ord($ostr[$ee]) <= ord('z') ) ||
				( ord($ostr[$ee]) >= ord(0) && ord($ostr[$ee]) <= ord(9) ) ||
				( $ostr[$ee] == '.' || $ostr[$ee] == '-' )
			) { continue; }
			break;
		}
		if ($ee == ($pos+1)) {
			$ppos = $pos += 1;
			continue;
		}

		$email = str_replace('@', '&#64;', substr($ostr, $es, $ee-$es));
		$email_url = '<a href="mailto:'.$email.'" target="_blank">'.$email.'</a>';
		$email_url_l = strlen($email_url);
		$ostr = fud_substr_replace($ostr, $email_url, $es, $ee-$es);
		$ppos =	$es+$email_url_l;
		$pos = $ppos;
	}

	return $ostr;
}

function html_to_tags($fudml)
{
	while (preg_match('!<span name="php">(.*?)</span>!is', $fudml, $res)) {
		$tmp = trim(html_entity_decode(strip_tags(str_replace('<br />', "\n", $res[1]))));
		$m = md5($tmp);
		$php[$m] = $tmp;
		$fudml = str_replace($res[0], "[php]\n".$m."\n[/php]", $fudml);
	}

	if (strpos($fudml, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')  !== false) {
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br />','<br /></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
		// old bad code
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br>','<br></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
	}

	/* old format */
	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">.*?</a><div align="left" id="(.*?)" style="visibility: hidden;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">(.*?)\</a\>\<div align\="left" id\=".*?" style\="visibility: hidden;"\>!is', '[spoiler=\1]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	/* new format */	
	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">.*?</a><div align="left" id="(.*?)" style="display: none;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">(.*?)\</a\>\<div align\="left" id\=".*?" style\="display: none;"\>!is', '[spoiler=\1]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	$fudml = str_replace('<font face=', '<font font=', $fudml);
	foreach (array('color', 'font', 'size') as $v) {
		while (preg_match('!<font '.$v.'=".+?">.*?</font>!is', $fudml, $m)) {
			$fudml = preg_replace('!<font '.$v.'="(.+?)">(.*?)</font>!is', '['.$v.'=\1]\2[/'.$v.']', $fudml);
		}
	}

	while (preg_match('!<acronym title=".+?">.*?</acronym>!is', $fudml)) {
		$fudml = preg_replace('!<acronym title="(.+?)">(.*?)</acronym>!is', '[acronym=\1]\2[/acronym]', $fudml);
	}
	while (preg_match('!<(o|u)l type=".+?">.*?</\\1l>!is', $fudml)) {
		$fudml = preg_replace('!<(o|u)l type="(.+?)">(.*?)</\\1l>!is', '[list type=\2]\3[/list]', $fudml);
	}

	$fudml = str_replace(
	array(
		'<b>', '</b>', '<i>', '</i>', '<u>', '</u>', '<s>', '</s>', '<sub>', '</sub>', '<sup>', '</sup>', '<del>', '</del>',
		'<div class="pre"><pre>', '</pre></div>', '<div align="center">', '<div align="left">', '<div align="right">', '</div>',
		'<ul>', '</ul>', '<span name="notag">', '</span>', '<li>', '&#64;', '&#58;&#47;&#47;', '<br />', '<pre>', '</pre>'
	),
	array(
		'[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]', '[sub]', '[/sub]', '[sup]', '[/sup]', '[del]', '[/del]', 
		'[code]', '[/code]', '[align=center]', '[align=left]', '[align=right]', '[/align]', '[list]', '[/list]',
		'[notag]', '[/notag]', '[*]', '@', '://', '', '[pre]', '[/pre]'
	),
	$fudml);

	while (preg_match('!<img src="(.*?)" border=0 alt="\\1">!is', $fudml)) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="\\1">!is', '[img]\1[/img]', $fudml);
	}
	while (preg_match('!<img class="(r|l)" src="(.*?)" border=0 alt="\\2">!is', $fudml)) {
		$fudml = preg_replace('!<img class="(r|l)" src="(.*?)" border=0 alt="\\2">!is', '[img\1]\2[/img\1]', $fudml);
	}
	while (preg_match('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', '[email]\1[/email]', $fudml);
	}
	while (preg_match('!<a href="(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">\\1</a>!is', '[url]\1[/url]', $fudml);
	}

	if (strpos($fudml, '<img src="') !== false) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="(.*?)">!is', '[img=\1]\2[/img]', $fudml);
	}
	if (strpos($fudml, '<img class="') !== false) {
		$fudml = preg_replace('!<img class="(r|l)" src="(.*?)" border=0 alt="(.*?)">!is', '[img\1=\2]\3[/img\1]', $fudml);
	}
	if (strpos($fudml, '<a href="mailto:') !== false) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">(.+?)</a>!is', '[email=\1]\2[/email]', $fudml);
	}
	if (strpos($fudml, '<a href="') !== false) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">(.+?)</a>!is', '[url=\1]\2[/url]', $fudml);
	}

	if (isset($php)) {
		$fudml = str_replace(array_keys($php), array_values($php), $fudml);
	}

	/* unhtmlspecialchars */
	return reverse_fmt($fudml);
}


function filter_ext($file_name)
{
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
	if (empty($GLOBALS['__FUD_EXT_FILER__'])) {
		return;
	}
	if (($p = strrpos($file_name, '.')) === false) {
		return 1;
	}
	return !in_array(strtolower(substr($file_name, ($p + 1))), $GLOBALS['__FUD_EXT_FILER__']);
}

function safe_tmp_copy($source, $del_source=0, $prefx='')
{
	if (!$prefx) {
		 $prefx = getmypid();
	}

	$umask = umask(($GLOBALS['FUD_OPT_2'] & 8388608 ? 0177 : 0111));
	if (!move_uploaded_file($source, ($name = tempnam($GLOBALS['TMP'], $prefx.'_')))) {
		return;
	}
	umask($umask);
	if ($del_source) {
		@unlink($source);
	}
	umask($umask);

	return basename($name);
}

function reverse_nl2br(&$data)
{
	if (strpos($data, '<br />') !== false) {
		return str_replace('<br />', '', $data);
	}
	return $data;
}$GLOBALS['__revfs'] = array('&quot;', '&lt;', '&gt;', '&amp;');
$GLOBALS['__revfd'] = array('"', '<', '>', '&');

function reverse_fmt($data)
{
	$s = $d = array();
	foreach ($GLOBALS['__revfs'] as $k => $v) {
		if (strpos($data, $v) !== false) {
			$s[] = $v;
			$d[] = $GLOBALS['__revfd'][$k];
		}
	}

	return $s ? str_replace($s, $d, $data) : $data;
}function fud_wrap_tok($data)
{
	$wa = array();
	$len = strlen($data);

	$i=$j=$p=0;
	$str = '';
	while ($i < $len) {
		switch ($data{$i}) {
			case ' ':
			case "\n":
			case "\t":
				if ($j) {
					$wa[] = array('word'=>$str, 'len'=>($j+1));
					$j=0;
					$str ='';
				}

				$wa[] = array('word'=>$data[$i]);

				break;
			case '<':
				if (($p = strpos($data, '>', $i)) !== false) {
					if ($j) {
						$wa[] = array('word'=>$str, 'len'=>($j+1));
						$j=0;
						$str ='';
					}
					$s = substr($data, $i, ($p - $i) + 1);
					if ($s == '<pre>') {
						$s = substr($data, $i, ($p = (strpos($data, '</pre>', $p) + 6)) - $i);
						--$p;
					} else if ($s == '<span name="php">') {
						$s = substr($data, $i, ($p = (strpos($data, '</span>', $p) + 7)) - $i);
						--$p;
					}

					$wa[] = array('word' => $s);

					$i = $p;
					$j = 0;
				} else {
					$str .= $data[$i];
					$j++;
				}
				break;

			case '&':
				if (($e = strpos($data, ';', $i))) {
					$st = substr($data, $i, ($e - $i + 1));
					if (($st{1} == '#' && is_numeric(substr($st, 3, -1))) || !strcmp($st, '&nbsp;') || !strcmp($st, '&gt;') || !strcmp($st, '&lt;') || !strcmp($st, '&quot;')) {
						if ($j) {
							$wa[] = array('word'=>$str, 'len'=>($j+1));
							$j=0;
							$str ='';
						}

						$wa[] = array('word' => $st, 'sp' => 1);
						$i=$e;
						$j=0;
						break;
					}
				} /* fall through */
			default:
				$str .= $data[$i];
				$j++;
		}
		$i++;
	}

	if ($j) {
		$wa[] = array('word'=>$str, 'len'=>($j+1));
	}

	return $wa;
}

function fud_wordwrap(&$data)
{
	if (!$GLOBALS['WORD_WRAP'] || $GLOBALS['WORD_WRAP'] >= strlen($data)) {
		return;
	}

	$wa = fud_wrap_tok($data);
	$m = (int) $GLOBALS['WORD_WRAP'];
	$l = 0;
	$data = '';
	foreach($wa as $v) {
		if (isset($v['len']) && $v['len'] > $m) {
			if ($v['len'] + $l > $m) {
				$l = 0;
				$data .= ' ';
			}
			$data .= wordwrap($v['word'], $m, ' ', 1);
			$l += $v['len'];
		} else {
			if (isset($v['sp'])) {
				if ($l > $m) {
					$data .= ' ';
					$l = 0;
				}
				++$l;
			} else if (!isset($v['len'])) {
				$l = 0;
			} else {
				$l += $v['len'];
			}
			$data .= $v['word'];
		}
	}
}$GLOBALS['__SML_CHR_CHK__'] = array("\n"=>1, "\r"=>1, "\t"=>1, " "=>1, "]"=>1, "["=>1, "<"=>1, ">"=>1, "'"=>1, '"'=>1, "("=>1, ")"=>1, "."=>1, ","=>1, "!"=>1, "?"=>1);

function smiley_to_post($text)
{
	$text_l = strtolower($text);
	include $GLOBALS['FORUM_SETTINGS_PATH'].'sp_cache';

	foreach ($SML_REPL as $k => $v) {
		$a = 0;
		$len = strlen($k);
		while (($a = strpos($text_l, $k, $a)) !== false) {
			if ((!$a || isset($GLOBALS['__SML_CHR_CHK__'][$text_l[$a - 1]])) && ((@$ch = $text_l[$a + $len]) == "" || isset($GLOBALS['__SML_CHR_CHK__'][$ch]))) {
				$text_l = substr_replace($text_l, $v, $a, $len);
				$text = substr_replace($text, $v, $a, $len);
				$a += strlen($v) - $len;
			} else {
				$a += $len;
			}
		}
	}

	return $text;
}

function post_to_smiley($text)
{
	/* include once since draw_post_smiley_cntrl() may use it too */
	include_once $GLOBALS['FORUM_SETTINGS_PATH'].'ps_cache';
	if (isset($PS_SRC)) {
		$GLOBALS['PS_SRC'] = $PS_SRC;
		$GLOBALS['PS_DST'] = $PS_DST;
	} else {
		$PS_SRC = $GLOBALS['PS_SRC'];
		$PS_DST = $GLOBALS['PS_DST'];
	}

	/* check for emoticons */
	foreach ($PS_SRC as $k => $v) {
		if (strpos($text, $v) === false) {
			unset($PS_SRC[$k], $PS_DST[$k]);
		}
	}

	return $PS_SRC ? str_replace($PS_SRC, $PS_DST, $text) : $text;
}function apply_custom_replace($text)
{
	if (!defined('__fud_replace_init')) {
		make_replace_array();
	}
	if (empty($GLOBALS['__FUD_REPL__'])) {
		return $text;
	}

	return preg_replace($GLOBALS['__FUD_REPL__']['pattern'], $GLOBALS['__FUD_REPL__']['replace'], $text);
}

function make_replace_array()
{
	$GLOBALS['__FUD_REPL__']['pattern'] = $GLOBALS['__FUD_REPL__']['replace'] = array();
	$a =& $GLOBALS['__FUD_REPL__']['pattern'];
	$b =& $GLOBALS['__FUD_REPL__']['replace'];

	$c = uq('SELECT with_str, replace_str FROM fud26_replace WHERE replace_str IS NOT NULL AND with_str IS NOT NULL AND LENGTH(replace_str)>0');
	while ($r = db_rowarr($c)) {
		$a[] = $r[1];
		$b[] = $r[0];
	}

	define('__fud_replace_init', 1);
}

function make_reverse_replace_array()
{
	$GLOBALS['__FUD_REPLR__']['pattern'] = $GLOBALS['__FUD_REPLR__']['replace'] = array();
	$a =& $GLOBALS['__FUD_REPLR__']['pattern'];
	$b =& $GLOBALS['__FUD_REPLR__']['replace'];

	$c = uq('SELECT replace_opt, with_str, replace_str, from_post, to_msg FROM fud26_replace');
	while ($r = db_rowarr($c)) {
		if (!$r[0]) {
			$a[] = $r[3];
			$b[] = $r[4];
		} else if ($r[0] && strlen($r[1]) && strlen($r[2])) {
			$a[] = '/'.str_replace('/', '\\/', preg_quote(stripslashes($r[1]))).'/';
			preg_match('/\/(.+)\/(.*)/', $r[2], $regs);
			$b[] = str_replace('\\/', '/', $regs[1]);
		}
	}

	define('__fud_replacer_init', 1);
}

function apply_reverse_replace($text)
{
	if (!defined('__fud_replacer_init')) {
		make_reverse_replace_array();
	}
	if (empty($GLOBALS['__FUD_REPLR__'])) {
		return $text;
	}
	return preg_replace($GLOBALS['__FUD_REPLR__']['pattern'], $GLOBALS['__FUD_REPLR__']['replace'], $text);
}function check_return($returnto)
{
	if ($GLOBALS['FUD_OPT_2'] & 32768 && !empty($_SERVER['PATH_INFO'])) {
		if (!$returnto || !strncmp($returnto, '/er/', 4)) {
			header('Location: http://timeweather.net/forum/index.php/i/'._rsidl);
		} else if ($returnto[0] == '/') { /* unusual situation, path_info & normal themes are active */
			header('Location: http://timeweather.net/forum/index.php'.$returnto);
		} else {
			header('Location: http://timeweather.net/forum/index.php?'.$returnto);
		}
	} else if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: http://timeweather.net/forum/index.php?t=index&'._rsidl);
	} else if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
		header('Location: http://timeweather.net/forum/index.php?'.$returnto.'&S='.s);
	} else {
		header('Location: http://timeweather.net/forum/index.php?'.$returnto);
	}
	exit;
}function validate_email($email)
{
        return !preg_match('!^([-_A-Za-z0-9\.]+)\@([-_A-Za-z0-9\.]+)\.([A-Za-z0-9]{2,4})$!', $email);
}

function encode_subject($text)
{
	if (preg_match('![\x7f-\xff]!', $text)) {
		$text = '=?' . 'ISO-8859-15' . '?B?' . base64_encode($text) . '?=';
	}

	return $text;
}

function send_email($from, $to, $subj, $body, $header='')
{
	if (empty($to)) {
		return;
	}

	if ($GLOBALS['FUD_OPT_1'] & 512) {
		if (!class_exists('fud_smtp')) {
			fud_use('smtp.inc');
		}
		$smtp = new fud_smtp;
		$smtp->msg = str_replace(array('\n', "\n."), array("\n", "\n.."), $body);
		$smtp->subject = encode_subject($subj);
		$smtp->to = $to;
		$smtp->from = $from;
		$smtp->headers = $header;
		$smtp->send_smtp_email();
		return;
	}

	if ($header) {
		$header = "\n" . str_replace("\r", "", $header);
	}
	$header = "From: ".$from."\nErrors-To: ".$from."\nReturn-Path: ".$from."\nX-Mailer: FUDforum v".$GLOBALS['FORUM_VERSION'].$header;

	$body = str_replace(array('\n',"\r"), array("\n",""), $body);
	$subj = encode_subject($subj);
	if (version_compare("4.3.3RC2", phpversion(), ">")) {
		$body = str_replace("\n.", "\n..", $body);
	}

	/* special handling for multibyte languages */
	if (!empty($GLOBALS['usr']->lang) && ($GLOBALS['usr']->lang == 'chinese' || $GLOBALS['usr']->lang == 'japanese') && extension_loaded('mbstring')) {
		if ($GLOBALS['usr']->lang == 'japanese') {
			mb_language('ja');
		} else {
			mb_language('uni');
		}
		mb_internal_encoding('UTF-8');
		$mail_func = 'mb_send_mail';
	} else {
		$mail_func = 'mail';
	}

	foreach ((array)$to as $email) {
		$mail_func($email, $subj, $body, $header);
	}
}include $GLOBALS['FORUM_SETTINGS_PATH'] . 'ip_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'login_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'email_filter_cache';

function is_ip_blocked($ip)
{
	if (empty($GLOBALS['__FUD_IP_FILTER__'])) {
		return;
	}
	$block =& $GLOBALS['__FUD_IP_FILTER__'];
	list($a,$b,$c,$d) = explode('.', $ip);

	if (!isset($block[$a])) {
		return;
	}
	if (isset($block[$a][$b][$c][$d])) {
		return 1;
	}

	if (isset($block[$a][256])) {
		$t = $block[$a][256];
	} else if (isset($block[$a][$b])) {
		$t = $block[$a][$b];
	} else {
		return;
	}

	if (isset($t[$c])) {
		$t = $t[$c];
	} else if (isset($t[256])) {
		$t = $t[256];
	} else {
		return;
	}

	return (isset($t[$d]) || isset($t[256])) ? 1 : null;
}

function is_login_blocked($l)
{
	foreach ($GLOBALS['__FUD_LGN_FILTER__'] as $v) {
		if (preg_match($v, $l)) {
			return 1;
		}
	}
	return;
}

function is_email_blocked($addr)
{
	if (empty($GLOBALS['__FUD_EMAIL_FILTER__'])) {
		return;
	}
	$addr = strtolower($addr);
	foreach ($GLOBALS['__FUD_EMAIL_FILTER__'] as $k => $v) {
		if (($v && (strpos($addr, $k) !== false)) || (!$v && preg_match($k, $addr))) {
			return 1;
		}
	}
	return;
}

function is_allowed_user(&$usr)
{
	if ($GLOBALS['FUD_OPT_1'] & 1048576 && $usr->users_opt & 262144) {
		error_dialog('ERROR: Your account is not yet confirmed', 'We have not received a confirmation from your parent and/or legal guardian, which would allow you to post messages. If you lost your COPPA form, <a href="index.php?t=coppa_fax&amp;'._rsid.'">click here</a> to see it again.');
	}

	if ($GLOBALS['FUD_OPT_2'] & 1 && !($usr->users_opt & 131072)) {
		std_error('emailconf');
	}

	if ($GLOBALS['FUD_OPT_2'] & 1024 && $usr->users_opt & 2097152) {
		error_dialog('Unverified Account', 'The administrator had chosen to review all accounts manually prior to activation. Until your account has been validated by the administrator you will not be able to utilize the full capabilities of your account.');
	}

	if ($usr->users_opt & 65536 || is_email_blocked($usr->email) || is_login_blocked($usr->login) || is_ip_blocked(get_ip())) {
		ses_delete($usr->sid);
		$usr = ses_anon_make();
		setcookie($GLOBALS['COOKIE_NAME'].'1', 'd34db33fd34db33fd34db33fd34db33f', __request_timestamp__+63072000, $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);
		error_dialog('ERROR: you are not allowed to post', 'Your account has been blocked from posting');
	}
}function register_fp($id)
{
	if (!isset($GLOBALS['__MSG_FP__'][$id])) {
		$GLOBALS['__MSG_FP__'][$id] = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$id, 'rb');
	}

	return $GLOBALS['__MSG_FP__'][$id];
}

function read_msg_body($off, $len, $file_id)
{
	if (!$len) {
		return;
	}

	$fp = register_fp($file_id);
	fseek($fp, $off);
	return fread($fp, $len);
}class fud_smtp
{
	var $fs, $last_ret, $msg, $subject, $to, $from, $headers;

	function get_return_code($cmp_code='250')
	{
		if (!($this->last_ret = fgets($this->fs, 1024))) {
			return;
		}
		if (substr($this->last_ret, 0, 3) == $cmp_code) {
			return 1;
		}

		return;
	}

	function wts($string)
	{
		fwrite($this->fs, $string . "\r\n");
	}

	function open_smtp_connex()
	{
		if( !($this->fs = fsockopen($GLOBALS['FUD_SMTP_SERVER'], 25, $errno, $errstr, $GLOBALS['FUD_SMTP_TIMEOUT'])) ) {
			exit("ERROR: stmp server at ".$GLOBALS['FUD_SMTP_SERVER']." is not available<br>\nAdditional Problem Info: $errno -> $errstr <br>\n");
		}
		if (!$this->get_return_code(220)) {
			return;
		}
		$this->wts("HELO ".$GLOBALS['FUD_SMTP_SERVER']);
		if (!$this->get_return_code()) {
			return;
		}

		/* Do SMTP Auth if needed */
		if ($GLOBALS['FUD_SMTP_LOGIN']) {
			$this->wts('AUTH LOGIN');
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_LOGIN']));
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_PASS']));
			if (!$this->get_return_code(235)) {
				return;
			}
		}

		return 1;
	}

	function send_from_hdr()
	{
		$this->wts('MAIL FROM: <'.$GLOBALS['NOTIFY_FROM'].'>');
		return $this->get_return_code();
	}

	function send_to_hdr()
	{
		$this->to = (array) $this->to;

		foreach ($this->to as $to_addr) {
			$this->wts('RCPT TO: <'.$to_addr.'>');
			if (!$this->get_return_code()) {
				return;
			}
		}
		return 1;
	}

	function send_data()
	{
		$this->wts('DATA');
		if (!$this->get_return_code(354)) {
			return;
		}

		/* This is done to ensure what we comply with RFC requiring each line to end with \r\n */
		$this->msg = preg_replace("!(\r)?\n!si", "\r\n", $this->msg);

		if( empty($this->from) ) $this->from = $GLOBALS['NOTIFY_FROM'];

		$this->wts('Subject: '.$this->subject);
		$this->wts('Date: '.date("r"));
		$this->wts('To: '.(count($this->to) == 1 ? $this->to[0] : $GLOBALS['NOTIFY_FROM']));
		$this->wts('From: '.$this->from);
		$this->wts('X-Mailer: FUDforum v'.$GLOBALS['FORUM_VERSION']);
		$this->wts($this->headers."\r\n");
		$this->wts($this->msg);
		$this->wts('.');

		return $this->get_return_code();
	}

	function close_connex()
	{
		$this->wts('quit');
		fclose($this->fs);
	}

	function send_smtp_email()
	{
		if (!$this->open_smtp_connex()) {
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_from_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_to_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_data()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}

		$this->close_connex();
	}
}

function generate_turing_val(&$rt)
{
	$t = array(
		array('....###....','.########..','..######..','.########.','.########.','..######...','.##.....##.','.####.','.......##.','.##....##.','.##.......','.##.....##.','.##....##.','.########..','..#######..','.########..','..######..','.########.','.##.....##.','.##.....##.','.##......##.','.##.....##.','.##....##.','.########.'),
		array('...##.##...','.##.....##.','.##....##.','.##.......','.##.......','.##....##..','.##.....##.','..##..','.......##.','.##...##..','.##.......','.###...###.','.###...##.','.##.....##.','.##.....##.','.##.....##.','.##....##.','....##....','.##.....##.','.##.....##.','.##..##..##.','..##...##..','..##..##..','......##..'),
		array('..##...##..','.##.....##.','.##.......','.##.......','.##.......','.##........','.##.....##.','..##..','.......##.','.##..##...','.##.......','.####.####.','.####..##.','.##.....##.','.##.....##.','.##.....##.','.##.......','....##....','.##.....##.','.##.....##.','.##..##..##.','...##.##...','...####...','.....##...'),
		array('.##.....##.','.########..','.##.......','.######...','.######...','.##...####.','.#########.','..##..','.......##.','.#####....','.##.......','.##.###.##.','.##.##.##.','.########..','.##.....##.','.########..','..######..','....##....','.##.....##.','.##.....##.','.##..##..##.','....###....','....##....','....##....'),
		array('.#########.','.##.....##.','.##.......','.##.......','.##.......','.##....##..','.##.....##.','..##..','.##....##.','.##..##...','.##.......','.##.....##.','.##..####.','.##........','.##..##.##.','.##...##...','.......##.','....##....','.##.....##.','..##...##..','.##..##..##.','...##.##...','....##....','...##.....'),
		array('.##.....##.','.##.....##.','.##....##.','.##.......','.##.......','.##....##..','.##.....##.','..##..','.##....##.','.##...##..','.##.......','.##.....##.','.##...###.','.##........','.##....##..','.##....##..','.##....##.','....##....','.##.....##.','...##.##...','.##..##..##.','..##...##..','....##....','..##......'),
		array('.##.....##.','.########..','..######..','.########.','.##.......','..######...','.##.....##.','.####.','..######..','.##....##.','.########.','.##.....##.','.##....##.','.##........','..#####.##.','.##.....##.','..######..','....##....','..#######..','....###....','..###..###..','.##.....##.','....##....','.########.')
	);

	$text = $rt = '';
	$rv = array();

	// pick 4 random letters
	for ($i = 0; $i < 4; $i++) {
		$rv[] = $v = mt_rand(0, 23);
		if ($v >= 11) {
			$v += 2;
		} else if ($v >= 3) {
			$v += 1;
		}
		$rt .= chr(65 + $v); // upper case real letter
	}

	$rt = md5($rt); // generate turing hash

	// generate turing text
	for ($i = 0; $i < 7; $i++) {
		foreach ($rv as $v) {
			$text .= $t[$i][$v];	
		}
		$text .= "<br />";
	}

	return $text;
}

function fetch_img($url, $user_id)
{
	$ext = array(1=>'gif', 2=>'jpg', 3=>'png', 4=>'swf');
	list($max_w, $max_y) = explode('x', $GLOBALS['CUSTOM_AVATAR_MAX_DIM']);
	if (!($img_info = @getimagesize($url)) || $img_info[0] > $max_w || $img_info[1] > $max_y || $img_info[2] > ($GLOBALS['FUD_OPT_1'] & 64 ? 4 : 3)) {
		return;
	}
	if (!($img_data = file_get_contents($url))) {
		return;
	}
	$name = $user_id . '.' . $ext[$img_info[2]]. '_';

	while (($fp = fopen(($path = tempnam($GLOBALS['TMP'], $name)), 'ab'))) {
		if (!ftell($fp)) { /* make sure that the temporary file picked, did not exist before, yes, this is paranoid. */
			break;
		}
	}
	fwrite($fp, $img_data);
	fclose($fp);

	return $path;
}
	/* intialize error status */
	$GLOBALS['error'] = 0;
	$GLOBALS['err_msg'] = array();

function sanitize_url($url)
{
	if (!$url) {
		return '';
	}

	if (strncasecmp($url, 'http://', strlen('http://')) && strncasecmp($url, 'https://', strlen('https://')) && strncasecmp($url, 'ftp://', strlen('ftp://'))) {
		if (stristr($url, 'javascript:')) {
			return '';
		} else {
			return 'http://' . $url;
		}
	}
	return $url;
}

function sanitize_login($login, $is_alias=0)
{
	$list = '';

	for ($i = 0; $i < 32; $i++) $list .= chr($i);
	if (!$is_alias) {
		for ($i = 127; $i < 160; $i++) $list .= chr($i);
	}
	return str_replace("\0", "", strtr($login, $list, str_repeat("\0", strlen($list))));
}

function register_form_check($user_id)
{
	/* new user specific checks */
	if (!$user_id) {
		if (($reg_limit_reached = $GLOBALS['REG_TIME_LIMIT'] + q_singleval('SELECT join_date FROM fud26_users WHERE id='.q_singleval('SELECT MAX(id) FROM fud26_users')) - __request_timestamp__) > 0) {
			set_err('reg_time_limit', '<tr class="RowStyleA"><td class="ac ErrorText" colspan="2">The limit of one registration per '.$GLOBALS['REG_TIME_LIMIT'].' seconds has been reached. Please wait '.$reg_limit_reached.' second(s), and then try to register once again.</td></tr>');
		}

		$_POST['reg_plaintext_passwd'] = trim($_POST['reg_plaintext_passwd']);

		if (strlen($_POST['reg_plaintext_passwd']) < 6) {
			set_err('reg_plaintext_passwd', 'Passwords must be at least 6 characters long.');
		}

		$_POST['reg_plaintext_passwd_conf'] = trim($_POST['reg_plaintext_passwd_conf']);

		if ($_POST['reg_plaintext_passwd'] !== $_POST['reg_plaintext_passwd_conf']) {
			set_err('reg_plaintext_passwd', 'Your passwords do not match, please try again.');
		}

		$_POST['reg_login'] = trim(sanitize_login($_POST['reg_login']));

		if (strlen($_POST['reg_login']) < 4) {
			set_err('reg_login', 'The Login you have selected is too short. Login names must be at least 4 characters long.');
		} else if (is_login_blocked($_POST['reg_login'])) {
			set_err('reg_login', 'This login name is not allowed.');
		} else if (get_id_by_login($_POST['reg_login'])) {
			set_err('reg_login', 'Forum login names must be unique. There is already a user with this name.');
		}

		if (!($GLOBALS['FUD_OPT_3'] & 128) && (empty($_POST['turing_test']) || empty($_POST['turing_res']) || md5(strtoupper(trim($_POST['turing_test']))) != $_POST['turing_res'])) {
			set_err('reg_turing', 'Invalid validation code.');
		}

		$_POST['reg_email'] = trim($_POST['reg_email']);

		/* E-mail validity check */
		if (validate_email($_POST['reg_email'])) {
			set_err('reg_email', 'The e-mail address you have specified does not appear to be valid.');
		} else if (get_id_by_email($_POST['reg_email'])) {
			set_err('reg_email', 'This e-mail address is already associated with an account. If you have forgotten your password please use the Restore option, instead of re-registering.');
		} else if (is_email_blocked($_POST['reg_email'])) {
			set_err('reg_email', 'This e-mail address is already associated with an account. If you have forgotten your password please use the Restore option, instead of re-registering.');
		}
	} else {
		if (empty($_POST['reg_confirm_passwd']) || !q_singleval("SELECT login FROM fud26_users WHERE id=".(!empty($_POST['mod_id']) ? __fud_real_user__ : $user_id)." AND passwd='".md5($_POST['reg_confirm_passwd'])."'")) {
			if (!empty($_POST['mod_id'])) {
				set_err('reg_confirm_passwd', 'You must enter your ADMINISTRATOR password to complete the changes.');
			} else {
				set_err('reg_confirm_passwd', 'You must enter your current password to complete the changes.');
			}
		}

		/* E-mail validity check */
		if (validate_email($_POST['reg_email'])) {
			set_err('reg_email', 'The e-mail address you have specified does not appear to be valid.');
		} else if (($email_id = get_id_by_email($_POST['reg_email'])) && $email_id != $user_id) {
			set_err('reg_email', 'Someone else is already registered with this e-mail address.');
		}
	}

	$_POST['reg_name'] = trim($_POST['reg_name']);
	$_POST['reg_home_page'] = sanitize_url(trim($_POST['reg_home_page']));
	$_POST['reg_user_image'] = !empty($_POST['reg_user_image']) ? sanitize_url(trim($_POST['reg_user_image'])) : '';

	if (!empty($_POST['reg_icq']) && !(int)$_POST['reg_icq']) { /* ICQ # can only be an integer */
		$_POST['reg_icq'] = '';
	}

	/* User's name or nick name */
	if (strlen($_POST['reg_name']) < 2) {
		set_err('reg_name', 'To successfully complete this registration you must enter your name.');
	}

	/* Image count check */
	if ($GLOBALS['FORUM_IMG_CNT_SIG'] && $GLOBALS['FORUM_IMG_CNT_SIG'] < substr_count(strtolower($_POST['reg_sig']), '[img]') ) {
		set_err('reg_sig', 'You are trying to use more than the allowed '.$GLOBALS['FORUM_IMG_CNT_SIG'].' images in your signature.');
	}

	/* Url Avatar check */
	if (!empty($_POST['reg_avatar_loc']) && !($GLOBALS['reg_avatar_loc_file'] = fetch_img($_POST['reg_avatar_loc'], $user_id))) {
		set_err('avatar', 'The specified url does not contain a valid image.');
	}

	/* Alias Check */
	if ($GLOBALS['FUD_OPT_2'] & 128 && isset($_POST['reg_alias'])) {
		if (($_POST['reg_alias'] = trim(sanitize_login($_POST['reg_alias'], true)))) {
			if (is_login_blocked($_POST['reg_alias'])) {
				set_err('reg_alias', 'This alias is not allowed');
			}
			if (q_singleval("SELECT id FROM fud26_users WHERE alias='".addslashes(make_alias($_POST['reg_alias']))."' AND id!=".$user_id)) {
				set_err('reg_alias', 'The alias you are trying to use is already in use by another forum member. Please choose another.');
			}
		}
	}

	if ($GLOBALS['FORUM_SIG_ML'] && strlen($_POST['reg_sig']) > $GLOBALS['FORUM_SIG_ML']) {
		set_err('reg_sig', 'Your signature exceeds the maximum allowed length of '.$GLOBALS['FORUM_SIG_ML'].' characters characters.');
	}

	return $GLOBALS['error'];
}

function fmt_year($val)
{
	if (!($val = (int)$val)) {
		return;
	}
	if ($val > 1000) {
		return $val;
	} else if ($val < 100 && $val > 10) {
		return (1900 + $val);
	} else if ($val < 10) {
		return (2000 + $val);
	}
}

function set_err($err_name, $err_msg)
{
	$GLOBALS['error'] = 1;
	if (isset($GLOBALS['err_msg'])) {
		$GLOBALS['err_msg'][$err_name] = $err_msg;
	} else {
		$GLOBALS['err_msg'] = array($err_name => $err_msg);
	}
}

function draw_err($err_name)
{
	if (!isset($GLOBALS['err_msg'][$err_name])) {
		return;
	}
	return '<br /><span class="ErrorText">'.$GLOBALS['err_msg'][$err_name].'</span>';
}

function make_avatar_loc($path, $disk, $web)
{
	$img_info = @getimagesize($disk . $path);

	if ($img_info[2] < 4 && $img_info[2] > 0) {
		return '<img src="'.$web . $path.'" '.$img_info[3].' />';
	} else if ($img_info[2] == 4) {
		return '<embed src="'.$web . $path.'" '.$img_info[3].' />';
	} else {
		return '';
	}
}

function remove_old_avatar($avatar_str)
{
	if (preg_match('!images/custom_avatars/(([0-9]+)\.([A-Za-z]+))" width=!', $avatar_str, $tmp)) {
		@unlink($GLOBALS['WWW_ROOT_DISK'] . 'images/custom_avatars/' . basename($tmp[1]));
	}
}

function decode_uent(&$uent)
{
	$uent->home_page = reverse_fmt($uent->home_page);
	$uent->user_image = reverse_fmt($uent->user_image);
	$uent->jabber = reverse_fmt($uent->jabber);
	$uent->aim = urldecode($uent->aim);
	$uent->yahoo = urldecode($uent->yahoo);
	$uent->msnm = urldecode($uent->msnm);
	$uent->affero = urldecode($uent->affero);
}

	if (!__fud_real_user__ && !($FUD_OPT_1 & 2)) {
		std_error('registration_disabled');
	}

	if (!__fud_real_user__ && !isset($_POST['reg_coppa']) && !isset($_GET['reg_coppa'])) {
		if ($FUD_OPT_1 & 1048576) {
			if ($FUD_OPT_2 & 32768) {
				header('Location: http://timeweather.net/forum/index.php/cp/'._rsidl);
			} else {
				header('Location: http://timeweather.net/forum/index.php?t=coppa&'._rsidl);
			}
		} else {
			if ($FUD_OPT_2 & 32768) {
				header('Location: http://timeweather.net/forum/index.php/pr/0/'._rsidl);
			} else {
				header('Location: http://timeweather.net/forum/index.php?t=pre_reg&'._rsidl);
			}
		}
		exit;
	}

	if (isset($_GET['mod_id'])) {
		$mod_id = (int)$_GET['mod_id'];
	} else if (isset($_POST['mod_id'])) {
		$mod_id = (int)$_POST['mod_id'];
	} else {
		$mod_id = '';
	}

	if (isset($_GET['reg_coppa'])) {
		$reg_coppa = (int)$_GET['reg_coppa'];
	} else if (isset($_POST['mod_id'])) {
		$reg_coppa = (int)$_POST['reg_coppa'];
	} else {
		$reg_coppa = '';
	}

	/* ip filter */
	if (is_ip_blocked(get_ip())) {
		invl_inp_err();
	}

	/* allow the root to modify settings other lusers */
	if (_uid && $is_a && $mod_id) {
		if (!($uent =& usr_reg_get_full($mod_id))) {
			exit('Invalid User Id');
		}
		decode_uent($uent);
	} else {
		if (__fud_real_user__) {
			$uent =& usr_reg_get_full($usr->id);
			decode_uent($uent);
		} else {
			$uent = new fud_user_reg;
			$uent->id = 0;
			$uent->users_opt = 4488183;
		}
	}

	$reg_avatar_loc_file = $avatar_tmp = $avatar_arr = null;
	/* deal with avatars, only done for regged users */
	if (_uid) {
		if (!empty($_POST['avatar_tmp'])) {
			list($avatar_arr['file'], $avatar_arr['del'], $avatar_arr['leave']) = explode("\n", base64_decode($_POST['avatar_tmp']));
		}
		if (isset($_POST['btn_detach'], $avatar_arr)) {
			$avatar_arr['del'] = 1;
		}
		if (!($FUD_OPT_1 & 8) && (!@file_exists($avatar_arr['file']) || empty($avatar_arr['leave']))) {
			/* hack attempt for URL avatar */
			$avatar_arr = null;
		} else if (($FUD_OPT_1 & 8) && isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['size'] > 0) { /* new upload */
			if ($_FILES['avatar_upload']['size'] >= $CUSTOM_AVATAR_MAX_SIZE) {
				set_err('avatar', 'The file you are trying to upload is too big, more than '.$GLOBALS['CUSTOM_AVATAR_MAX_SIZE'].' bytes');
			} else {
				/* [user_id].[file_extension]_'random data' */
				$file_name = $uent->id . strrchr($_FILES['avatar_upload']['name'], '.') . '_';
				$tmp_name = safe_tmp_copy($_FILES['avatar_upload']['tmp_name'], 0, $file_name);

				if (!($img_info = @getimagesize($TMP . $tmp_name))) {
					set_err('avatar', 'The specified url does not contain a valid image.');
					unlink($TMP . $tmp_name);
				}

				list($max_w, $max_y) = explode('x', $CUSTOM_AVATAR_MAX_DIM);
				if ($img_info[2] > ($FUD_OPT_1 & 64 ? 4 : 3)) {
					set_err('avatar', 'The avatar you are trying to upload is not allowed. Check the allowed file types.');
					unlink($TMP . $tmp_name);
				} else if ($img_info[0] >$max_w || $img_info[1] >$max_y) {
					set_err('avatar', 'Avatar dimensions of <b>('.$img_info[0].'x'.$img_info[1].')</b> exceed the allowed dimensions of <b>('.$GLOBALS['CUSTOM_AVATAR_MAX_DIM'].')</b> pixels.');
					unlink($TMP . $tmp_name);
				} else {
					/* remove old uploaded file, if one exists & is not in DB */
					if (empty($avatar_arr['leave']) && @file_exists($avatar_arr['file'])) {
						@unlink($TMP . $avatar_arr['file']);
					}

					$avatar_arr['file'] = $tmp_name;
					$avatar_arr['del'] = 0;
					$avatar_arr['leave'] = 0;
				}
			}
		}
	}

	if ($GLOBALS['is_post']) {
		$new_users_opt = 0;
		foreach (array('display_email', 'notify', 'notify_method', 'ignore_admin', 'email_messages', 'pm_messages', 'pm_notify', 'default_view', 'gender', 'append_sig', 'show_sigs', 'show_avatars', 'show_im', 'invisible_mode') as $v) {
			if (!empty($_POST['reg_'.$v])) {
				$new_users_opt |= (int) $_POST['reg_'.$v];
			}
		}

		/* security check, prevent haxors from passing values that shouldn't */
		if (!($new_users_opt & (131072|65536|262144|524288|1048576|2097152|4194304|8388608|16777216|33554432|67108864))) {
			$uent->users_opt = ($uent->users_opt & (131072|65536|262144|524288|1048576|2097152|4194304|8388608|16777216|33554432|67108864)) | $new_users_opt;
		}
	}

	/* SUBMITTION CODE */
	if (isset($_POST['fud_submit']) && !isset($_POST['btn_detach']) && !isset($_POST['btn_upload']) && !register_form_check($uent->id)) {
		$old_email = $uent->email;
		$old_avatar_loc = $uent->avatar_loc;
		$old_avatar = $uent->avatar;

		/* import data from _POST into $uent object */
		foreach (array_keys(get_class_vars("fud_user")) as $v) {
			if (isset($_POST['reg_'.$v])) {
				$uent->{$v} = $_POST['reg_'.$v];
			}
		}

		/* only one theme avaliable, so no select */
		if (!$uent->theme) {
			$uent->theme = q_singleval("SELECT id FROM fud26_themes WHERE theme_opt>=2 AND (theme_opt & 2) > 0 LIMIT 1");
		}

		$uent->bday = fmt_year($_POST['b_year']) . str_pad((int)$_POST['b_month'], 2, '0', STR_PAD_LEFT) . str_pad((int)$_POST['b_day'], 2, '0', STR_PAD_LEFT);
		$uent->sig = apply_custom_replace($uent->sig);
		if ($FUD_OPT_1 & 131072) {
			$uent->sig = tags_to_html($uent->sig, $FUD_OPT_1 & 524288);
		} else if ($FUD_OPT_1 & 65536) {
			$uent->sig = nl2br(htmlspecialchars($uent->sig));
		}

		if ($FUD_OPT_1 & 196608) {
			$uent->sig = char_fix($uent->sig);
		}

		if ($FUD_OPT_1 & 262144) {
			$uent->sig = smiley_to_post($uent->sig);
		}
		fud_wordwrap($uent->sig);

		if (!__fud_real_user__) { /* new user */
			/* new users do not have avatars */
			$uent->users_opt |= 4194304;

			/* handle coppa passed to us by pre_reg form */
			if (!(int)$_POST['reg_coppa']) {
				$uent->users_opt ^= 262144;
			}

			/* make the account un-validated, if admin wants to approve accounts manually */
			if ($FUD_OPT_2 & 1024) {
				$uent->users_opt |= 2097152;
			}

			$uent->add_user();

			if ($FUD_OPT_2 & 1) {
				send_email($NOTIFY_FROM, $uent->email, 'Registration Confirmation', 'Thank you for registering,\nTo activate your account please go to the URL below:\n\nhttp://timeweather.net/forum/index.php?t=emailconf&conf_key='.$uent->conf_key.'\n\nOnce your account is activated you will be logged-into the forum and\nredirected to the main page.', '');
			} else {
				send_email($NOTIFY_FROM, $uent->email, 'Forum Registration Confirmation', 'Thank you for registering,\n\nHere are your login details for the forum:\n\nForum URL: index.php?t=index\nLogin: '.$uent->login.'\nPassword: '.$_POST['reg_plaintext_passwd'].'\n\nPlease note that passwords are case sensitive!\nTo modify your settings or profile, go to this page:\nhttp://timeweather.net/forum/index.php?t=register\n', '');
			}

			/* we notify all admins about the new user, so that they can approve him */
			if (($FUD_OPT_2 & 132096) == 132096) {
				$admins = array();
				$c = uq("SELECT email FROM fud26_users WHERE users_opt>=1048576 AND (users_opt & 1048576) > 0");
				while ($r = db_rowarr($c)) {
					$admins[] = $r[0];
				}
				send_email($NOTIFY_FROM, $admins, 'A new user has registered and their account is pending confirmation', 'A new user ('.$uent->login.') has just registered, and since account confirmation is enabled, their account will not become active until it is confirmed by you or another forum administrator. To review the account please go to: '.$GLOBALS['WWW_ROOT'].'adm/admaccapr.php\n\nThis is an automated process. Do not reply to this message.\nIf you want to turn off future e-mail notifications of new user registrations, you can do so via the Admin Control Panel. Change the "New Account Notification" setting.', '');
			}

			/* login the new user into the forum */
			user_login($uent->id, $usr->ses_id, 1);

			if ($FUD_OPT_1 & 1048576 && $uent->users_opt & 262144) {
				if ($FUD_OPT_2 & 32768) {
					header('Location: http://timeweather.net/forum/index.php/cpf/'._rsidl);
				} else {
					header('Location: http://timeweather.net/forum/index.php?t=coppa_fax&'._rsidl);
				}
				exit;
			} else if (!($uent->users_opt & 131072) || $FUD_OPT_2 & 1024) {
				header('Location: http://timeweather.net/forum/index.php' . ($FUD_OPT_2 & 32768 ? '/rc/' : '?t=reg_conf&') . _rsidl);
				exit;
			}

			check_return($usr->returnto);
		} else if ($uent->id) { /* updating a user */
			/* Restore avatar values to their previous values */
			$uent->avatar = $old_avatar;
			$uent->avatar_loc = $old_avatar_loc;
			$old_opt = $uent->users_opt & (4194304|16777216|8388608);
			$uent->users_opt |= 4194304|16777216|8388608;

			/* prevent non-confirmed users from playing with avatars, yes we are that cruel */
			if ($FUD_OPT_1 & 28 && _uid) {
				if ($_POST['avatar_type'] == 'b') { /* built-in avatar */
					if (!$old_avatar && $old_avatar_loc) {
						remove_old_avatar($old_avatar_loc);
						$uent->avatar_loc = '';
					} else if (isset($avatar_arr['file'])) {
						@unlink($TMP . basename($avatar_arr['file']));
					}
					if ($_POST['reg_avatar'] == '0') {
						$uent->avatar_loc = '';
						$uent->avatar = 0;
					} else if ($uent->avatar != $_POST['reg_avatar'] && ($img = q_singleval('SELECT img FROM fud26_avatar WHERE id='.(int)$_POST['reg_avatar']))) {
						/* verify that the avatar exists and it is different from the one in DB */
						$uent->avatar_loc = make_avatar_loc('images/avatars/' . $img, $WWW_ROOT_DISK, $WWW_ROOT);
						$uent->avatar = $_POST['reg_avatar'];
					}
					if ($uent->avatar && $uent->avatar_loc) {
						$uent->users_opt ^= 4194304|16777216;
					}
				} else {
					if ($_POST['avatar_type'] == 'c' && $reg_avatar_loc_file) { /* New URL avatar */
						$common_av_name = $reg_avatar_loc_file;

						if (!empty($avatar_arr['file'])) {
							$avatar_arr['del'] = 1;
						}
					} else if ($_POST['avatar_type'] == 'u' && empty($avatar_arr['del']) && empty($avatar_arr['leave'])) { /* uploaded file */
						$common_av_name = $avatar_arr['file'];
					} else {
						$common_av_name = '';
					}

					/* remove old avatar if need be */
					if (!empty($avatar_arr['del'])) {
						if (empty($avatar_arr['leave'])) {
							@unlink($TMP . basename($avatar_arr['file']));
						} else {
							remove_old_avatar($old_avatar_loc);
						}
					}

					/* add new avatar if needed */
					if ($common_av_name) {
						$common_av_name = basename($common_av_name);
						$av_path = 'images/custom_avatars/' . substr($common_av_name, 0, strpos($common_av_name, '_'));
						copy($TMP . basename($common_av_name), $WWW_ROOT_DISK . $av_path);
						@unlink($TMP . basename($common_av_name));
						if (($uent->avatar_loc = make_avatar_loc($av_path, $WWW_ROOT_DISK, $WWW_ROOT))) {
						 	if (!($FUD_OPT_1 & 32) || $uent->users_opt & 1048576) {
						 		$uent->users_opt ^= 16777216|4194304;
						 	} else {
						 		$uent->users_opt ^= 8388608|4194304;
					 		}
					 	}
					} else if (empty($avatar_arr['leave']) || !empty($avatar_arr['del'])) {
				 		$uent->avatar_loc = '';
				 	} else if (!empty($avatar_arr['leave'])) {
				 		$uent->users_opt ^= (8388608|16777216|4194304) ^ $old_opt;
				 	}
				 	$uent->avatar = 0;
				}
				if (empty($uent->avatar_loc)) {
					$uent->users_opt ^= 8388608|16777216;
				}
			} else {
				$uent->users_opt ^= (8388608|16777216|4194304) ^ $old_opt;
			}

			$uent->sync_user();

			/* if the user had changed their e-mail, force them re-confirm their account (unless admin) */
			if ($FUD_OPT_2 & 1 && $old_email && $old_email != $uent->email && !($uent->users_opt & 1048576)) {
				$conf_key = usr_email_unconfirm($uent->id);
				send_email($NOTIFY_FROM, $uent->email, 'E-mail change confirmation', 'Please confirm your new e-mail account "'.$uent->email.'" that replaces your old e-mail account "'.$old_email.'", by going to the URL below:\nhttp://timeweather.net/forum/index.php?t=emailconf&conf_key='.$conf_key.'\n\nOnce you confirm your new e-mail address, your forum account will be re-activated.', '');
			}
			if (!$mod_id) {
				check_return($usr->returnto);
			} else {
				if ($FUD_OPT_2 & 32768) {
					header('Location: http://timeweather.net/forum/adm/admuser.php?usr_id='.$uent->id.'&'.str_replace(array(s, '/?'), array('S='.s, '&'),_rsidl).'&act=nada');
				} else {
					header('Location: http://timeweather.net/forum/adm/admuser.php?usr_id='.$uent->id.'&'._rsidl.'&act=nada');
				}
				exit;
			}
		} else {
			error_dialog('ERROR: unable to register', 'Unable to create user account, please contact the administrator at <a href="mailto:'.$ADMIN_EMAIL.'">'.$ADMIN_EMAIL.'</a>');
		}
	}

	$avatar_type = '';
	$chr_fix = array('reg_sig', 'reg_name', 'reg_bio', 'reg_location', 'reg_occupation', 'reg_interests'); 
	if ($FUD_OPT_2 & 128) {
		$chr_fix[] = 'reg_alias';
	}
	if (!__fud_real_user__) {
		$chr_fix[] = 'reg_login';
	} else {
		$reg_login = char_fix(htmlspecialchars($uent->login));
	}

	/* populate form variables based on user's profile */
	if (__fud_real_user__ && !isset($_POST['prev_loaded'])) {
		foreach ($uent as $k => $v) {
			${'reg_'.$k} = htmlspecialchars($v);
		}
		foreach($chr_fix as $v) {
			$$v = char_fix(reverse_fmt($$v));
		}

		$reg_sig = apply_reverse_replace($reg_sig);

		if ($FUD_OPT_1 & 262144) {
			$reg_sig = post_to_smiley($reg_sig);
		}

		if ($FUD_OPT_1 & 131072) {
			$reg_sig = html_to_tags($reg_sig);
		} else if ($FUD_OPT_1 & 65536) {
			$reg_sig = reverse_nl2br($reg_sig);
		}

		if ($FUD_OPT_1 & 196608) {
			$reg_sig = char_fix($reg_sig);
		}

		if ($uent->bday) {
			$b_year = substr($uent->bday, 0, 4);
			$b_month = substr($uent->bday, 4, 2);
			$b_day = substr($uent->bday, 6, 8);
		} else {
			$b_year = $b_month = $b_day = '';
		}
		if (!$reg_avatar && $reg_avatar_loc) { /* custom avatar */
			if (preg_match('!src="([^"]+)" width="!', reverse_fmt($reg_avatar_loc), $tmp)) {
				$avatar_arr['file'] = $tmp[1];
				$avatar_arr['del'] = 0;
				$avatar_arr['leave'] = 1;
				$avatar_type = 'u';
			}
		}
	} else if (isset($_POST['prev_loaded'])) { /* import data from POST data */
		foreach ($_POST as $k => $v) {
			if (!strncmp($k, 'reg_', 4)) {
				${$k} = htmlspecialchars($v);
			}
		}
		foreach($chr_fix as $v) {
			$$v = char_fix($$v);
		}

		$b_year = $_POST['b_year'];
		$b_month = $_POST['b_month'];
		$b_day = $_POST['b_day'];
		if (isset($_POST['avatar_type'])) {
			$avatar_type = $_POST['avatar_type'];
		}
	}

	/* When we need to create a new user, define default values for various options */
	if (!__fud_real_user__ && !isset($_POST['prev_loaded'])) {
		foreach (array_keys(get_object_vars($uent)) as $v) {
			 ${'reg_'.$v} = '';
		}

		$uent->users_opt = 4488182;
		if (!($FUD_OPT_2 & 4)) {
			$uent->users_opt ^= 128;
		}
		if (!($FUD_OPT_2 & 8)) {
			$uent->users_opt ^= 256;
		}

		$b_year = $b_month = $b_day = '';
		$reg_time_zone = $SERVER_TZ;
	}

	if (!$mod_id) {
		if (__fud_real_user__) {
			ses_update_status($usr->sid, 'Viewing own profile', 0, 0);
		} else {
			ses_update_status($usr->sid, 'Registration Page', 0, 0);
		}
	}

	$TITLE_EXTRA = ': Register Form';

if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}$tabs = '';
if (_uid) {
	$tablist = array(
'User CP'=>'uc',
'Settings'=>'register',
'Subscriptions'=>'subscribed',
'Referrals'=>'referals',
'Buddy List'=>'buddy_list',
'Ignore List'=>'ignore_list');

	if (!($FUD_OPT_2 & 8192)) {
		unset($tablist['Referrals']);
	}

	if (isset($_POST['mod_id'])) {
		$mod_id_chk = $_POST['mod_id'];
	} else if (isset($_GET['mod_id'])) {
		$mod_id_chk = $_GET['mod_id'];
	} else {
		$mod_id_chk = null;
	}

	if (!$mod_id_chk) {
		if ($FUD_OPT_1 & 1024) {
			$tablist['Private Messaging'] = 'pmsg';
		}
		$pg = ($_GET['t'] == 'pmsg_view' || $_GET['t'] == 'ppost') ? 'pmsg' : $_GET['t'];

		foreach($tablist as $tab_name => $tab) {
			$tab_url = 'index.php?t='.$tab.'&amp;S='.s;
			if ($tab == 'referals') {
				if (!($FUD_OPT_2 & 8192)) {
					continue;
				}
				$tab_url .= '&amp;id='._uid;
			}
			$tabs .= $pg == $tab ? '<td class="tabON"><div class="tabT"><a class="tabON" href="'.$tab_url.'">'.$tab_name.'</a></div></td>' : '<td class="tabI"><div class="tabT"><a href="'.$tab_url.'">'.$tab_name.'</a></div></td>';
		}

		$tabs = '<table cellspacing=1 cellpadding=0 class="tab">
<tr>'.$tabs.'</tr>
</table>';
	}
}

	if ($FUD_OPT_2 & 2048) {
		$affero_domain = parse_url($WWW_ROOT);
		$register_affero = '<tr class="RowStyleA"><td>Affero Username:<br /><span class="SmallText">If you have an <a href="http://www.affero.com/ca/'.urlencode($affero_domain['host']).'" target="_blank">Affero</a> username, enter it here.</span></td><td><input type="text" name="reg_affero" value="'.$reg_affero.'" maxLength=32 size=25></td></tr>';
	} else {
		$register_affero = '';
	}

	/* Initialize avatar options */
	$avatar = $avatar_type_sel = '';

	if (__fud_real_user__) {
		if ($uent->users_opt & 131072 && $FUD_OPT_2 & 1 && !($uent->users_opt & 1048576)) {
			$email_warning_msg = '<br /><span class="regEW">If you change your current e-mail address, your account will be marked unconfirmed until you confirm it via e-mail.</span>';
		} else {
			$email_warning_msg = '';
		}

		if ($FUD_OPT_1 & 28 && _uid) {
			if ($FUD_OPT_1 == 28) {
				/* if there are no built-in avatars, don't show them */
				if (q_singleval('SELECT count(*) FROM fud26_avatar')) {
					$sel_opt = "Built In\nSpecify URL\nUpload Avatar";
					$a_type='b';
					$sel_val = "b\nc\nu";
				} else {
					$sel_opt = "Specify URL\nUpload Avatar";
					$a_type='u';
					$sel_val = "c\nu";
				}
			} else {
				$a_type = $sel_opt = $sel_val = '';

				if (q_singleval('SELECT count(*) FROM fud26_avatar') && $FUD_OPT_1 & 16) {
					$sel_opt .= "Built In\n";
					$a_type = 'b';
					$sel_val .= "b\n";
				}
				if ($FUD_OPT_1 & 8) {
					$sel_opt .= "Upload Avatar\n";
					if (!$a_type) {
						$a_type = 'u';
					}
					$sel_val .= "u\n";
				}
				if ($FUD_OPT_1 & 4) {
					$sel_opt .= "Specify URL\n";
					if (!$a_type) {
						$a_type = 'c';
					}
					$sel_val .= "c\n";
				}
				$sel_opt = trim($sel_opt);
				$sel_val = trim($sel_val);
			}

			if ($a_type) { /* rare condition, no built-in avatars & no other avatars are allowed */
				if (!$avatar_type) {
					$avatar_type = $a_type;
				}
				$avatar_type_sel_options = tmpl_draw_select_opt($sel_val, $sel_opt, $avatar_type);
				$avatar_type_sel = '<tr class="vt RowStyleA"><td>Avatar Type:</td><td><select name="avatar_type" onChange="javascript: document.fud_register.submit();">'.$avatar_type_sel_options.'</select></td></tr>';

				/* preview image */
				if (isset($_POST['prev_loaded'])) {
					if ((!empty($_POST['reg_avatar']) && $_POST['reg_avatar'] == $uent->avatar) || (!empty($avatar_arr['file']) && empty($avatar_arr['del']) && $avatar_arr['leave'])) {
						$custom_avatar_preview = $uent->avatar_loc;
					} else if (!empty($_POST['reg_avatar']) && ($im = q_singleval('SELECT img FROM fud26_avatar WHERE id='.(int)$_POST['reg_avatar']))) {
						$custom_avatar_preview = make_avatar_loc('images/avatars/' . $im, $WWW_ROOT_DISK, $WWW_ROOT);
					} else {
						if ($reg_avatar_loc_file) {
							$common_name = $reg_avatar_loc_file;
						} else if (!empty($avatar_arr['file']) && empty($avatar_arr['del'])) {
							$common_name = $avatar_arr['file'];
						} else {
							$common_name = '';
						}
						$custom_avatar_preview = $common_name ? make_avatar_loc(basename($common_name), $TMP, 'index.php?t=tmp_view&img=') : '';
					}
				} else if ($uent->avatar_loc) {
					$custom_avatar_preview = $uent->avatar_loc;
				} else {
					$custom_avatar_preview = '';
				}

				if (!$custom_avatar_preview) {
					$custom_avatar_preview = '<img src="blank.gif" />';
				}

				/* determine the avatar specification field to show */
				if ($avatar_type == 'b') {
					if (empty($reg_avatar)) {
						$reg_avatar = '0';
						$reg_avatar_img = 'blank.gif';
					} else if (!empty($reg_avatar_loc)) {
						preg_match('!images/avatars/([^"]+)"!', reverse_fmt($reg_avatar_loc), $tmp);
						$reg_avatar_img = 'images/avatars/' . $tmp[1];
					} else {
						$reg_avatar_img = 'images/avatars/' . q_singleval('SELECT img FROM fud26_avatar WHERE id='.(int)$reg_avatar);
					}
					$del_built_in_avatar = $reg_avatar ? '[<a href="javascript: return false;" onClick="document.reg_avatar_img.src=\'blank.gif\'; document.fud_register.reg_avatar.value=\'0\';">Delete Avatar</a>]' : '';
					$avatar = '<tr class="vt RowStyleA"><td>Avatar:</td><td><img src="'.$reg_avatar_img.'" name="reg_avatar_img" alt="" />
<input type="hidden" name="reg_avatar" value="'.$reg_avatar.'">[<a href="javascript: window_open(\'http://timeweather.net/forum/index.php?t=avatarsel&amp;'._rsid.'\', \'avtsel\', 400, 300);">Select Avatar</a>]
'.$del_built_in_avatar.'<br /></td></tr>';
				} else if ($avatar_type == 'c') {
					if (!isset($reg_avatar_loc)) {
						$reg_avatar_loc = '';
					}
					$avatar = '<tr class="RowStyleC vt"><td colspan=2>The custom avatar will not appear until it is approved by the administrator.<br><span class="SmallText">The avatar, should be no larger than <b>'.$GLOBALS['CUSTOM_AVATAR_MAX_DIM'].' pixels</b> and must be in <b>jpg</b>, <b>gif</b> or <b>png</b> format.</span></td></tr>
<tr class="vt RowStyleA"><td>Custom Avatar URL: '.draw_err('avatar').'</td><td><input type="text" value="'.$reg_avatar_loc.'" name="reg_avatar_loc"></td></tr>';
				} else if ($avatar_type == 'u') {
					$avatar_tmp = $avatar_arr ? base64_encode($avatar_arr['file'] . "\n" . $avatar_arr['del'] . "\n" . $avatar_arr['leave']) : '';
					$buttons = (!empty($avatar_arr['file']) && empty($avatar_arr['del'])) ? '&nbsp;<input type="submit" class="button" name="btn_detach" value="Delete Avatar">' : '<input type="file" name="avatar_upload"> <input type="submit" class="button" name="btn_upload" value="Preview">
<input type="hidden" name="tmp_f_val" value="1">';
					$avatar = '<tr class="RowStyleC vt"><td colspan=2>The custom avatar will not appear until it is approved by the administrator.<br><span class="SmallText">The avatar, should be no larger than <b>'.$GLOBALS['CUSTOM_AVATAR_MAX_DIM'].' pixels</b> and must be in <b>jpg</b>, <b>gif</b> or <b>png</b> format.</span></td></tr>
<tr class="vt RowStyleA"><td>Custom Avatar File: '.draw_err('avatar').'</td><td><table border=0 cellspacing=0 cellpadding=0><tr><td>'.$custom_avatar_preview.'</td><td>'.$buttons.'</td></tr></table></td></tr>
<input type="hidden" name="avatar_tmp" value="'.$avatar_tmp.'">';
				}
			}
		}
	}

	$theme_select = '';
	$r = q("SELECT id, name FROM fud26_themes WHERE theme_opt>=1 AND (theme_opt & 1) > 0 ORDER BY ((theme_opt & 2) > 0) DESC");
	/* only display theme select if there is >1 theme */
	if (db_count($r) > 1) {
		while ($t = db_rowarr($r)) {
			$selected = $t[0] == $reg_theme ? ' selected' : '';
			$theme_select .= '<option value="'.$t[0].'"'.$selected.'>'.$t[1].'</option>';
		}
		$theme_select = '<tr class="RowStyleA"><td>Theme:</td><td><select name="reg_theme">'.$theme_select.'</select></td></tr>';
	}
	unset($r);

	$views[384] = 'Flat View message and topic listing';
	if (!($FUD_OPT_3 & 2)) {
		$views[128] = 'Flat topic listing/Tree message listing';
	}
	if ($FUD_OPT_2 & 512) {
		$views[256] = 'Tree topic listing/Flat message listing';
		if (!($FUD_OPT_3 & 2)) {
			$views[0] = 'Tree View message and topic listing';
		}
	}

	$day_select		= tmpl_draw_select_opt("\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12\n13\n14\n15\n16\n17\n18\n19\n20\n21\n22\n23\n24\n25\n26\n27\n28\n29\n30\n31", "\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12\n13\n14\n15\n16\n17\n18\n19\n20\n21\n22\n23\n24\n25\n26\n27\n28\n29\n30\n31", $b_day);
	$month_select		= tmpl_draw_select_opt("\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12", "\nJanuary\nFebruary\nMarch\nApril\nMay\nJune\nJuly\nAugust\nSeptember\nOctober\nNovember\nDecember", $b_month);
	$gender_select		= tmpl_draw_select_opt("512\n1024\n0","UNSPECIFIED\nMale\nFemale", ($uent->users_opt & 512 ? 512 : ($uent->users_opt & 1024)));
	$mppg_select		= tmpl_draw_select_opt("0\n5\n10\n20\n30\n40", "Use forum default\n5\n10\n20\n30\n40", $reg_posts_ppg);
	$view_select		= tmpl_draw_select_opt(implode("\n", array_keys($views)), implode("\n", $views), (($uent->users_opt & 128) | ($uent->users_opt & 256)));
	$timezone_select	= tmpl_draw_select_opt($tz_values, $tz_names, $reg_time_zone);
	$notification_select	= tmpl_draw_select_opt("4\n134217728", "E-mail\nDon&#39;t Notify", ($uent->users_opt & (4|134217728)));

	$ignore_admin_radio	= tmpl_draw_radio_opt('reg_ignore_admin', "8\n0", "Yes\nNo", ($uent->users_opt & 8), '&nbsp;&nbsp;');
	$invisible_mode_radio	= tmpl_draw_radio_opt('reg_invisible_mode', "32768\n0", "Yes\nNo", ($uent->users_opt & 32768), '&nbsp;&nbsp;');
	$show_email_radio	= tmpl_draw_radio_opt('reg_display_email', "1\n0", "Yes\nNo", ($uent->users_opt & 1), '&nbsp;&nbsp;');
	$notify_default_radio	= tmpl_draw_radio_opt('reg_notify', "2\n0", "Yes\nNo", ($uent->users_opt & 2), '&nbsp;&nbsp;');
	$pm_notify_default_radio= tmpl_draw_radio_opt('reg_pm_notify', "64\n0", "Yes\nNo", ($uent->users_opt & 64), '&nbsp;&nbsp;');
	$accept_user_email	= tmpl_draw_radio_opt('reg_email_messages', "16\n0", "Yes\nNo", ($uent->users_opt & 16), '&nbsp;&nbsp;');
	$accept_pm		= tmpl_draw_radio_opt('reg_pm_messages', "32\n0", "Yes\nNo", ($uent->users_opt & 32), '&nbsp;&nbsp;');
	$show_sig_radio		= tmpl_draw_radio_opt('reg_show_sigs', "4096\n0", "Yes\nNo", ($uent->users_opt & 4096), '&nbsp;&nbsp;');
	$show_avatar_radio	= tmpl_draw_radio_opt('reg_show_avatars', "8192\n0", "Yes\nNo", ($uent->users_opt & 8192), '&nbsp;&nbsp;');
	$show_im_radio		= tmpl_draw_radio_opt('reg_show_im', "16384\n0", "Yes\nNo", ($uent->users_opt & 16384), '&nbsp;&nbsp;');
	$append_sig_radio	= tmpl_draw_radio_opt('reg_append_sig', "2048\n0", "Yes\nNo", ($uent->users_opt & 2048), '&nbsp;&nbsp;');

if ($FUD_OPT_2 & 2 || $is_a) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = $FUD_OPT_2 & 2 ? '<br /><div class="SmallText al">Total time taken to generate the page: '.$page_gen_time.' seconds</div>' : '<br /><div class="SmallText al">Total time taken to generate the page: '.$page_gen_time.' seconds</div>';
} else {
	$page_stats = '';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<BASE HREF="http://timeweather.net/forum/">
<script language="javascript" src="lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="theme/default/forum.css" type="text/css" media="screen" title="Default FUDforum Theme">
</head>
<body>

<table width="98%" cellpadding="0" cellspacing="0" class="tbright_cell">
<tr valign="top" bgcolor="#EEEEFF">
<img border="0" src="/images/banner.jpg" width="730" height="100">

<img border="0" src="/images/line14.gif" width="100%" height="25">
</tr>



<tr>

<h2><a href="http://timeweather.net">Jump to Website</a></h2>
<table class="wa" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">


</tr>
<div class="UserControlPanel"><?php echo $private_msg; ?> <?php echo (($FUD_OPT_1 & 8388608 || (_uid && $FUD_OPT_1 & 4194304) || $usr->users_opt & 1048576) ? '<a class="UserControlPanel" href="index.php?t=finduser&amp;btn_submit=Find&amp;'._rsid.'"><img src="theme/default/images/top_members.png" alt="Members" /> Members</a>&nbsp;&nbsp;' : ''); ?> <?php echo ($FUD_OPT_1 & 16777216 ? '<a class="UserControlPanel" href="index.php?t=search&amp;'._rsid.'"><img src="theme/default/images/top_search.png" alt="Search" /> Search</a>&nbsp;&nbsp;' : ''); ?> <a class="UserControlPanel" accesskey="h" href="index.php?t=help_index&amp;<?php echo _rsid; ?>"><img src="theme/default/images/top_help.png" alt="FAQ" /> FAQ</a> <?php echo (__fud_real_user__ ? '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=uc&amp;'._rsid.'"><img src="theme/default/images/top_profile.png" title="Click here to access user control panel" alt="User CP" /> User CP</a>' : '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=register&amp;'._rsid.'"><img src="theme/default/images/top_register.png" alt="Register" /> Register</a>'); ?> <?php echo (__fud_real_user__ ? '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=login&amp;'._rsid.'&amp;logout=1&amp;SQ='.$GLOBALS['sq'].'"><img src="theme/default/images/top_logout.png" alt="Logout" /> Logout [ '.$usr->alias.' ]</a>' : '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=login&amp;'._rsid.'"><img src="theme/default/images/top_login.png" alt="Login" /> Login</a>'); ?>&nbsp;&nbsp; <a class="UserControlPanel" href="index.php?t=index&amp;<?php echo _rsid; ?>"><img src="theme/default/images/top_home.png" alt="Home" /> Home</a> <?php echo ($is_a ? '&nbsp;&nbsp;<a class="UserControlPanel" href="adm/admglobal.php?S='.s.'&amp;SQ='.$GLOBALS['sq'].'"><img src="theme/default/images/top_admin.png" alt="Admin Control Panel" /> Admin Control Panel</a>' : ''); ?></div>
<?php echo $tabs; ?>
<form method="post" action="index.php?t=register" name="fud_register" enctype="multipart/form-data"<?php echo ($FUD_OPT_3 & 256 ? ' autocomplete="off"' : ''); ?>>
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2>Required Information</th></tr>
<tr><td colspan="2" class="RowStyleC">All fields are required.  Please note that passwords are case sensitive.</td></tr>
<?php echo (!__fud_real_user__ ? (!__fud_real_user__ ? draw_err('reg_time_limit').'' : '' )  .'
<tr class="RowStyleA"><td>Login:'.draw_err('reg_login').'</td><td><input type="text" size=25 name="reg_login" value="'.$reg_login.'" maxLength='.$GLOBALS['MAX_LOGIN_SHOW'].'></td></tr>
'.($FUD_OPT_2 & 128 ? '<tr class="RowStyleA"><td>Alias:'.draw_err('reg_alias').'<br /><span class="SmallText">If you want a nickname other than your login to be displayed in the forum, enter it here.</span></td><td><input type="text" name="reg_alias" size=25 value="'.$reg_alias.'" maxLength="'.$GLOBALS['MAX_LOGIN_SHOW'].'"></td></tr>' : '' )  .'
<tr class="RowStyleA"><td>Password:'.draw_err('reg_plaintext_passwd').'</td><td><input type="password" name="reg_plaintext_passwd" size=25 maxlength=16></td></tr>
<tr class="RowStyleA"><td>Confirm Password:</td><td><input type="password" name="reg_plaintext_passwd_conf" size=25 maxlength=16></td></tr>
<tr class="RowStyleA"><td>E-mail Address:'.draw_err('reg_email').'<br /><span class="SmallText">Please enter a valid e-mail address. You can choose to hide it below, in the Preferences section.</span></td><td><input type="text" name="reg_email" size=25 value="'.$reg_email.'"></td></tr>
<tr class="RowStyleA"><td>Name:'.draw_err('reg_name').'</td><td><input type="text" name="reg_name" size=25 value="'.$reg_name.'"></td></tr>
'.(!($FUD_OPT_3 & 128) ? '<tr class="RowStyleA"><td>Please enter the code shown below:'.draw_err('reg_turing').'<br /><div style="white-space: pre; font-family: Courier; color: black; background-color: #C0C0C0;">'.generate_turing_val($turing_res).'<input type="hidden" name="turing_res" value="'.$turing_res.'"></div></td><td class="vt"><input type="text" name="turing_test" value=""></td></tr>' : '' )  : '<tr class="RowStyleA"><td>Login:</td><td class="fb">'.$reg_login.'</td></tr>
'.($FUD_OPT_2 & 128 ? '<tr class="RowStyleA"><td>Alias:'.draw_err('reg_alias').'<br /><span class="SmallText">If you want a nickname other than your login to be displayed in the forum, enter it here.</span></td><td><input type="text" name="reg_alias" size=25 value="'.$reg_alias.'" maxLength="'.$GLOBALS['MAX_LOGIN_SHOW'].'"></td></tr>' : '' )  .'
<tr class="RowStyleA"><td>Your Password:'.draw_err('reg_confirm_passwd').'</td><td><input type="password" name="reg_confirm_passwd" size=25 maxlength=16>'.(!$mod_id ? '&nbsp;<span class="SmallText">[ <a href="javascript://" onClick="javascript: window_open(\'http://timeweather.net/forum/index.php?t=rpasswd&amp;'._rsid.'\',\'rpass\',380,250);">change password</a> ]</span>' : '' )  .'</td></tr>
<tr class="RowStyleA"><td>E-mail Address:'.draw_err('reg_email').'<br /><span class="SmallText">Please enter a valid e-mail address. You can choose to hide it below, in the Preferences section.</span>'.$email_warning_msg.'</td><td><input type="text" name="reg_email" size=25 value="'.$reg_email.'"></td></tr>
<tr class="RowStyleA"><td>Name:'.draw_err('reg_name').'</td><td><input type="text" name="reg_name" size=25 value="'.$reg_name.'"></td></tr>
<tr><td colspan=2 class="ac RowStyleC"><input type="submit" class="button" name="fud_submit" value="Update"></td></tr>' )  .'
<tr><th colspan=2>Optional Information</th></tr>
<tr><td colspan="2" class="RowStyleC">It is recommended that you not reveal any personal or identifying information in your profile.  All information will be viewable by other forum members.</td></tr>
<tr class="RowStyleA"><td>Location:</td><td><input type="text" name="reg_location" value="'.$reg_location.'"maxlength=255 size=30></td></tr>
<tr class="RowStyleA"><td>Occupation:</td><td><input type="text" name="reg_occupation" value="'.$reg_occupation.'"maxlength=255 size=30></td></tr>
<tr class="RowStyleA"><td>Interests:</td><td><input type="text" name="reg_interests" value="'.$reg_interests.'"maxlength=255 size=30></td></tr>
'.($FUD_OPT_2 & 65536 ? '<tr class="RowStyleA"><td>Image:</td><td><input type="text" name="reg_user_image" value="'.$reg_user_image.'"maxlength=255 size=30></td></tr>' : ''); ?>
<tr class="RowStyleA"><td>ICQ</td><td><input type="text" name="reg_icq" value="<?php echo $reg_icq; ?>" maxLength=32 size=25></td></tr>
<tr class="RowStyleA"><td>AIM Handle:</td><td><input type="text" name="reg_aim" value="<?php echo $reg_aim; ?>" maxLength=32 size=25></td></tr>
<tr class="RowStyleA"><td>Yahoo Messenger:</td><td><input type="text" name="reg_yahoo" value="<?php echo $reg_yahoo; ?>" maxLength=32 size=25></td></tr>
<tr class="RowStyleA"><td>MSN Messenger:</td><td><input type="text" name="reg_msnm" value="<?php echo $reg_msnm; ?>" maxLength=32 size=25></td></tr>
<tr class="RowStyleA"><td>Jabber Handle:</td><td><input type="text" name="reg_jabber" value="<?php echo $reg_jabber; ?>" maxLength=32 size=25></td></tr>
<?php echo $register_affero; ?>
<tr class="RowStyleA"><td>Homepage:</td><td><input type="text" name="reg_home_page" value="<?php echo $reg_home_page; ?>" maxLength=255></td></tr>
<?php echo $avatar_type_sel; ?>
<?php echo $avatar; ?>
<tr class="RowStyleA vt"><td>Birth Date:<br /><span class="SmallText">If you enter a birthdate, then other forum members will be able to see it in your profile</span></td>
<td>
<table border=0 cellspacing=3 cellpadding=0>
 <tr class="GenText">
  <td class="ac">Month</td>
  <td class="ac">Day</td>
  <td class="ac">Year</td>
 </tr>
 <tr>
  <td class="ac"><select name="b_month"><?php echo $month_select; ?></select></td>
  <td class="ac"><select name="b_day"><?php echo $day_select; ?></select></td>
  <td class="ac"><input type="text" name="b_year" value="<?php echo $b_year; ?>" maxLength=4 size=5></td>
 </tr>
</table></td></tr>
<tr class="RowStyleA"><td>Gender:</td><td><select name="reg_gender"><?php echo $gender_select; ?></select></td></tr>
<tr class="RowStyleA"><td class="RowStyleA" valign="top">Biography:<br /><span class="SmallText">A few details about yourself, such as your interests, job, etc...</span></td><td><textarea name="reg_bio" rows=5 cols=35><?php echo $reg_bio; ?></textarea></td></tr>
<tr><th colspan=2>Preferences</th></tr>
<tr class="RowStyleA"><td class="vt">Signature:<br /><span class="SmallText">Optional signature, which will appear at the bottom of your messages<br /></span><?php echo tmpl_post_options('sig').($FORUM_SIG_ML ? '<br /><b>Maximum Length: </b>'. $GLOBALS['FORUM_SIG_ML'].' characters <a href="javascript: alert(\'Your Signature is \'+document.fud_register.reg_sig.value.length+\' characters long. The maximum allowed signature length is '. $GLOBALS['FORUM_SIG_ML'].' characters.\');" class="SmallText">Check Signature Length</a>' : '' )  ; ?></td><td><?php echo draw_err('reg_sig'); ?><textarea name="reg_sig" rows=8 cols=50><?php echo $reg_sig; ?></textarea></td></tr>
<tr class="RowStyleA"><td>Time Zone:</td><td><select name="reg_time_zone" class="SmallText"><?php echo $timezone_select; ?></select></td></tr>
<tr class="RowStyleA"><td>Ignore Administrative Messages:</td><td><?php echo $ignore_admin_radio; ?></td></tr>
<tr class="RowStyleA"><td>Invisible Mode:<br /><span class="SmallText">Hides your online status</span></td><td><?php echo $invisible_mode_radio; ?></td></tr>
<tr class="RowStyleA"><td>Show E-mail Address:<br /><span class="SmallText">Choose this option if you want your e-mail address to be displayed publicly.</span></td><td><?php echo $show_email_radio; ?></td></tr>
<tr class="RowStyleA"><td>Select Notification by Default:<br /><span class="SmallText">If notification is enabled by default, it can be disabled when posting.</span></td><td><?php echo $notify_default_radio; ?></td></tr>
<tr class="RowStyleA"><td>Private Message Notification<br /><span class="SmallText">If enabled, you will be notified whenever a private message is sent to you.</span></td><td><?php echo $pm_notify_default_radio; ?></td></tr>
<tr class="RowStyleA"><td>Choose Notification Method:</td><td><select name="reg_notify_method" onChange="javascript: re=/[^0-9]/g; a=document.fud_register.reg_icq.value.replace(re, ''); if(this.value=='ICQ' && !a.length ) { alert('You chose ICQ notification, but did not specify an ICQ #. Defaulting to E-mail&#46;'); this.value='EMAIL'; }"><?php echo $notification_select; ?></select></td></tr>
<tr class="RowStyleA"><td>Allow E-mail Messages:<br /><span class="SmallText">Allow other users to send you e-mails via this forum.</span></td><td><?php echo $accept_user_email; ?></td></tr>
<tr class="RowStyleA"><td>Allow Private Messages<br /><span class="SmallText">Allow other users to send you private messages in this forum.</span></td><td><?php echo $accept_pm; ?></td></tr>
<tr class="RowStyleA"><td>Use Signature by Default:<br /><span class="SmallText">Automatically append your signature to every message you post</span></td><td><?php echo $append_sig_radio; ?></td></tr>
<tr class="RowStyleA"><td>Show Signatures:<br /><span class="SmallText">Allows you to either hide or show other forum members&#39; signatures</span></td><td><?php echo $show_sig_radio; ?></td></tr>
<tr class="RowStyleA"><td>Show Avatars:<br /><span class="SmallText">Allows you to hide avatars of other users when viewing their messages</span></td><td><?php echo $show_avatar_radio; ?></td></tr>
<tr class="RowStyleA"><td>Show IM indicators<br /><span class="SmallText">Whether or not to show IM indicators of the author beside their messages.</span></td><td><?php echo $show_im_radio; ?></td></tr>
<tr class="RowStyleA"><td>Messages Per Page:</td><td><select name="reg_posts_ppg"><?php echo $mppg_select; ?></select></td></tr>
<tr class="RowStyleA"><td>Default Topic View:</td><td><select name="reg_default_view"><?php echo $view_select; ?></select></td></tr>
<?php echo $theme_select; ?>

<tr class="RowStyleC"><td colspan=2 class="ac"><?php echo (!__fud_real_user__ ? '<input type="submit" class="button" name="fud_submit" value="Register">' : '<input type="submit" class="button" name="fud_submit" value="Update">'); ?>&nbsp;<INPUT TYPE="reset" class="button" NAME="Reset" VALUE="Reset"></td></tr>
</table>
<?php echo _hs; ?>
<input type="hidden" name="prev_loaded" value="1">
<input type="hidden" name="mod_id" value="<?php echo $mod_id; ?>">
<input type="hidden" name="reg_coppa" value="<?php echo $reg_coppa; ?>">
</form>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>