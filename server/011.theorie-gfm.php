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
?>
<script type="text/javascript">
	$(document).ready(function() {

		var req = 'progs/taux/historique_json.php?mode=2&noMinMax=1';

		var jqxhr = $.getJSON(req, function(data, status) {

				var chart1 = new Highcharts.StockChart({



					legend: {
						enabled: true
					},

					navigator: {
						height: 30,
					},

					rangeSelector: {
						inputEnabled: true,
						inputDateFormat: '%d %b %Y',

						buttons: [{
							type: 'month',
							count: 1,
							text: '1m'
						}, {
							type: 'month',
							count: 6,
							text: '6m'
						}, {
							type: 'year',
							count: 1,
							text: '1an'
						}, {
							type: 'all',
							count: 1,
							text: 'Tout'
						}],
						selected: 3
					},

					exporting: {
						buttons: {
							contextButton: {
								menuItems: [{
									text: "Enregistrer au format PNG",

									onclick: function() {

										$("body").addClass("loading");
										setTimeout(function() {
											chart1.exportChartLocal();
											$("body").removeClass("loading");
										}, 1000);

									}

								}],
							}
						}
					},

					chart: {
						renderTo: 'graphique',
						type: "area",

						zoomType: 'x'
					},

					title: {
						text: 'Théorie GFM©'
					},

					scrollbar: {
						enabled: false,
						height: 8
					},
					xAxis: {
						type: 'datetime',
						minRange: 30 * 24 * 3600000,
						ordinal: false,
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '%H:%M',
							day: '%e %b',
							week: '%e %b',
							month: '%b %y',
							year: '%Y'
						}
					},

					yAxis: {
						title: {
							text: "Taux"
						},
						endOnTick: false,
						startOnTick: false,
						opposite: false
					},

					tooltip: {
						shared: true,
						split: false,
						valueDecimals: 3,
						dateTimeLabelFormats: {
							millisecond: '%A %e %b %Y, %H:%M:%S.%L',
							second: '%A %e %b %Y, %H:%M:%S',
							minute: '%A %e %b %Y, %H:%M',
							hour: '%A %e %b %Y, %H:%M',
							day: '%A %e %b %Y',
							week: 'Semaine du %e %B %Y',
							month: '%B %Y',
							year: '%Y'
						}

					},
					series: [{
							name: "TEC10",
							data: data["tec10"],
						},
						{
							name: "TEC15",
							data: data["tec15"],
							visible: false

						},
						{
							name: "TEC20",
							data: data["tec20"],
							visible: false
						},
						{
							name: "TEC25",
							data: data["tec25"],
							visible: false
						},
						{
							name: "TEC30",
							data: data["tec30"],
							visible: false
						},
						{
							name: "Eur-1m",
							data: data["eur1m"],
						},
						{
							name: "Eur-3m",
							data: data["eur3m"],
							visible: false
						},
						{
							name: "Eur-6m",
							data: data["eur6m"],
							visible: false
						},
						{
							name: "Eur-12m",
							data: data["eur12m"],
							visible: false
						}
					],


					plotOptions: {
						area: {
							fillOpacity: 0.4,
							lineWidth: 1,

							dataGrouping: {
								groupPixelWidth: 10,
								dateTimeLabelFormats: {
									millisecond: ['%A %e %B %Y', '%A %e %B %Y', ''],
									second: ['%A %e %B %Y', '%A %e %B %Y', ''],
									minute: ['%A %e %B %Y', '%A %e %B %Y', ''],
									hour: ['%A %e %B %Y', '%A %e %B %Y', ''],
									day: ['%A %e %B %Y', '%A %e %B', '-%A %e %B %Y'],
									week: ['Semaine du %e %B %Y', '%e %B', '-%e %B %Y'],
									month: ['%B %Y', '%B', '-%B %Y'],
									year: ['%Y', '%Y', '-%Y']
								}

							},
							states: {
								hover: {
									enabled: false
								}
							},
							marker: {
								states: {
									hover: {
										enabled: false
									}
								}
							}
						}
					}
				});

				$("#message").hide();

			})
			.fail(function(msg) {
				$("#message").text("Erreur de communication avec le serveur...");
				alert(msg.responseText);
			});
	});
</script>

<div class="row">
	<div class="col s12">
		<div id="graphique">
			<div id='message' class="center-align">
				<img width="124" height="124" src="gears.gif" alt="loading">
				<p>Traitement des données en cours, merci de patienter...</p>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col s12">
		<p>Le tracé en parallèle du TEC 10 et de l'EURIBOR 1 mois ne laisse aucun doute sur le surcoût que doit assumer, immédiatement ou à terme, tout souscripteur d'un emprunt à taux fixe adossé à cet indice. Il convient d'observer que la date de souscription d'un emprunt se situe ponctuellement sur l'une ou l'autre courbe. Le point, correspondant à la date de la souscription d'un emprunt à taux fixe, doit être prolongé par une droite, parallèle à l'axe des abscisses et de longueur égale à la durée de l'emprunt souscrit. Cette droite indique la permanence du taux pendant toute la durée de l'emprunt.</p>


		<p>Quelle que soit la position du point d'origine choisi sur la courbe TEC 10, la surface délimitée par l'axe des abscisses et la droite précitée contient le plus souvent le tracé de la courbe EURIBOR.</p>

		<p>Les périodes au cours desquelles l'emprunteur en taux fixe est gagnant (EURIBOR au-dessus de la droite) ne compensent jamais les périodes au cours desquelles il est perdant, lorsque la courbe EURIBOR est en dessous de la droite.</p>

	</div>
</div>
