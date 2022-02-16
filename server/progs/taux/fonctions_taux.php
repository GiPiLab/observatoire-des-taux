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

function np($nombre)
{
	return number_format($nombre, 3, ',', ' ');
}



function maildie($message)
{
	die($message);
}

function connect_base()
{
	$connect = mysqli_connect("host", "xxx", "yyyy", "zzzz") or maildie("Connection à la base impossible !");
	return ($connect);
}


function tableau_plus_annuel_tec($annee, $dernier_connu, $connect)
{
	list($y, $m, $j) = explode('-', $dernier_connu['date']);

	/*
	 *CALCUL DU RANG : Version factoris�e, simple !*/

	$tocheck = array("TEC10", "TEC15", "TEC20", "TEC25", "TEC30");

	$rangs = array();
	$total_rangs = array();
	$minimums = array();
	$maximums = array();

	foreach ($tocheck as $one) {
		$requete = "SELECT DISTINCT $one from TEC where $one is not null and YEAR(date)='$annee' ORDER BY $one DESC";
		$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
		$rangs[$one] = 1;
		$total_rangs[$one] = 0;
		while ($temp = mysqli_fetch_row($result)) {
			if ($temp[0] > $dernier_connu[$one]) {
				$rangs[$one]++;
			}
			$total_rangs[$one]++;
		}

		$requete = "SELECT MIN($one) from TEC where YEAR(date)='$annee'";
		$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
		$minimums[$one] = mysqli_fetch_row($result);

		$requete = "SELECT MAX($one) from TEC where YEAR(date)='$annee'";
		$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
		$maximums[$one] = mysqli_fetch_row($result);
	}

	echo "<table class='taux striped centered'><caption>Positions pour l'ann&eacute;e en cours</caption><thead><tr><th>maturit&eacute;</th><th>rang/nb<br />(1/x=max)</th><th>plus haut</th><th>plus bas</th></tr></thead><tbody>";

	//Maturit�, nbcotations, rang, moyenne, maxi, mini

	foreach ($tocheck as $one) {
		if ($total_rangs[$one] != 0) {
			printf("<tr><td>$one</td><td>%u/%u</td><td>%s&nbsp;%%</td><td>%s&nbsp;%%</td></tr>", $rangs[$one], $total_rangs[$one], number_format($maximums[$one][0], 3, ',', ' '), number_format($minimums[$one][0], 3, ',', ' '));
		} else {
			echo "<tr><td>$one</td><td>NA</td><td>NA</td><td>NA</td></tr>";
		}
	}
	echo '</tbody></table>';
}

