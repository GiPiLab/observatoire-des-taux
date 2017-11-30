$(function() {
Highcharts.setOptions({
      lang:{
	contextButtonTitle:"Exporter ce graphique...",
	downloadPNG:"T&eacute;l&eacute;charger le graphique au format PNG",
	downloadJPEG:"T&eacute;l&eacute;charger le graphique au format JPEG",
	downloadPDF:"T&eacute;l&eacute;charger le graphique au format PDF",
	downloadSVG:"T&eacute;l&eacute;charger le graphique au format SVG",
	printChart:"Imprimer le graphique",
	exportButtonTitle:"Exporter le graphique",
	loading:"Chargement...",
	decimalPoint:',',
	thousandsSep:' ',
	printButtonTitle:"Imprimer le graphique",
	rangeSelectorFrom:"Du",
	rangeSelectorTo:"Au",
	
	resetZoom:"Zoom 1:1",
	resetZoomTitle:"Zoomer au ratio 1:1",

	months: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 
	'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
	shortMonths:['janv.','févr.','mars','avr.','mai','juin','juill.','août','sept.','oct.','nov.','dec.'],
	weekdays: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']
	}
});
});


function countOccByInterval(array,nbDesiredParts)
{
	if(!Array.isArray(array))
	{
		console.log("Erreur array attendu");
		return;
	}

	if(nbDesiredParts!==parseInt(nbDesiredParts,10))
	{
		console.log("nbDesiredParts doit être un entier");
		return;
	}

	if(nbDesiredParts<=0 || nbDesiredParts>20)
	{
		console.log("nbDesiredParts hors bornes");
		return;
	}

	if(array.length===0)
	{
		console.log("Tableau vide");
		return;
	}

	if(nbDesiredParts>array.length)
	{
		console.log("Plus de parties que d'éléments, fixe au nombre d'éléments");
		nbDesiredParts=array.length;
	}

	var max=new Big(Math.max.apply(null,array));
	var min=new Big(Math.min.apply(null,array));

	var decimalInterval=max.minus(min).div(nbDesiredParts);

	var nbOcc=new Array();

	var arraySorted=array.sort(function(a,b){return a-b;});

	var intervalLow=new Big(min);
	var intervalHigh=intervalLow.plus(decimalInterval);

	var count=1;

	while(intervalHigh.lte(max))
	{

		if(count===nbDesiredParts)
			intervalHigh=max;

		var categ={
			min:parseFloat(intervalLow),
			max:parseFloat(intervalHigh),
			name:"de "+intervalLow.toFixed(3).replace('.',',')+" à "+intervalHigh.toFixed(3).replace('.',','),
			nbOcc:0
		};
	
		if(count!==nbDesiredParts)
		{
			arraySorted.forEach(function(element){				
				if(intervalLow.lte(element) && intervalHigh.gt(element)) 
				{
					categ.nbOcc++;
				}
			});
		}

		//Derniere partie, on arrondit en prenant high=max et on prend <= au max
		else
		{
			arraySorted.forEach(function(element){				
				if(intervalLow.lte(element) && intervalHigh.gte(element)) 
				{
					categ.nbOcc++;
				}
			});
		}

		if(categ.nbOcc>0)
		{
			var occBig=new Big(categ.nbOcc);
			categ.y=parseFloat(occBig.div(array.length).times(100));
			nbOcc.push(categ);
		}

		intervalLow=intervalHigh;
		intervalHigh=intervalHigh.plus(decimalInterval);
		count++;
	}
	return nbOcc;

}
