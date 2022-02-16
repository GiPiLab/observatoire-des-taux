<?php
/*
* Observatoire des taux
*
* Copyright Thibault et Gilbert Mondary, Laboratoire de Recherche pour le Développement Local (2006--)
*
* labo@gipilab.org
*
* Ce logiciel est un programme informatique servant à visualiser différents indicateurs sur les taux
* (historique, courbes des taux, pression conjoncturelle...)
*
*
* Ce logiciel est régi par la licence CeCILL soumise au droit français et
* respectant les principes de diffusion des logiciels libres. Vous pouvez
* utiliser, modifier et/ou redistribuer ce programme sous les conditions
* de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA
* sur le site "http://www.cecill.info".
*
* En contrepartie de l'accessibilité au code source et des droits de copie,
* de modification et de redistribution accordés par cette licence, il n'est
* offert aux utilisateurs qu'une garantie limitée. Pour les mêmes raisons,
* seule une responsabilité restreinte pèse sur l'auteur du programme, le
* titulaire des droits patrimoniaux et les concédants successifs.
*
* A cet égard l'attention de l'utilisateur est attirée sur les risques
* associés au chargement, à l'utilisation, à la modification et/ou au
* développement et à la reproduction du logiciel par l'utilisateur étant
* donné sa spécificité de logiciel libre, qui peut le rendre complexe à
* manipuler et qui le réserve donc à des développeurs et des professionnels
* avertis possédant des connaissances informatiques approfondies. Les
* utilisateurs sont donc invités à charger et tester l'adéquation du
* logiciel à leurs besoins dans des conditions permettant d'assurer la
* sécurité de leurs systèmes et ou de leurs données et, plus généralement,
* à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.
*
* Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
* pris connaissance de la licence CeCILL, et que vous en avez accepté les
* termes.
*
*/


//N'utilise pas de SESSION car peut être appellé depuis le simulateur d'emprunts sur android
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
require_once 'fonctions_taux.php';

if (isset($_GET['annee'])) {
	$annee = htmlentities($_GET['annee']);
	if (!is_numeric($annee) || $annee > date("Y") || $annee < 1999) die("Ann&eacute;e incorrecte !");
	$annee = (int)$annee;
} else $annee = date("Y");


$connect = connect_base();

$requete = "select round(max(1_mois),3) as max1,round(max(3_mois),3) as max3,round(max(6_mois),3) as max6, round(max(12_mois),3) as max12, round(min(1_mois),3) as min1,round(min(3_mois),3) as min3,round(min(6_mois),3) as min6,round(min(12_mois),3) as min12 from euribor where year(date)<='$annee' limit 1";
$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
while ($euribor = mysqli_fetch_assoc($result)) {
	$euribors['unMois']['min'] = $euribor['min1'];
	$euribors['troisMois']['min'] = $euribor['min3'];
	$euribors['sixMois']['min'] = $euribor['min6'];
	$euribors['douzeMois']['min'] = $euribor['min12'];
	$euribors['unMois']['max'] = $euribor['max1'];
	$euribors['troisMois']['max'] = $euribor['max3'];
	$euribors['sixMois']['max'] = $euribor['max6'];
	$euribors['douzeMois']['max'] = $euribor['max12'];
}

$requete = "select * from euribor where year(date)='$annee' order by date desc limit 2";
$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
$date_euribor = 0;
$i = 0;
while ($euribor = mysqli_fetch_assoc($result)) {
	if ($i == 0) {
		$euribors['unMois']['current'] = $euribor['1_mois'];
		$euribors['troisMois']['current'] = $euribor['3_mois'];
		$euribors['sixMois']['current'] = $euribor['6_mois'];
		$euribors['douzeMois']['current'] = $euribor['12_mois'];
	} else {
		$euribors['unMois']['prev'] = $euribor['1_mois'];
		$euribors['troisMois']['prev'] = $euribor['3_mois'];
		$euribors['sixMois']['prev'] = $euribor['6_mois'];
		$euribors['douzeMois']['prev'] = $euribor['12_mois'];
	}
	list($annee, $mois, $jour) = explode('-', $euribor['date']);
	if ($date_euribor == 0) $date_euribor = "$jour/$mois/$annee";
	$i++;
}
$euribors['date'] = $date_euribor;