function tableau_plus_annuel($annee, $dernier_connu, $connect)
{
	//if($annee!=date("Y"))return;

	list($y, $m, $j) = explode('-', $dernier_connu['date']);
	if ($y >= 2020) {
		$ester_or_eonia = "ester";
	} else {
		$ester_or_eonia = "eonia";
	}

	$requete = "SELECT COUNT(*) from euribor,$ester_or_eonia where euribor.date=$ester_or_eonia.date and YEAR(euribor.date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$nbcotations = mysqli_fetch_row($result);

	/*
	 *CALCUL DU RANG*/
	$requete = "SELECT DISTINCT $ester_or_eonia from $ester_or_eonia where YEAR(date)='$annee' ORDER BY $ester_or_eonia DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_ester_or_eonia = 1;
	$total_rangs_ester_or_eonia = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu["$ester_or_eonia"]) {
			$rang_ester_or_eonia++;
		}
		$total_rangs_ester_or_eonia++;
	}
	$requete = "SELECT DISTINCT 1_mois from euribor where YEAR(date)='$annee' ORDER BY 1_mois DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_1mois = 1;
	$total_rangs_1mois = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu['1_mois']) {
			$rang_1mois++;
		}
		$total_rangs_1mois++;
	}

	$requete = "SELECT DISTINCT 3_mois from euribor where YEAR(date)='$annee' ORDER BY 3_mois DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_3mois = 1;
	$total_rangs_3mois = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu['3_mois']) {
			$rang_3mois++;
		}
		$total_rangs_3mois++;
	}


	$requete = "SELECT DISTINCT 6_mois from euribor where YEAR(date)='$annee' ORDER BY 6_mois DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_6mois = 1;
	$total_rangs_6mois = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu['6_mois']) {
			$rang_6mois++;
		}
		$total_rangs_6mois++;
	}


	$requete = "SELECT DISTINCT 12_mois from euribor where YEAR(date)='$annee' ORDER BY 12_mois DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_12mois = 1;
	$total_rangs_12mois = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu['12_mois']) {
			$rang_12mois++;
		}
		$total_rangs_12mois++;
	}
	$requete = "SELECT MIN($ester_or_eonia) from $ester_or_eonia where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$minimum_ester_or_eonia = mysqli_fetch_row($result);

	$requete = "SELECT MIN(1_mois) from euribor where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$minimum_1mois = mysqli_fetch_row($result);

	$requete = "SELECT MIN(3_mois) from euribor where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$minimum_3mois = mysqli_fetch_row($result);

	$requete = "SELECT MIN(6_mois) from euribor where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$minimum_6mois = mysqli_fetch_row($result);

	$requete = "SELECT MIN(12_mois) from euribor where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$minimum_12mois = mysqli_fetch_row($result);

	$requete = "SELECT MAX($ester_or_eonia) from $ester_or_eonia where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$maximum_ester_or_eonia = mysqli_fetch_row($result);

	$requete = "SELECT MAX(1_mois) from euribor where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$maximum_1mois = mysqli_fetch_row($result);

	$requete = "SELECT MAX(3_mois) from euribor where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$maximum_3mois = mysqli_fetch_row($result);

	$requete = "SELECT MAX(6_mois) from euribor where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$maximum_6mois = mysqli_fetch_row($result);

	$requete = "SELECT MAX(12_mois) from euribor where YEAR(date)='$annee'";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$maximum_12mois = mysqli_fetch_row($result);

	printf("<table class='taux striped centered'><caption>Positions pour l'ann&eacute;e en cours (%s cotations)</caption><thead><tr><th>maturit&eacute;</th><th>rang/nb<br />(1/x=max)</th><th>plus haut</th><th>plus bas</th></tr></thead><tbody>", $nbcotations[0]);

	//Maturit�, nbcotations, rang, moyenne, maxi, mini

	printf("<tr><td>$ester_or_eonia</td><td>%u/%u</td><td>%s&nbsp;%%</td><td>%s&nbsp;%%</td></tr>", $rang_ester_or_eonia, $total_rangs_ester_or_eonia, number_format($maximum_ester_or_eonia[0], 3, ',', ' '), number_format($minimum_ester_or_eonia[0], 3, ',', ' '));

	printf("<tr><td>1 mois</td><td>%u/%u</td><td>%s&nbsp;%%</td><td>%s&nbsp;%%</td></tr>", $rang_1mois, $total_rangs_1mois, number_format($maximum_1mois[0], 3, ',', ' '), number_format($minimum_1mois[0], 3, ',', ' '));

	printf("<tr><td>3 mois</td><td>%u/%u</td><td>%s&nbsp;%%</td><td>%s&nbsp;%%</td></tr>", $rang_3mois, $total_rangs_3mois, number_format($maximum_3mois[0], 3, ',', ' '), number_format($minimum_3mois[0], 3, ',', ' '));

	printf("<tr><td>6 mois</td><td>%u/%u</td><td>%s&nbsp;%%</td><td>%s&nbsp;%%</td></tr>", $rang_6mois, $total_rangs_6mois, number_format($maximum_6mois[0], 3, ',', ' '), number_format($minimum_6mois[0], 3, ',', ' '));

	printf("<tr><td>1 an</td><td>%u/%u</td><td>%s&nbsp;%%</td><td>%s&nbsp;%%</td></tr>", $rang_12mois, $total_rangs_12mois, number_format($maximum_12mois[0], 3, ',', ' '), number_format($minimum_12mois[0], 3, ',', ' '));

	echo '</tbody></table>';
}

