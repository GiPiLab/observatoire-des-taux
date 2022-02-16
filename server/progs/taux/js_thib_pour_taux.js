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

$(function () {
	Highcharts.setOptions({
		lang: {
			contextButtonTitle: "Exporter ce graphique...",
			downloadPNG: "T&eacute;l&eacute;charger le graphique au format PNG",
			downloadJPEG: "T&eacute;l&eacute;charger le graphique au format JPEG",
			downloadPDF: "T&eacute;l&eacute;charger le graphique au format PDF",
			downloadSVG: "T&eacute;l&eacute;charger le graphique au format SVG",
			printChart: "Imprimer le graphique",
			exportButtonTitle: "Exporter le graphique",
			loading: "Chargement...",
			decimalPoint: ',',
			thousandsSep: ' ',
			printButtonTitle: "Imprimer le graphique",
			rangeSelectorFrom: "Du",
			rangeSelectorTo: "Au",

			resetZoom: "Zoom 1:1",
			resetZoomTitle: "Zoomer au ratio 1:1",

			months: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin',
				'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
			shortMonths: ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juill.', 'août', 'sept.', 'oct.', 'nov.', 'dec.'],
			weekdays: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']
		}
	});
});


function countOccByInterval(array, nbDesiredParts) {
	if (!Array.isArray(array)) {
		console.log("Erreur array attendu");
		return;
	}

	if (nbDesiredParts !== parseInt(nbDesiredParts, 10)) {
		console.log("nbDesiredParts doit être un entier");
		return;
	}

	if (nbDesiredParts <= 0 || nbDesiredParts > 20) {
		console.log("nbDesiredParts hors bornes");
		return;
	}

	if (array.length === 0) {
		console.log("Tableau vide");
		return;
	}

	if (nbDesiredParts > array.length) {
		console.log("Plus de parties que d'éléments, fixe au nombre d'éléments");
		nbDesiredParts = array.length;
	}

	var max = new Big(Math.max.apply(null, array));
	var min = new Big(Math.min.apply(null, array));

	var decimalInterval = max.minus(min).div(nbDesiredParts);

	var nbOcc = new Array();

	var arraySorted = array.sort(function (a, b) { return a - b; });

	var intervalLow = new Big(min);
	var intervalHigh = intervalLow.plus(decimalInterval);

	var count = 1;

	while (intervalHigh.lte(max)) {

		if (count === nbDesiredParts)
			intervalHigh = max;

		var categ = {
			min: parseFloat(intervalLow),
			max: parseFloat(intervalHigh),
			name: "de " + intervalLow.toFixed(3).replace('.', ',') + " à " + intervalHigh.toFixed(3).replace('.', ','),
			nbOcc: 0
		};

		if (count !== nbDesiredParts) {
			arraySorted.forEach(function (element) {
				if (intervalLow.lte(element) && intervalHigh.gt(element)) {
					categ.nbOcc++;
				}
			});
		}

		//Derniere partie, on arrondit en prenant high=max et on prend <= au max
		else {
			arraySorted.forEach(function (element) {
				if (intervalLow.lte(element) && intervalHigh.gte(element)) {
					categ.nbOcc++;
				}
			});
		}

		if (categ.nbOcc > 0) {
			var occBig = new Big(categ.nbOcc);
			categ.y = parseFloat(occBig.div(array.length).times(100));
			nbOcc.push(categ);
		}

		intervalLow = intervalHigh;
		intervalHigh = intervalHigh.plus(decimalInterval);
		count++;
	}
	return nbOcc;

}
