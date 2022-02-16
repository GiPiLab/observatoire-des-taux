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

require "fonctions_taux.php";

//0=TEC, 1=euribor/eonia/ester, 2=les deux
$mode = 0;

if (isset($_GET['mode'])) {
	if ($_GET['mode'] != 0 && $_GET['mode'] != 1 && $_GET['mode'] != 2) $mode = 0;
	else $mode = $_GET['mode'];
}
$connect = connect_base();

$vals = array();
$alldatas = array();

if ($mode == 0 || $mode == 2) {
	$tocheck = array("TEC10", "TEC15", "TEC20", "TEC25", "TEC30");
	$tecs = array();
	foreach ($tocheck as $oneval) {
		$vals[$oneval] = array();
		$tecs[$oneval] = array();
		$res = mysqli_query($connect, "select $oneval from TEC where $oneval is not null") or die(mysqli_error($connect));

		while (($result = mysqli_fetch_array($res)) != NULL) {
			if (array_key_exists($result[$oneval], $vals[$oneval])) $vals[$oneval][$result[$oneval]]++;
			else $vals[$oneval][$result[$oneval]] = 1;
		}
		foreach ($vals[$oneval] as $key => $val) {
			if ($val > 2) $tecs[$oneval][] = array(doubleval($key), $val);
		}
		$alldata[$oneval]["data"] = $tecs[$oneval];
		$alldata[$oneval]["nbOcc"] = mysqli_num_rows($res);
	}
}

if ($mode == 1 || $mode == 2) {
	$vals['eonia'] = array();
	$res = mysqli_query($connect, "select eonia from eonia where eonia is not null") or die(mysqli_error($connect));
	$countEo = 0;
	while (($result = mysqli_fetch_array($res)) != NULL) {
		$countEo++;
		if (array_key_exists($result['eonia'], $vals['eonia'])) $vals['eonia'][$result['eonia']]++;
		else $vals['eonia'][$result['eonia']] = 1;
	}

	$eonias = array();
	foreach ($vals['eonia'] as $key => $val) {
		$eonia = array(doubleval($key), $val);
		if ($val > 2)
			$eonias[] = $eonia;
	}

	$alldata["eonia"]["data"] = $eonias;
	$alldata["eonia"]["nbOcc"] = mysqli_num_rows($res);

	//ESTER
	$vals['ester'] = array();
	$res = mysqli_query($connect, "select ester from ester where ester is not null") or die(mysqli_error($connect));
	$countEster = 0;
	while (($result = mysqli_fetch_array($res)) != NULL) {
		$countEster++;
		if (array_key_exists($result['ester'], $vals['ester'])) $vals['ester'][$result['ester']]++;
		else $vals['ester'][$result['ester']] = 1;
	}

	$esters = array();
	foreach ($vals['ester'] as $key => $val) {
		$ester = array(doubleval($key), $val);
		if ($val > 2)
			$esters[] = $ester;
	}

	$alldata["ester"]["data"] = $esters;
	$alldata["ester"]["nbOcc"] = mysqli_num_rows($res);


	$euribors = array();

	$tocheck = array("1_mois", "3_mois", "6_mois", "12_mois");

	foreach ($tocheck as $oneval) {
		$count = 0;
		$vals[$oneval] = array();
		$euribors[$oneval] = array();

		$requete = "select $oneval from euribor where $oneval is not null";
		$res = mysqli_query($connect, $requete) or die("Requête impossible : " . mysqli_error($connect));
		while ($result = mysqli_fetch_array($res)) {
			if (array_key_exists($result[$oneval], $vals[$oneval])) $vals[$oneval][$result[$oneval]]++;
			else $vals[$oneval][$result[$oneval]] = 1;
		}

		foreach ($vals[$oneval] as $key => $val) {
			if ($val > 2) $euribors[$oneval][] = array(doubleval($key), $val);
		}
		$alldata["eur-" . $oneval]["data"] = $euribors[$oneval];
		$alldata["eur-" . $oneval]["nbOcc"] = mysqli_num_rows($res);
	}
}
mysqli_close($connect);
echo json_encode($alldata, JSON_NUMERIC_CHECK);