function tableau_plus_tout_tec($dernier_connu, $connect)
{
	list($y, $m, $j) = explode('-', $dernier_connu['date']);

	$tocheck = array("TEC10", "TEC15", "TEC20", "TEC25", "TEC30");

	$rangs = array();
	$total_rangs = array();
	$minimums = array();
	$maximums = array();


	foreach ($tocheck as $one) {
		$requete = "SELECT $one,date from TEC where $one is not null and YEAR(date)<='$y' group by $one ORDER BY $one DESC";
		$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
		$rangs[$one] = 1;
		$total_rangs[$one] = 0;
		while ($temp = mysqli_fetch_row($result)) {
			if ($temp[0] > $dernier_connu[$one]) {
				$rangs[$one]++;
			}
			$total_rangs[$one]++;
		}

		$requete = "SELECT $one, DATE_FORMAT(date,'%e-%c-%Y') from TEC where $one is not null and year(date)<=$y order by $one limit 1";
		$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
		$res = mysqli_fetch_row($result);
		$minimums[$one]['val'] = $res[0];
		$minimums[$one]['date'] = $res[1];

		$requete = "SELECT $one, DATE_FORMAT(date,'%e-%c-%Y') from TEC where $one is not null and year(date)<=$y order by $one desc limit 1";
		$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
		$res = mysqli_fetch_row($result);
		$maximums[$one]['val'] = $res[0];
		$maximums[$one]['date'] = $res[1];
	}

	echo "<table class='taux striped centered'><caption>Positions depuis le 04/01/1999</caption><thead><tr><th>maturit&eacute;</th><th>rang/nb<br />(1/x=max)</th><th>plus haut</th><th>plus bas</th></tr></thead><tbody>";

	//Maturité, nbcotations, rang, moyenne, maxi, mini

	foreach ($tocheck as $one) {
		if ($total_rangs[$one] != 0) {
			printf("<tr><td>$one</td><td>%u/%u</td><td>%s&nbsp;%%<br />%s</td><td>%s&nbsp;%%<br />%s</td></tr>", $rangs[$one], $total_rangs[$one], number_format($maximums[$one]['val'], 3, ',', ' '), $maximums[$one]['date'], number_format($minimums[$one]['val'], 3, ',', ' '), $minimums[$one]['date']);
		} else {
			echo "<tr><td>$one</td><td>NA</td><td>NA</td><td>NA</td></tr>";
		}
	}
	echo '</tbody></table>';
}

