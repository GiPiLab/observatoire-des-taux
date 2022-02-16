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


//header("Content-type: text/json");

require "fonctions_taux.php";

//0=TEC, 1=euribor/eonia, 2=les deux
$mode = 0;

$noDate = false;

$noMinMax = false;

//Ne pas calculer les mini/maxi pour les enveloppes
if (isset($_REQUEST['noMinMax']))
	$noMinMax = true;

//Ne pas ajouter les dates, juste les taux les uns derrière les autres
if (isset($_REQUEST['noDate'])) {
	$noDate = true;
	$noMinMax = true;
}


if (isset($_REQUEST['mode'])) {
	if ($_REQUEST['mode'] != 0 && $_REQUEST['mode'] != 1 && $_REQUEST['mode'] != 2) $mode = 0;
	else $mode = (int)$_REQUEST['mode'];
}

setLocale(LC_ALL, 'fr_FR');
date_default_timezone_set("UTC");

$alldata = array();

$connect = connect_base();


if ($mode == 0 || $mode == 2) {

	$requete = "select * from TEC order by date";

	$result = mysqli_query($connect, $requete) or die(mysqli_error($connect));

	$tecs10 = array();
	$tecs15 = array();
	$tecs20 = array();
	$tecs25 = array();
	$tecs30 = array();

	if (!$noMinMax) {
		$tecs10AREARANGE = array();
		$tecs15AREARANGE = array();
		$tecs20AREARANGE = array();
		$tecs25AREARANGE = array();
		$tecs30AREARANGE = array();

		$changeTEC10Range = false;
		$changeTEC15Range = false;
		$changeTEC20Range = false;
		$changeTEC25Range = false;
		$changeTEC30Range = false;

		$tec10MIN = 1000;
		$tec10MAX = -1000;
		$tec15MIN = 1000;
		$tec15MAX = -1000;
		$tec20MIN = 1000;
		$tec20MAX = -1000;
		$tec25MIN = 1000;
		$tec25MAX = -1000;
		$tec30MIN = 1000;
		$tec30MAX = -1000;
	}

	$total = mysqli_num_rows($result);
	$count = 0;
	while ($donnees = mysqli_fetch_array($result)) {
		if (!$noMinMax) {
			if ($donnees['TEC10'] != null) {
				if ($tec10MIN > $donnees['TEC10']) {
					$changeTEC10Range = true;
					$tec10MIN = $donnees['TEC10'];
				}
				if ($tec10MAX < $donnees['TEC10']) {
					$changeTEC10Range = true;
					$tec10MAX = $donnees['TEC10'];
				}
			}

			if ($donnees['TEC15'] != null) {
				if ($tec15MIN > $donnees['TEC15']) {
					$changeTEC15Range = true;
					$tec15MIN = $donnees['TEC15'];
				}
				if ($tec15MAX < $donnees['TEC15']) {
					$changeTEC15Range = true;
					$tec15MAX = $donnees['TEC15'];
				}
			}
			if ($donnees['TEC20'] != null) {
				if ($tec20MIN > $donnees['TEC20']) {
					$changeTEC20Range = true;
					$tec20MIN = $donnees['TEC20'];
				}
				if ($tec20MAX < $donnees['TEC20']) {
					$changeTEC20Range = true;
					$tec20MAX = $donnees['TEC20'];
				}
			}
			if ($donnees['TEC25'] != null) {
				if ($tec25MIN > $donnees['TEC25']) {
					$changeTEC25Range = true;
					$tec25MIN = $donnees['TEC25'];
				}
				if ($tec25MAX < $donnees['TEC25']) {
					$changeTEC25Range = true;
					$tec25MAX = $donnees['TEC25'];
				}
			}
			if ($donnees['TEC30'] != null) {
				if ($tec30MIN > $donnees['TEC30']) {
					$changeTEC30Range = true;
					$tec30MIN = $donnees['TEC30'];
				}
				if ($tec30MAX < $donnees['TEC30']) {
					$changeTEC30Range = true;
					$tec30MAX = $donnees['TEC30'];
				}
			}
		}

		if (!$noDate) {
			$tec10 = array();
			$tec15 = array();
			$tec20 = array();
			$tec25 = array();
			$tec30 = array();
		}

		if (!$noMinMax) {
			$tec10Range = array();
			$tec15Range = array();
			$tec20Range = array();
			$tec25Range = array();
			$tec30Range = array();
		}

		list($y, $m, $d) = explode('-', $donnees['date']);
		$date_timestamp = mktime(0, 0, 0, $m, $d, $y) * 1000;
		if (!$noDate) {
			$tec10[] = $date_timestamp;
			$tec15[] = $date_timestamp;
			$tec20[] = $date_timestamp;
			$tec25[] = $date_timestamp;
			$tec30[] = $date_timestamp;
		}

		if (!$noMinMax) {
			$tec10Range[] = $date_timestamp;
			$tec10Range[] = $tec10MIN <= $tec10MAX ? $tec10MIN : null;
			$tec10Range[] = $tec10MIN <= $tec10MAX ? $tec10MAX : null;
			$tec15Range[] = $date_timestamp;
			$tec15Range[] = $tec15MIN <= $tec15MAX ? $tec15MIN : null;
			$tec15Range[] = $tec15MIN <= $tec15MAX ? $tec15MAX : null;
			$tec20Range[] = $date_timestamp;
			$tec20Range[] = $tec20MIN <= $tec20MAX ? $tec20MIN : null;
			$tec20Range[] = $tec20MIN <= $tec20MAX ? $tec20MAX : null;
			$tec25Range[] = $date_timestamp;
			$tec25Range[] = $tec25MIN <= $tec25MAX ? $tec25MIN : null;
			$tec25Range[] = $tec25MIN <= $tec25MAX ? $tec25MAX : null;
			$tec30Range[] = $date_timestamp;
			$tec30Range[] = $tec30MIN <= $tec30MAX ? $tec30MIN : null;
			$tec30Range[] = $tec30MIN <= $tec30MAX ? $tec30MAX : null;
		}

		if (!$noDate) {
			$tec10[] = $donnees['TEC10'];
			$tec15[] = $donnees['TEC15'];
			$tec20[] = $donnees['TEC20'];
			$tec25[] = $donnees['TEC25'];
			$tec30[] = $donnees['TEC30'];
		} else {
			$tec10 = $donnees['TEC10'];
			$tec15 = $donnees['TEC15'];
			$tec20 = $donnees['TEC20'];
			$tec25 = $donnees['TEC25'];
			$tec30 = $donnees['TEC30'];
		}

		if ($tec10 != null)
			$alldata["tec10"][] = $tec10;
		if ($tec15 != null)
			$alldata["tec15"][] = $tec15;
		if ($tec20 != null)
			$alldata["tec20"][] = $tec20;
		if ($tec25 != null)
			$alldata["tec25"][] = $tec25;
		if ($tec30 != null)
			$alldata["tec30"][] = $tec30;
		if (!$noMinMax) {
			$count++;
			if ($changeTEC10Range == true || $count == $total) {
				$alldata["tec10RANGE"][] = $tec10Range;
				$changeTEC10Range = false;
			}
			if ($changeTEC15Range == true || $count == $total) {
				$alldata["tec15RANGE"][] = $tec15Range;
				$changeTEC15Range = false;
			}
			if ($changeTEC20Range == true || $count == $total) {
				$alldata["tec20RANGE"][] = $tec20Range;
				$changeTEC20Range = false;
			}
			if ($changeTEC25Range == true || $count == $total) {
				$alldata["tec25RANGE"][] = $tec25Range;
				$changeTEC25Range = false;
			}
			if ($changeTEC30Range == true || $count == $total) {
				$alldata["tec30RANGE"][] = $tec30Range;
				$changeTEC30Range = false;
			}
		}
	}
}


