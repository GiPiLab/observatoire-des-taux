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

require_once "fonctions_taux.php";

if (isset($_GET['annee'])) {
	$annee = htmlentities($_GET['annee']);
	if (!is_numeric($annee) || $annee > date("Y") || $annee < 1999) die("Ann&eacute;e incorrecte !");
	$annee = (int)$annee;
} else $annee = date("Y");

if ($annee >= 2020) {
	$esterOrEonia = "ester";
} else {
	$esterOrEonia = "eonia";
}

$mode = 0;

//Mode = 1 : TEC
if (isset($_GET['mode']) && $_GET['mode'] === "1") $mode = 1;


$connect = connect_base();

if ($mode == 0) {
	$requete = "select * from euribor,$esterOrEonia where euribor.date=$esterOrEonia.date and year(euribor.date)='$annee' order by euribor.date limit 1";
} else {
	$requete = "select * from TEC where year(date)='$annee' order by date limit 1";
}

$result = mysqli_query($connect, $requete) or die(mysqli_error($connect));
$premier_jour_annee = mysqli_fetch_assoc($result);

if (empty($premier_jour_annee['date'])) die("Erreur impr&eacute;vue !");

$premier_jour_annee["date"] = date("d/m/Y", strtotime($premier_jour_annee["date"]));

if ($mode == 0) {
	$requete = "select * from euribor,$esterOrEonia where euribor.date=$esterOrEonia.date and year(euribor.date)='$annee' order by euribor.date desc limit 1";
} else {
	$requete = "select * from TEC where year(date)='$annee' order by date desc limit 1";
}

$result = mysqli_query($connect, $requete) or die(mysqli_error($connect));
while (($res = mysqli_fetch_assoc($result)) != NULL) {
	$res["date"] = date("d/m/Y", strtotime($res["date"]));
	$data["dernierJourConnu"] = $res;
}
mysqli_close($connect);

if (empty($data["dernierJourConnu"])) die("Erreur impr&eacute;vue !");


$data['premierJourAnnee'] = $premier_jour_annee;

echo json_encode($data, JSON_NUMERIC_CHECK);