function tableau_plus_tout($dernier_connu, $connect)
{
	list($y, $m, $j) = explode('-', $dernier_connu['date']);
	if ($y >= 2020) {
		$ester_or_eonia = "ester";
	} else {
		$ester_or_eonia = "eonia";
	}

	$requete = "SELECT COUNT(*) from euribor,$ester_or_eonia where euribor.date=$ester_or_eonia.date";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$nbcotations = mysqli_fetch_row($result);


	/*
	 *CALCUL DU RANG*/
	$requete = "SELECT DISTINCT $ester_or_eonia from $ester_or_eonia where year(date)<='$y' ORDER BY $ester_or_eonia DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_ester_or_eonia = 1;
	$total_rangs_ester_or_eonia = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu["$ester_or_eonia"]) {
			$rang_ester_or_eonia++;
		}
		$total_rangs_ester_or_eonia++;
	}

	$requete = "SELECT DISTINCT 1_mois from euribor where year(date)<='$y' ORDER BY 1_mois DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_1mois = 1;
	$total_rangs_1mois = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu['1_mois']) {
			$rang_1mois++;
		}
		$total_rangs_1mois++;
	}

	$requete = "SELECT DISTINCT 3_mois from euribor where year(date)<='$y' ORDER BY 3_mois DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_3mois = 1;
	$total_rangs_3mois = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu['3_mois']) {
			$rang_3mois++;
		}
		$total_rangs_3mois++;
	}


	$requete = "SELECT DISTINCT 6_mois from euribor where year(date)<='$y' ORDER BY 6_mois DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_6mois = 1;
	$total_rangs_6mois = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu['6_mois']) {
			$rang_6mois++;
		}
		$total_rangs_6mois++;
	}


	$requete = "SELECT DISTINCT 12_mois from euribor where year(date)<='$y' ORDER BY 12_mois DESC";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$rang_12mois = 1;
	$total_rangs_12mois = 0;
	while ($temp = mysqli_fetch_row($result)) {
		if ($temp[0] > $dernier_connu['12_mois']) {
			$rang_12mois++;
		}
		$total_rangs_12mois++;
	}
	$requete = "SELECT $ester_or_eonia, DATE_FORMAT(date,'%e-%c-%Y') from $ester_or_eonia where year(date)<='$y' order by $ester_or_eonia limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$minimum_ester_or_eonia = $res[0];
	$date_minimum_ester_or_eonia = $res[1];

	$requete = "SELECT 1_mois, DATE_FORMAT(date,'%e-%c-%Y') from euribor where year(date)<='$y' order by 1_mois limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$minimum_1mois = $res[0];
	$date_minimum_1mois = $res[1];

	$requete = "SELECT 3_mois, DATE_FORMAT(date,'%e-%c-%Y') from euribor where year(date)<='$y' order by 3_mois limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$minimum_3mois = $res[0];
	$date_minimum_3mois = $res[1];

	$requete = "SELECT 6_mois, DATE_FORMAT(date,'%e-%c-%Y') from euribor where year(date)<='$y'  order by 6_mois limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$minimum_6mois = $res[0];
	$date_minimum_6mois = $res[1];

	$requete = "SELECT 12_mois, DATE_FORMAT(date,'%e-%c-%Y') from euribor where year(date)<='$y'  order by 12_mois limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$minimum_12mois = $res[0];
	$date_minimum_12mois = $res[1];

	$requete = "SELECT $ester_or_eonia, DATE_FORMAT(date,'%e-%c-%Y') from $ester_or_eonia where year(date)<='$y'  order by $ester_or_eonia desc,date limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$maximum_ester_or_eonia = $res[0];
	$date_maximum_ester_or_eonia = $res[1];

	$requete = "SELECT 1_mois, DATE_FORMAT(date,'%e-%c-%Y') from euribor where year(date)<='$y'  order by 1_mois desc,date limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$maximum_1mois = $res[0];
	$date_maximum_1mois = $res[1];

	$requete = "SELECT 3_mois, DATE_FORMAT(date,'%e-%c-%Y') from euribor where year(date)<='$y'  order by 3_mois desc,date limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$maximum_3mois = $res[0];
	$date_maximum_3mois = $res[1];

	$requete = "SELECT 6_mois, DATE_FORMAT(date,'%e-%c-%Y') from euribor where year(date)<='$y'  order by 6_mois  desc,date limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$maximum_6mois = $res[0];
	$date_maximum_6mois = $res[1];

	$requete = "SELECT 12_mois, DATE_FORMAT(date,'%e-%c-%Y') from euribor where year(date)<='$y'  order by 12_mois desc,date limit 1";
	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$res = mysqli_fetch_row($result);
	$maximum_12mois = $res[0];
	$date_maximum_12mois = $res[1];
	printf("<table class='taux striped centered'><caption>Positions depuis le 30/12/1998 (%s cotations)</caption><thead><tr><th>maturit&eacute;</th><th>rang/nb<br />(1/x=max)</th><th>plus haut</th><th>plus bas</th></tr></thead><tbody>", $nbcotations[0]);

	printf("<tr><td>$ester_or_eonia</td><td>%u/%u</td><td>%s&nbsp;%%<br />%s</td><td>%s&nbsp;%%<br />%s</td></tr>", $rang_ester_or_eonia, $total_rangs_ester_or_eonia, number_format($maximum_ester_or_eonia, 3, ',', ' '), $date_maximum_ester_or_eonia, number_format($minimum_ester_or_eonia, 3, ',', ' '), $date_minimum_ester_or_eonia);

	printf("<tr><td>1 mois</td><td>%u/%u</td><td>%s&nbsp;%%<br />%s</td><td>%s&nbsp;%%<br />%s</td></tr>", $rang_1mois, $total_rangs_1mois, number_format($maximum_1mois, 3, ',', ' '), $date_maximum_1mois, number_format($minimum_1mois, 3, ',', ' '), $date_minimum_1mois);

	printf("<tr><td>3 mois</td><td>%u/%u</td><td>%s&nbsp;%%<br />%s</td><td>%s&nbsp;%%<br />%s</td></tr>", $rang_3mois, $total_rangs_3mois, number_format($maximum_3mois, 3, ',', ' '), $date_maximum_3mois, number_format($minimum_3mois, 3, ',', ' '), $date_minimum_3mois);

	printf("<tr><td>6 mois</td><td>%u/%u</td><td>%s&nbsp;%%<br />%s</td><td>%s&nbsp;%%<br />%s</td></tr>", $rang_6mois, $total_rangs_6mois, number_format($maximum_6mois, 3, ',', ' '), $date_maximum_6mois, number_format($minimum_6mois, 3, ',', ' '), $date_minimum_6mois);

	printf("<tr><td>1 an</td><td>%u/%u</td><td>%s&nbsp;%%<br />%s</td><td>%s&nbsp;%%<br />%s</td></tr>", $rang_12mois, $total_rangs_12mois, number_format($maximum_12mois, 3, ',', ' '), $date_maximum_12mois, number_format($minimum_12mois, 3, ',', ' '), $date_minimum_12mois);

	echo '</tbody></table>';
}

