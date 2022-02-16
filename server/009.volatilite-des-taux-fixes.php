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

		var chart1, chart2;
		var req = 'progs/taux/historique_json.php?mode=0&noDate=1';

		$.getJSON(req, function(data, status) {

				var pieData_tec10 = countOccByInterval(data["tec10"], 5);
				var pieData_tec15 = countOccByInterval(data["tec15"], 5);
				var pieData_tec20 = countOccByInterval(data["tec20"], 5);
				var pieData_tec25 = countOccByInterval(data["tec25"], 5);
				var pieData_tec30 = countOccByInterval(data["tec30"], 5);


				chart2 = new Highcharts.Chart({
					exporting: {
						buttons: {
							contextButton: {
								menuItems: [{
									text: "Enregistrer au format PNG",

									onclick: function() {
										$("body").addClass("loading");
										setTimeout(function() {
											chart2.exportChartLocal();
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
					title: {
						text: "Répartition des taux fixes"
					},

					subtitle: {
						text: "TEC10"
					},


					tooltip: {
						pointFormat: '{point.nbOcc} occurrences soit {point.percentage:.2f}%'
					},


					chart: {
						renderTo: "graphique2",
						type: "pie"
					},

					plotOptions: {
						pie: {
							dataLabels: {
								format: '{point.name}<br>{point.percentage:.2f}% ({point.nbOcc})'
							}
						}
					},

					series: [{
							id: "tec10",
							name: "TEC10",
							data: pieData_tec10
						},
						{
							id: "tec15",
							name: "TEC15",
							data: pieData_tec15,
							visible: false

						},
						{
							id: "tec20",
							name: "TEC20",
							data: pieData_tec20,
							visible: false
						},
						{
							id: "tec25",
							name: "TEC25",
							data: pieData_tec25,
							visible: false
						},
						{
							id: "tec30",
							name: "TEC30",
							data: pieData_tec30,
							visible: false
						}
					]

				});

				$("#message2").hide();


				$('[name="group1"]').on("change", function() {
					if (this.checked) {
						chart2.series.forEach(function(element) {

							if (element.options.id != this.value) {
								element.hide();
							}
						});
						var serie = chart2.get(this.value);
						serie.show();
						chart2.setTitle({
							text: "Répartition des taux fixes"
						}, {
							text: serie.name
						});
					}
				});

				$("#numParts3").on("input", function() {
					$("#numParts3Label").html($("#numParts3").val());
				});


				$("#numParts3Button").on("click", function() {

					var numPartsValue = $("#numParts3").val();
					$("#graphique2").hide();

					$("#message2").show(400, function() {
						pieData_tec10 = countOccByInterval(data["tec10"], parseInt(numPartsValue));
						pieData_tec15 = countOccByInterval(data["tec15"], parseInt(numPartsValue));
						pieData_tec20 = countOccByInterval(data["tec20"], parseInt(numPartsValue));
						pieData_tec25 = countOccByInterval(data["tec25"], parseInt(numPartsValue));
						pieData_tec30 = countOccByInterval(data["tec30"], parseInt(numPartsValue));

						chart2.get("tec10").setData(pieData_tec10, false);
						chart2.get("tec15").setData(pieData_tec15, false);
						chart2.get("tec20").setData(pieData_tec20, false);
						chart2.get("tec25").setData(pieData_tec25, false);
						chart2.get("tec30").setData(pieData_tec30, true);
						$("#message2").hide();
						$("#graphique2").show();
					});

				});

			})
			.fail(function(msg) {
				$("#message2").text("Erreur de communication avec le serveur...");
				alert(msg.responseText);
			});


		var req = 'progs/taux/frequences_json.php?mode=0';

		var jqxhr = $.getJSON(req, function(data, status) {

				chart1 = new Highcharts.Chart({
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

					credits: {
						enabled: true,
						href: "",
						text: "©2006-<?php echo date('Y'); ?> Laboratoire de Recherche pour le Développement Local"
					},

					legend: {
						enabled: true
					},

					chart: {
						renderTo: 'graphique1',
						defaultSeriesType: 'scatter',
						zoomType: 'xy'
					},

					title: {
						text: 'Volatilité des taux fixes'
					},


					xAxis: {
						title: {
							text: "Taux"
						}
					},

					tooltip: {
						formatter: function() {
							return "<b>" + this.series.name + " (" + this.series.options.nbOcc + " occurrences)</b><br />Taux : " + this.x.toFixed(3).replace('.', ',') + "<br />Nombre d'occurrences : " + this.y + " (soit " + (this.y / this.series.options.nbOcc * 100).toFixed(3).replace('.', ',') + "%)";
						}
					},

					yAxis: {
						title: {
							text: "Nombre d'occurrences"
						},
						min: 0,
						allowDecimals: false
					},

					series: [{
							name: "TEC-10",
							data: data["TEC10"]["data"],
							nbOcc: data["TEC10"]["nbOcc"]
						},
						{
							name: "TEC-15",
							data: data["TEC15"]["data"],
							nbOcc: data["TEC15"]["nbOcc"],
							visible: false
						},
						{
							name: "TEC-20",
							data: data["TEC20"]["data"],
							nbOcc: data["TEC20"]["nbOcc"],
							visible: false
						},
						{
							name: "TEC-25",
							data: data["TEC25"]["data"],
							nbOcc: data["TEC25"]["nbOcc"],
							visible: false
						},
						{
							name: "TEC-30",
							data: data["TEC30"]["data"],
							nbOcc: data["TEC30"]["nbOcc"],
							visible: false
						}
					],
					plotOptions: {
						scatter: {
							stickyTracking: false
						}
					}
				});
				$("#message1").hide();

			})
			.fail(function(msg) {
				$("#message1").text("Erreur de communication avec le serveur...");
				alert(msg.responseText);
			});
	});
</script>

<div class="row">
	<div id="fixes" class="col s12">

		<div class="row">
			<div class="col l9 s12">
				<div id="graphique1">
					<div id='message1' class="center-align">
						<img width="124" height="124" src="gears.gif" alt="loading">
						<p>Traitement des données en cours, merci de patienter...</p>
					</div>
				</div>
			</div>
			<div class="col l3 s12">
				Ce graphique illustre la notion de <i>volatilité des taux</i>. Chaque valeur du taux est indiquée en abscisse, et en ordonnée le nombre de jours (pas nécessairement consécutifs) durant lesquels ce taux est apparu. Les taux qui apparaissent moins de trois fois ne sont pas affichés. On peut remarquer que certaines valeurs ont tendance à se répéter plus que d'autres et au contraire qu'il existe des taux qui n'apparaissent quasiment jamais.
			</div>
		</div>

		<div class="section">

			<div class="row">
				<div class="col l4 s12" id="radioMode">
					<h5>Répartition des taux</h5>
					<p>Ce graphique présente la répartition des occurrences des taux à l'intérieur de N catégories de tailles égales (N est réglable avec le curseur). Par exemple si N=3, que le taux minimum de la période historique vaut 0 et que le taux maximum vaut 3, le graphique comportera au plus trois zones : de 0(inclus) à 1(exclus), de 1(inclus) à 2(exclus) et de 2(inclus) à 3(inclus). La taille de chaque zone correspondra aux nombres de taux qui se situent dans l'intervalle. Les zones vides ne sont pas affichées</p>
					<label for="numParts3">Nombre de regroupements voulus :</label>
					<span id="numParts3Label">5</span>
					<div class="range-field">
						<input type="range" style="width:100%" id="numParts3" min="2" max="10" value="5" />
						<input style="width:100%" type="button" id="numParts3Button" class="btn" value="Valider" />
					</div>
				</div>
				<div class="col l8 s12">
					<div id='message2' class="center-align">
						<img width="124" height="124" src="gears.gif" alt="loading">
						<p>Traitement des données en cours, merci de patienter...</p>
					</div>
					<div id="graphique2">
					</div>
					<div class="center-align">
						<label>
							<input name="group1" type="radio" class="with-gap" checked id="tec10" value="tec10" />
							<span>TEC10</span>
						</label>
						<label>
							<input name="group1" type="radio" class="with-gap" id="tec15" value="tec15" />
							<span>TEC15</span>
						</label>
						<label>
							<input name="group1" type="radio" class="with-gap" id="tec20" value="tec20" />
							<span>TEC20</span>
						</label>
						<label>
							<input name="group1" type="radio" class="with-gap" id="tec25" value="tec25" />
							<span>TEC25</span>
						</label>
						<label>
							<input name="group1" type="radio" class="with-gap" id="tec30" value="tec30" />
							<span>TEC30</span>
						</label>
					</div>
				</div>
			</div>
		</div>

	</div>

</div>