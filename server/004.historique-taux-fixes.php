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
<script type="text/javascript" language="javascript">
	$(document).ready(function() {



		var chartFixe;

		var req = 'progs/taux/historique_json.php?mode=0';

		$.getJSON(req, function(data, status) {

				chartFixe = new Highcharts.StockChart({
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

					credits: {
						enabled: true,
						href: "",
						text: "©2006-<?php echo date('Y'); ?> Laboratoire de Recherche pour le Développement Local"
					},

					legend: {
						enabled: true
					},

					navigator: {
						height: 30,
					},

					rangeSelector: {
						inputEnabled: true,
						inputDateFormat: '%d %b %Y',
						/*inputDateFormat:'%d-%m-%Y',
        inputEditDateFormat:'%d-%m-%Y',
	inputDateParser:function(value)
	{
		value=value.split(/-/);
		ladate=Date.UTC(parseInt(value[2]),parseInt(value[1])-1,parseInt(value[0]));
		return ladate;
	},*/
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

					chart: {
						renderTo: 'graphiqueFixe',
						zoomType: 'x'
					},

					title: {
						text: 'Historique des taux fixes'
					},

					scrollbar: {
						enabled: false,
						height: 8
					},
					xAxis: {
						type: 'datetime',
						ordinal: false,
						minRange: 30 * 24 * 3600000,
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
							name: "Minmax TEC10",
							data: data["tec10RANGE"],
							visible: false,
							type: "arearange",
							color: Highcharts.getOptions().colors[0]
						},
						{
							name: "TEC15",
							data: data["tec15"],
							visible: false

						},
						{
							name: "Minmax TEC15",
							data: data["tec15RANGE"],
							visible: false,
							type: "arearange",
							color: Highcharts.getOptions().colors[1]
						},
						{
							name: "TEC20",
							data: data["tec20"],
							visible: false
						},
						{
							name: "Minmax TEC20",
							data: data["tec20RANGE"],
							visible: false,
							type: "arearange",
							color: Highcharts.getOptions().colors[2]
						},
						{
							name: "TEC25",
							data: data["tec25"],
							visible: false
						},
						{
							name: "Minmax TEC25",
							data: data["tec25RANGE"],
							visible: false,
							type: "arearange",
							color: Highcharts.getOptions().colors[3]
						},
						{
							name: "TEC30",
							data: data["tec30"],
							visible: false
						},
						{
							name: "Minmax TEC30",
							data: data["tec30RANGE"],
							visible: false,
							type: "arearange",
							color: Highcharts.getOptions().colors[4]
						}
					],


					plotOptions: {
						series: {

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
							arearange: {
								step: "left",
								lineWidth: 0,
								fillOpacity: 0.3,
								states: {
									hover: {
										enabled: false
									}
								}
							},

							line: {
								lineWidth: 2
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

				$("#messageFixe").hide();

			})
			.fail(function(msg) {
				$("#messageFixe").text("Erreur de communication avec le serveur...");
				alert(msg.responseText);
			});


	});
</script>

<div class="row">

	<div id="fixes" class="col s12">
		<div id="graphiqueFixe">
			<div id='messageFixe' class="center-align">
				<img width="124" height="124" src="gears.gif" alt="loading">
				<p>Traitement des données en cours, merci de patienter...</p>
			</div>
		</div>
		<p>Ce graphique présente l'évolution des taux depuis leur création. Selon le niveau de zoom les taux sont regroupés par mois ou par semaine et la valeur affichée correspond à leurs moyennes mensuelles (ou hebdomadaires). Les courbes "Minmax" illustrent l'évolution des extrêmes de chaque taux au fil du temps. Le décalage éventuel entre les courbes Minmax et les extrêmes affichés des taux est dû au regroupement des valeurs expliqué précédemment. A un instant donné, la valeur du taux et les deux valeurs de la courbe Minmax associée représentent la pression conjonctuelle du jour pour ce taux. Ainsi la courbe historique munie de sa courbe Minmax peut être vue comme une représentation de l'évolution de la pression conjoncturelle au fil du temps&nbsp;!</p>
	</div>

</div>