/*
 * Retourne le dernier jour connu dans la base
 */
function tableau_variations_veille($annee, $connect, $afficher = 1, $modetec = 0)
{
	if ($annee > date("Y") || $annee < 1999) maildie("Ann&eacute;e incorrecte !");
	if ($annee >= 2020) {
		$ester_or_eonia = "ester";
	} else {
		$ester_or_eonia = "eonia";
	}
	if ($modetec == 0) {
		$requete = "SELECT * from euribor,$ester_or_eonia where euribor.date=$ester_or_eonia.date and YEAR(euribor.date)='$annee' order by euribor.date desc limit 2";
	} else {
		$requete = "SELECT * from TEC where YEAR(date)='$annee' order by date desc limit 2";
	}


	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$premier_jour_annee = mysqli_fetch_array($result);
	if (empty($premier_jour_annee['date'])) maildie("Il faut au minimum deux jours de cotations !");
	$dernier_jour_annee_avant = mysqli_fetch_array($result);
	if (empty($dernier_jour_annee_avant['date'])) maildie("Il faut au minimum deux jours de cotations !");


	if ($afficher == 1) {
		if ($modetec == 0)
			tableau_variations("Derni&egrave;res cotations", $premier_jour_annee, $dernier_jour_annee_avant);
		else
			tableau_variations_tec("Derni&egrave;res cotations", $premier_jour_annee, $dernier_jour_annee_avant);
	}
	return $premier_jour_annee;
}

