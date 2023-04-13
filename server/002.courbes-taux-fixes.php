<?php if (!defined('GIPISITE')) exit();
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

require_once "progs/taux/fonctions_taux.php";

if (!isset($_GET['annee'])) $annee = date('Y');
else $annee = htmlentities($_GET['annee']);

if (!is_numeric($annee) || (int)$annee > date("Y") || (int)$annee < 1999) die("Année incorrecte !");

$connect = connect_base();
$annee = (int)$annee;

?>



<div class="row">
	<div class="col m6 s12">
		<h4>Courbes des taux fixes, exercice&nbsp;<?php echo $annee; ?></h4>
	</div>
	<div style="margin-top:1em" class="col m6 s12">
		<form id="formRange" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="GET">
			<!-- hardcoded page number, ugly but easy xss prevention -->
			<input type="hidden" name="page" value="2" />
			<label>Choisir l'exercice :</label>

			<span id="valueRange"><?php echo $annee; ?></span>
			<div class="range-field">
				<input style="width:100%" type="range" min="1999" max="<?php echo date('Y'); ?>" value="<?php echo $annee; ?>" onInput="document.getElementById('valueRange').innerHTML=this.value;" name="annee">
				<input class="btn" style="width:100%" type="submit" value="Valider" />
			</div>
		</form>
	</div>
</div>

<?php

echo "<div class='row'>";


echo "<div class='col l6 s12'><div id='graphiqueFixe'>";

echo " <div id='messageFixe' class='center-align'><img width='124' height='124' src='gears.gif' alt='loading'><p>Traitement des données en cours, merci de patienter...</p></div></div></div>";

echo "<div class='col l6 s12'>";
$dernier_connu = tableau_variations_veille($annee, $connect, 1, 1);
echo "</div></div>";

echo "<div class='row'><div class='col l6 m12 s12'>";
tableau_variations_premieres($annee, $connect, 1);
echo "</div><div class='col l6 m12 s12'><br>";
tableau_variations_annuelles_tec($annee, $connect);
echo "</div></div>";
?>


<script type="text/javascript">
	$(document).ready(function() {

		var chartFixe;

		/*Taux fixes*/

		req = 'progs/taux/graphique_fiche_JSON.php?mode=1&annee=<?php echo $annee; ?>';

		$.getJSON(req, function(data, status) {


			if (Object.keys(data).length != 2) {
				alert("Erreur il faut deux jours de cotation");
				return;
			}

			chartFixe = new Highcharts.chart({
				exporting: {
					buttons: {
						contextButton: {
							menuItems: [{
								text: "Enregistrer au format PNG",

								onclick: function() {
									$("body").addClass("loading");
									setTimeout(function() {
										chartFixe.exportChartLocal();
										$("body").removeClass("loading");
									}, 1000);
								}
							}],
						}
					}
				},


				chart: {
					renderTo: 'graphiqueFixe'
				},

				title: {
					text: 'Évolution de la courbe des taux'
				},

				xAxis: {
					categories: ['TEC10', 'TEC15', 'TEC20', 'TEC25', 'TEC30']
				},

				yAxis: {
					title: {
						text: "Taux"
					}
				},

				tooltip: {
					shared: true,
					valueDecimals: 3
				},


				plotOptions: {
					line: {
						dataLabels: {
							enabled: true,
							format: '{point.y:,.3f}'
						}
					}
				},

				series: [{
						name: data["dernierJourConnu"]["date"],
						data: [data["dernierJourConnu"]["TEC10"], data["dernierJourConnu"]["TEC15"], data["dernierJourConnu"]["TEC20"], data["dernierJourConnu"]["TEC25"], data["dernierJourConnu"]["TEC30"]]
					},
					{
						name: data["premierJourAnnee"]["date"],
						data: [data["premierJourAnnee"]["TEC10"], data["premierJourAnnee"]["TEC15"], data["premierJourAnnee"]["TEC20"],
							data["premierJourAnnee"]["TEC25"], data["premierJourAnnee"]["TEC30"]
						]
					}
				]
			});

			$("#messageFixe").hide();

		}).fail(function(msg) {
			$("#messageFixe").text("Erreur de communication avec le serveur...");
			alert(msg.responseText);
		});


	});
</script>