if ($mode == 1 || $mode == 2) {
	$requete = "select * from eonia order by date";

	$result = mysqli_query($connect, $requete) or die("Requete impossible : " . mysqli_error($connect));

	if (!$noMinMax) {
		$eoniaMIN = 1000;
		$eoniaMAX = -1000;
		$total = mysqli_num_rows($result);
		$count = 0;
		$changeEoniaRange = false;
	}

	while ($donnees = mysqli_fetch_array($result)) {
		if (!$noDate)
			$eonia = array();
		if (!$noMinMax) {
			$eoniaRange = array();
			if ($donnees['eonia'] != null) {
				if ($eoniaMIN > $donnees['eonia']) {
					$changeEoniaRange = true;
					$eoniaMIN = $donnees['eonia'];
				}
				if ($eoniaMAX < $donnees['eonia']) {
					$eoniaMAX = $donnees['eonia'];
					$changeEoniaRange = true;
				}
			}
		}

		list($y, $m, $d) = explode('-', $donnees['date']);
		$date_timestamp = mktime(0, 0, 0, $m, $d, $y) * 1000;
		if (!$noDate) {
			$eonia[] = $date_timestamp;
			$eonia[] = $donnees['eonia'];
		} else {
			$eonia = $donnees['eonia'];
		}
		if (!$noMinMax) {
			$count++;
			if ($changeEoniaRange == true || $count == $total) {
				$eoniaRange[] = $date_timestamp;
				$eoniaRange[] = $eoniaMIN <= $eoniaMAX ? $eoniaMIN : null;
				$eoniaRange[] = $eoniaMIN <= $eoniaMAX ? $eoniaMAX : null;
				$alldata['eoniaRANGE'][] = $eoniaRange;
				$changeEoniaRange = false;
			}
		}
		if ($eonia != null)
			$alldata['eonia'][] = $eonia;
	}

	//ESTER
	$requete = "select * from ester order by date";

	$result = mysqli_query($connect, $requete) or die("Requete impossible : " . mysqli_error($connect));

	if (!$noMinMax) {
		$esterMIN = 1000;
		$esterMAX = -1000;
		$total = mysqli_num_rows($result);
		$count = 0;
		$changeEsterRange = false;
	}

	while ($donnees = mysqli_fetch_array($result)) {
		if (!$noDate)
			$ester = array();
		if (!$noMinMax) {
			$esterRange = array();
			if ($donnees['ester'] != null) {
				if ($esterMIN > $donnees['ester']) {
					$changeEsterRange = true;
					$esterMIN = $donnees['ester'];
				}
				if ($esterMAX < $donnees['ester']) {
					$esterMAX = $donnees['ester'];
					$changeEsterRange = true;
				}
			}
		}

		list($y, $m, $d) = explode('-', $donnees['date']);
		$date_timestamp = mktime(0, 0, 0, $m, $d, $y) * 1000;
		if (!$noDate) {
			$ester[] = $date_timestamp;
			$ester[] = $donnees['ester'];
		} else {
			$ester = $donnees['ester'];
		}
		if (!$noMinMax) {
			$count++;
			if ($changeEsterRange == true || $count == $total) {
				$esterRange[] = $date_timestamp;
				$esterRange[] = $esterMIN <= $esterMAX ? $esterMIN : null;
				$esterRange[] = $esterMIN <= $esterMAX ? $esterMAX : null;
				$alldata['esterRANGE'][] = $esterRange;
				$changeEsterRange = false;
			}
		}
		if ($ester != null)
			$alldata['ester'][] = $ester;
	}

	$requete = "select * from euribor order by date";

	$result = mysqli_query($connect, $requete) or die("Requete impossible : " . mysqli_error($connect));

	$total = mysqli_num_rows($result);
	$count = 0;


	if (!$noMinMax) {
		$changeEur1mRange = false;
		$changeEur3mRange = false;
		$changeEur6mRange = false;
		$changeEur12mRange = false;
		$eur1MIN = 1000;
		$eur1MAX = -1000;
		$eur3MIN = 1000;
		$eur3MAX = -1000;
		$eur6MIN = 1000;
		$eur6MAX = -1000;
		$eur12MIN = 1000;
		$eur12MAX = -1000;
	}

	while ($donnees = mysqli_fetch_array($result)) {
		if (!$noDate) {
			$euribor1 = array();
			$euribor3 = array();
			$euribor6 = array();
			$euribor12 = array();
		}

		if (!$noMinMax) {
			$eur1mRange = array();
			$eur3mRange = array();
			$eur6mRange = array();
			$eur12mRange = array();

			if ($donnees['1_mois'] != null) {
				if ($eur1MIN > $donnees['1_mois']) {
					$changeEur1mRange = true;
					$eur1MIN = $donnees['1_mois'];
				}
				if ($eur1MAX < $donnees['1_mois']) {
					$changeEur1mRange = true;
					$eur1MAX = $donnees['1_mois'];
				}
			}
			if ($donnees['3_mois'] != null) {
				if ($eur3MIN > $donnees['3_mois']) {
					$changeEur3mRange = true;
					$eur3MIN = $donnees['3_mois'];
				}
				if ($eur3MAX < $donnees['3_mois']) {
					$changeEur3mRange = true;
					$eur3MAX = $donnees['3_mois'];
				}
			}
			if ($donnees['6_mois'] != null) {
				if ($eur6MIN > $donnees['6_mois']) {
					$changeEur6mRange = true;
					$eur6MIN = $donnees['6_mois'];
				}
				if ($eur6MAX < $donnees['6_mois']) {
					$changeEur6mRange = true;
					$eur6MAX = $donnees['6_mois'];
				}
			}
			if ($donnees['12_mois'] != null) {
				if ($eur12MIN > $donnees['12_mois']) {
					$changeEur12mRange = true;
					$eur12MIN = $donnees['12_mois'];
				}
				if ($eur12MAX < $donnees['12_mois']) {
					$changeEur12mRange = true;
					$eur12MAX = $donnees['12_mois'];
				}
			}
		}

		list($y, $m, $d) = explode('-', $donnees['date']);
		$date_timestamp = mktime(0, 0, 0, $m, $d, $y) * 1000;
		if (!$noDate) {
			$euribor1[] = $date_timestamp;
			$euribor3[] = $date_timestamp;
			$euribor6[] = $date_timestamp;
			$euribor12[] = $date_timestamp;
			$euribor1[] = $donnees['1_mois'];
			$euribor3[] = $donnees['3_mois'];
			$euribor6[] = $donnees['6_mois'];
			$euribor12[] = $donnees['12_mois'];
		} else {
			$euribor1 = $donnees['1_mois'];
			$euribor3 = $donnees['3_mois'];
			$euribor6 = $donnees['6_mois'];
			$euribor12 = $donnees['12_mois'];
		}
		if (!$noMinMax) {
			$count++;
			$eur1mRange[] = $date_timestamp;
			$eur1mRange[] = $eur1MIN <= $eur1MAX ? $eur1MIN : null;
			$eur1mRange[] = $eur1MIN <= $eur1MAX ? $eur1MAX : null;
			$eur3mRange[] = $date_timestamp;
			$eur3mRange[] = $eur3MIN <= $eur3MAX ? $eur3MIN : null;
			$eur3mRange[] = $eur3MIN <= $eur3MAX ? $eur3MAX : null;
			$eur6mRange[] = $date_timestamp;
			$eur6mRange[] = $eur6MIN <= $eur6MAX ? $eur6MIN : null;
			$eur6mRange[] = $eur6MIN <= $eur6MAX ? $eur6MAX : null;
			$eur12mRange[] = $date_timestamp;
			$eur12mRange[] = $eur12MIN <= $eur12MAX ? $eur12MIN : null;
			$eur12mRange[] = $eur12MIN <= $eur12MAX ? $eur12MAX : null;
			if ($changeEur1mRange == true || $count == $total) {
				$alldata["eur1mRANGE"][] = $eur1mRange;
				$changeEur1mRange = false;
			}

			if ($changeEur3mRange == true || $count == $total) {
				$alldata["eur3mRANGE"][] = $eur3mRange;
				$changeEur3mRange = false;
			}
			if ($changeEur6mRange == true || $count == $total) {
				$alldata["eur6mRANGE"][] = $eur6mRange;
				$changeEur6mRange = false;
			}
			if ($changeEur12mRange == true || $count == $total) {
				$alldata["eur12mRANGE"][] = $eur12mRange;
				$changeEur12mRange = false;
			}
		}
		if ($euribor1 != null)
			$alldata["eur1m"][] = $euribor1;
		if ($euribor3 != null)
			$alldata["eur3m"][] = $euribor3;
		if ($euribor6 != null)
			$alldata["eur6m"][] = $euribor6;
		if ($euribor12 != null)
			$alldata["eur12m"][] = $euribor12;
	}
}

mysqli_close($connect);

echo json_encode($alldata, JSON_NUMERIC_CHECK);