function tableau_variations_tec($titre, $valeurs, $valeurs_avant)
{
	list($annee, $mois, $jour) = explode("-", $valeurs['date']);
	$diff = array();
	$diff_pc = array();
	$array_to_check = array('TEC10', 'TEC15', 'TEC20', 'TEC25', 'TEC30');
	foreach ($array_to_check as $unecle) {
		if (is_numeric($valeurs[$unecle]) && is_numeric($valeurs_avant[$unecle])) {
			$mydiff_pc = ((float)$valeurs[$unecle] - (float)$valeurs_avant[$unecle]) / abs((float)$valeurs_avant[$unecle]) * 100;
			$mydiff = ((float)$valeurs[$unecle] - (float)$valeurs_avant[$unecle]);
			if ($mydiff < 0) {
				$diff[] = '<span style="color:#ff0000">' . number_format($mydiff, 3, ',', ' ') . '</span>';
				$diff_pc[] = '<span style="color:#ff0000">' . number_format($mydiff_pc, 3, ',', ' ') . '</span>';
			} else {
				$diff[] = '<span style="color:#0000ff">+' . number_format($mydiff, 3, ',', ' ') . '</span>';
				$diff_pc[] = '<span style="color:#0000ff">+' . number_format($mydiff_pc, 3, ',', ' ') . '</span>';
			}
		} else {
			$diff[] = 'NA';
			$diff_pc[] = 'NA';
		}
	}
	printf("<table class='taux striped centered'><caption>$titre</caption><thead><tr><th>maturit&eacute;</th><th>%s</th><th>variation<br />veille</th><th>variation<br />veille en&nbsp;%%</th></tr></thead><tbody><tr><td>TEC10</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>TEC15</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>TEC20</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>TEC25</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>TEC30</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr></tbody></table>", date("d/m/Y", mktime(0, 0, 0, $mois, $jour, $annee)), number_format($valeurs['TEC10'], 3, ',', ' '), $diff[0], $diff_pc[0], number_format($valeurs['TEC15'], 3, ',', ' '), $diff[1], $diff_pc[1], number_format($valeurs['TEC20'], 3, ',', ' '), $diff[2], $diff_pc[2], number_format($valeurs['TEC25'], 3, ',', ' '), $diff[3], $diff_pc[3], number_format($valeurs['TEC30'], 3, ',', ' '), $diff[4], $diff_pc[4]);
}

function tableau_variations($titre, $valeurs, $valeurs_avant)
{
	list($annee, $mois, $jour) = explode("-", $valeurs['date']);
	$diff = array();
	$diff_pc = array();
	if ($annee >= 2020) {
		$ester_or_eonia = "ester";
	} else {
		$ester_or_eonia = "eonia";
	}
	$array_to_check = array($ester_or_eonia, '1_mois', '3_mois', '6_mois', '12_mois');
	foreach ($array_to_check as $unecle) {
		if (is_numeric($valeurs[$unecle]) && is_numeric($valeurs_avant[$unecle])) {
			if ($valeurs_avant[$unecle] != 0) {
				$mydiff_pc = ((float)$valeurs[$unecle] - (float)$valeurs_avant[$unecle]) / abs((float)$valeurs_avant[$unecle]) * 100;
			} else {
				$mydiff_pc = INF;
			}
			$mydiff = ((float)$valeurs[$unecle] - (float)$valeurs_avant[$unecle]);
			if ($mydiff < 0) {
				$diff[] = '<span style="color:#ff0000">' . number_format($mydiff, 3, ',', ' ') . '</span>';
				$diff_pc[] = '<span style="color:#ff0000">' . number_format($mydiff_pc, 3, ',', ' ') . '</span>';
			} else {
				$diff[] = '<span style="color:#0000ff">+' . number_format($mydiff, 3, ',', ' ') . '</span>';
				$diff_pc[] = '<span style="color:#0000ff">+' . number_format($mydiff_pc, 3, ',', ' ') . '</span>';
			}
		} else {
			$diff[] = 'NA';
			$diff_pc[] = 'NA';
		}
	}
	printf("<table class='striped centered'><caption>$titre</caption><thead><tr><th>maturit&eacute;</th><th>%s</th><th>variation<br />veille</th><th>variation<br />veille en&nbsp;%%</th></tr></thead><tbody><tr><td>$ester_or_eonia</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>1 mois</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>3 mois</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>6 mois</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>1 an</td><td>%s&nbsp;%%</td><td>%s</td><td>%s&nbsp;%%</td></tr></tbody></table>", date("d/m/Y", mktime(0, 0, 0, $mois, $jour, $annee)), number_format($valeurs[$ester_or_eonia], 3, ',', ' '), $diff[0], $diff_pc[0], number_format($valeurs['1_mois'], 3, ',', ' '), $diff[1], $diff_pc[1], number_format($valeurs['3_mois'], 3, ',', ' '), $diff[2], $diff_pc[2], number_format($valeurs['6_mois'], 3, ',', ' '), $diff[3], $diff_pc[3], number_format($valeurs['12_mois'], 3, ',', ' '), $diff[4], $diff_pc[4]);
}