$requete = "select round(max(TEC10),3) as max10,round(max(TEC15),3) as max15,round(max(TEC20),3) as max20, round(max(TEC25),3) as max25, round(max(TEC30),3) as max30, round(min(TEC10),3) as min10,round(min(TEC15),3) as min15,round(min(TEC20),3) as min20,round(min(TEC25),3) as min25, round(min(TEC30),3) as min30 from TEC where year(date)<='$annee' limit 1";
$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
while ($tec = mysqli_fetch_assoc($result)) {
	$tecs['tec10']['min'] = $tec['min10'];
	$tecs['tec15']['min'] = $tec['min15'];
	$tecs['tec20']['min'] = $tec['min20'];
	$tecs['tec25']['min'] = $tec['min25'];
	$tecs['tec30']['min'] = $tec['min30'];
	$tecs['tec10']['max'] = $tec['max10'];
	$tecs['tec15']['max'] = $tec['max15'];
	$tecs['tec20']['max'] = $tec['max20'];
	$tecs['tec25']['max'] = $tec['max25'];
	$tecs['tec30']['max'] = $tec['max30'];
}

$requete = "select * from TEC where year(date)='$annee' order by date desc limit 2";
$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
$date_tec = 0;
$i = 0;

while ($tec = mysqli_fetch_assoc($result)) {
	if ($i == 0) {
		$tecs['tec10']['current'] = $tec['TEC10'];
		$tecs['tec15']['current'] = $tec['TEC15'];
		$tecs['tec20']['current'] = $tec['TEC20'];
		$tecs['tec25']['current'] = $tec['TEC25'];
		$tecs['tec30']['current'] = $tec['TEC30'];
	} else {
		$tecs['tec10']['prev'] = $tec['TEC10'];
		$tecs['tec15']['prev'] = $tec['TEC15'];
		$tecs['tec20']['prev'] = $tec['TEC20'];
		$tecs['tec25']['prev'] = $tec['TEC25'];
		$tecs['tec30']['prev'] = $tec['TEC30'];
	}
	$i++;
	list($annee, $mois, $jour) = explode('-', $tec['date']);
	if ($date_tec == 0) $date_tec = "$jour/$mois/$annee";
}
$tecs['date'] = $date_tec;


if ($annee >= 2020) {
	$ester_or_eonia = "ester";
} else {
	$ester_or_eonia = "eonia";
}


$requete = "select round(min($ester_or_eonia),3) as mineo,round(max($ester_or_eonia),3) as maxeo from $ester_or_eonia where year(date)<='$annee' limit 1";
$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
while ($eo = mysqli_fetch_assoc($result)) {
	$eonia['min'] = $eo['mineo'];
	$eonia['max'] = $eo['maxeo'];
}

$requete = "select * from $ester_or_eonia where year(date)='$annee' order by date desc limit 2";
$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
$date_eonia = 0;
$i = 0;
while ($eo = mysqli_fetch_assoc($result)) {
	if ($i == 0) {
		$eonia['current'] = $eo[$ester_or_eonia];
	} else {
		$eonia['prev'] = $eo[$ester_or_eonia];
	}
	list($annee, $mois, $jour) = explode('-', $eo['date']);
	if ($date_eonia == 0) $date_eonia = "$jour/$mois/$annee";
	$i++;
}
$eonia['date'] = $date_eonia;
$data["$ester_or_eonia"] = $eonia;
$data['euribors'] = $euribors;
$data['tecs'] = $tecs;
mysqli_close($connect);
echo json_encode($data, JSON_NUMERIC_CHECK);