function tableau_variations_premieres($annee, $connect, $modetec = 0)
{
	if ($annee > date("Y") || $annee < 1999) maildie("Ann&eacute;e incorrecte");
	if ($annee >= 2020) {
		$ester_or_eonia = "ester";
	} else {
		$ester_or_eonia = "eonia";
	}
	if ($modetec == 0) {
		$requete = "SELECT * from euribor,$ester_or_eonia where euribor.date=$ester_or_eonia.date and YEAR(euribor.date)='$annee' order by euribor.date limit 1";
	} else {
		$requete = "SELECT * from TEC where YEAR(date)='$annee' order by date limit 1";
	}


	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$premier_jour_annee = mysqli_fetch_array($result);
	if (empty($premier_jour_annee['date'])) maildie("Il faut au minimum deux jours de cotations !");

	$avant = $annee - 1;

	if ($modetec == 0) {
		$requete = "SELECT * from euribor,$ester_or_eonia where euribor.date=$ester_or_eonia.date and YEAR(euribor.date)='$avant' order by euribor.date desc limit 1";
	} else {
		$requete = "SELECT * from TEC where YEAR(date)='$avant' order by date desc limit 1";
	}

	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$dernier_jour_annee_avant = mysqli_fetch_array($result);
	if (empty($dernier_jour_annee_avant['date'])) maildie("Il faut au minimum deux jours de cotations !");

	if ($modetec == 0) {
		tableau_variations("Premi&egrave;res cotations", $premier_jour_annee, $dernier_jour_annee_avant);
	} else {
		tableau_variations_tec("Premi&egrave;res cotations", $premier_jour_annee, $dernier_jour_annee_avant);
	}
}


function tableau_variations_annuelles_tec($annee, $connect)
{
	if ($annee > date("Y") || $annee < 1999) maildie("Ann&eacute;e incorrecte");

	$requete = "SELECT * from TEC where YEAR(date)='$annee' order by date desc limit 1";

	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$valeurs = mysqli_fetch_array($result);
	if (empty($valeurs['date'])) maildie("Il faut au minimum deux jours de cotations !");

	$requete = "SELECT * from TEC where YEAR(date)='$annee' order by date limit 1";


	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$valeurs_avant = mysqli_fetch_array($result);
	if (empty($valeurs_avant['date'])) maildie("Il faut au minimum deux jours de cotations !");


	list($annee, $mois, $jour) = explode("-", $valeurs['date']);

	$diff = array();
	$diff_pc = array();
	$array_to_check = array('TEC10', 'TEC15', 'TEC20', 'TEC25', 'TEC30');
	foreach ($array_to_check as $unecle) {
		if (is_numeric($valeurs[$unecle]) && is_numeric($valeurs_avant[$unecle])) {
			if ($valeurs_avant[$unecle] != 0) {
				$mydiff_pc = ((float)$valeurs[$unecle] - (float)$valeurs_avant[$unecle]) / (float)abs($valeurs_avant[$unecle]) * 100;
			} else {
				$mydiff_pc = INF;
			}

			$mydiff = ((float)$valeurs[$unecle] - (float)$valeurs_avant[$unecle]);
			if ($mydiff < 0) {
				$diff[] = '<span style="color:#ff0000">' . number_format($mydiff, 3, ',', ' ') . '</span>';
				$diff_pc[] = '<span style="color:#ff0000">' . number_format($mydiff_pc, 3, ',', ' ') . '</span>';
			} else {
				$diff[] = '<span style="color:#0000ff">+' . number_format($mydiff, 3, ',', ' ') . '</span>';
				$diff_pc[] = '<span style="color:#0000ff">+' . number_format($mydiff_pc, 3, ',', ' ') . '</span>';
			}
		} else {
			$diff[] = 'NA';
			$diff_pc[] = 'NA';
		}
	}
	printf("<table class='striped centered taux'><caption>Variations annuelles</caption><thead><tr><th>maturit&eacute;</th><th>en valeur</th><th>en&nbsp;%%</th></tr></thead><tbody><tr><td>TEC10</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>TEC15</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>TEC20</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>TEC25</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>TEC30</td><td>%s</td><td>%s&nbsp;%%</td></tr></tbody></table>", $diff[0], $diff_pc[0], $diff[1], $diff_pc[1], $diff[2], $diff_pc[2], $diff[3], $diff_pc[3], $diff[4], $diff_pc[4]);
}


function tableau_variations_annuelles($annee, $connect)
{
	if ($annee > date("Y") || $annee < 1999) maildie("Ann&eacute;e incorrecte");

	if ($annee >= 2020) {
		$ester_or_eonia = "ester";
	} else {
		$ester_or_eonia = "eonia";
	}

	$requete = "SELECT * from euribor,$ester_or_eonia where euribor.date=$ester_or_eonia.date and YEAR(euribor.date)='$annee' order by euribor.date desc limit 1";


	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$valeurs = mysqli_fetch_array($result);
	if (empty($valeurs['date'])) maildie("Il faut au minimum deux jours de cotations !");

	$requete = "SELECT * from euribor,$ester_or_eonia where euribor.date=$ester_or_eonia.date and YEAR(euribor.date)='$annee' order by euribor.date limit 1";


	$result = mysqli_query($connect, $requete) or maildie(mysqli_error($connect));
	$valeurs_avant = mysqli_fetch_array($result);
	if (empty($valeurs_avant['date'])) maildie("Il faut au minimum deux jours de cotations !");

	list($annee, $mois, $jour) = explode("-", $valeurs['date']);
	$diff = array();
	$diff_pc = array();
	$array_to_check = array("$ester_or_eonia", '1_mois', '3_mois', '6_mois', '12_mois');
	foreach ($array_to_check as $unecle) {
		if (is_numeric($valeurs[$unecle]) && is_numeric($valeurs_avant[$unecle])) {
			if ($valeurs_avant[$unecle] != 0) {
				$mydiff_pc = ((float)$valeurs[$unecle] - (float)$valeurs_avant[$unecle]) / (float)abs($valeurs_avant[$unecle]) * 100;
			} else {
				$mydiff_pc = INF;
			}
			$mydiff = ((float)$valeurs[$unecle] - (float)$valeurs_avant[$unecle]);
			if ($mydiff < 0) {
				$diff[] = '<span style="color:#ff0000">' . number_format($mydiff, 3, ',', ' ') . '</span>';
				$diff_pc[] = '<span style="color:#ff0000">' . number_format($mydiff_pc, 3, ',', ' ') . '</span>';
			} else {
				$diff[] = '<span style="color:#0000ff">+' . number_format($mydiff, 3, ',', ' ') . '</span>';
				$diff_pc[] = '<span style="color:#0000ff">+' . number_format($mydiff_pc, 3, ',', ' ') . '</span>';
			}
		} else {
			$diff[] = 'NA';
			$diff_pc[] = 'NA';
		}
	}
	printf("<table class='striped centered taux'><caption>Variations annuelles</caption><thead><tr><th>maturit&eacute;</th><th>en valeur</th><th>en&nbsp;%%</th></tr></thead><tbody><tr><td>$ester_or_eonia</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>1 mois</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>3 mois</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>6 mois</td><td>%s</td><td>%s&nbsp;%%</td></tr><tr><td>1 an</td><td>%s</td><td>%s&nbsp;%%</td></tr></tbody></table>", $diff[0], $diff_pc[0], $diff[1], $diff_pc[1], $diff[2], $diff_pc[2], $diff[3], $diff_pc[3], $diff[4], $diff_pc[4]);
}
