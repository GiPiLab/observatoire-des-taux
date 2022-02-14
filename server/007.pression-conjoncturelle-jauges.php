<?php if(!defined('GIPISITE'))exit();
require_once "progs/taux/fonctions_taux.php";
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


if(!isset($_GET['annee']))$annee=date('Y');else $annee=$_GET['annee'];

if(!is_numeric($annee) || (int)$annee>date("Y") || (int)$annee <1999)die("Année incorrecte !");

$annee=(int)$annee;

if ($annee >= 2020) {
	$esterOrEonia = "ester";
} else {
	$esterOrEonia = "eonia";
}

$connect=connect_base();
?>
<script type="text/javascript" language="javascript">
$(document).ready(function(){

		//La page de toutes les jauges circulaires
		var reqJSON='progs/taux/getTauxJSON.php?annee=<?php echo $annee;?>';
  
    $.getJSON(reqJSON,function(mesData,status){

	    $('#tooltipeonia').html(
	    "<div style='text-align:left'><?php echo ucfirst($esterOrEonia); ?> ("+mesData.<?php echo $esterOrEonia; ?>.date+")<hr>taux : "+mesData.<?php echo $esterOrEonia; ?>.current
		    +"%<br>précédent : "+mesData.<?php echo $esterOrEonia; ?>.prev+"%<br>plus petit : "+mesData.<?php echo $esterOrEonia; ?>.min
		    +"%<br>plus grand : "+mesData.<?php echo $esterOrEonia; ?>.max+"%</div>");
	    
	    $('#tooltipeur1m').html("<div style='text-align:left'>Eur-1m ("+mesData.euribors.date+")<hr>taux : "+mesData.euribors.unMois.current
		    +"%<br>précédent : "+mesData.euribors.unMois.prev+"%<br>plus petit : "+mesData.euribors.unMois.min
		    +"%<br>plus grand : "+mesData.euribors.unMois.max+"%</div>");



	    $('#tooltipeur3m').html("<div style='text-align:left'>Eur-3m ("+mesData.euribors.date+")<hr>taux : "+mesData.euribors.troisMois.current
		    +"%<br>précédent : "+mesData.euribors.troisMois.prev+"%<br>plus petit : "+mesData.euribors.troisMois.min
		    +"%<br>plus grand : "+mesData.euribors.troisMois.max+"%</div>");
	    $('#tooltipeur6m').html("<div style='text-align:left'>Eur-6m ("+mesData.euribors.date+")<hr>taux : "+mesData.euribors.sixMois.current
		    +"%<br>précédent : "+mesData.euribors.sixMois.prev+"%<br>plus petit : "+mesData.euribors.sixMois.min
		    +"%<br>plus grand : "+mesData.euribors.sixMois.max+"%</div>");

	    $('#tooltipeur12m').html("<div style='text-align:left'>Eur-12m ("+mesData.euribors.date+")<hr>taux : "+mesData.euribors.douzeMois.current
		    +"%<br>précédent : "+mesData.euribors.douzeMois.prev+"%<br>plus petit : "+mesData.euribors.douzeMois.min
		    +"%<br>plus grand : "+mesData.euribors.douzeMois.max+"%</div>");

	    $('#tooltiptec10').html(
	    "<div style='text-align:left'>TEC-10 ("+mesData.tecs.date+")<hr>taux : "+mesData.tecs.tec10.current
		    +"%<br>précédent : "+mesData.tecs.tec10.prev+"%<br>plus petit : "+mesData.tecs.tec10.min
		    +"%<br>plus grand : "+mesData.tecs.tec10.max+"%</div>");
	    $('#tooltiptec15').html(
	    "<div style='text-align:left'>TEC-15 ("+mesData.tecs.date+")<hr>taux : "+mesData.tecs.tec15.current
		    +"%<br>précédent : "+mesData.tecs.tec15.prev+"%<br>plus petit : "+mesData.tecs.tec15.min
		    +"%<br>plus grand : "+mesData.tecs.tec15.max+"%</div>");
	    $('#tooltiptec20').html(
	    "<div style='text-align:left'>TEC-20 ("+mesData.tecs.date+")<hr>taux : "+mesData.tecs.tec20.current
		    +"%<br>précédent : "+mesData.tecs.tec20.prev+"%<br>plus petit : "+mesData.tecs.tec20.min
		    +"%<br>plus grand : "+mesData.tecs.tec20.max+"%</div>");

	    $('#tooltiptec25').html(
	    "<div style='text-align:left'>TEC-25 ("+mesData.tecs.date+")<hr>taux : "+mesData.tecs.tec25.current
		    +"%<br>précédent : "+mesData.tecs.tec25.prev+"%<br>plus petit : "+mesData.tecs.tec25.min
		    +"%<br>plus grand : "+mesData.tecs.tec25.max+"%</div>");

	    $('#tooltiptec30').html(
	   "<div style='text-align:left'>TEC-30 ("+mesData.tecs.date+")<hr>taux : "+mesData.tecs.tec30.current
		    +"%<br>précédent : "+mesData.tecs.tec30.prev+"%<br>plus petit : "+mesData.tecs.tec30.min
		    +"%<br>plus grand : "+mesData.tecs.tec30.max+"%</div>");
	    
	    var floatEoCurrent=mesData.<?php echo $esterOrEonia; ?>.current;
	    var floatEoPrev=mesData.<?php echo $esterOrEonia; ?>.prev;
	    var floatEur1mCurrent=mesData.euribors.unMois.current;
	    var floatEur1mPrev=mesData.euribors.unMois.prev;
	    var floatEur3mCurrent=mesData.euribors.troisMois.current;
	    var floatEur3mPrev=mesData.euribors.troisMois.prev;
	    var floatEur6mCurrent=mesData.euribors.sixMois.current;
	    var floatEur6mPrev=mesData.euribors.sixMois.prev;
	    var floatEur12mCurrent=mesData.euribors.douzeMois.current;
	    var floatEur12mPrev=mesData.euribors.douzeMois.prev;
	    var floatTEC10Current=mesData.tecs.tec10.current;
	    var floatTEC10Prev=mesData.tecs.tec10.prev;
	    var floatTEC15Current=mesData.tecs.tec15.current;
	    var floatTEC15Prev=mesData.tecs.tec15.prev;
	    var floatTEC20Current=mesData.tecs.tec20.current;
	    var floatTEC20Prev=mesData.tecs.tec20.prev;
	    var floatTEC25Current=mesData.tecs.tec25.current;
	    var floatTEC25Prev=mesData.tecs.tec25.prev;
	    var floatTEC30Current=mesData.tecs.tec30.current;
	    var floatTEC30Prev=mesData.tecs.tec30.prev;


	    var symboleEo='→';
	    if(floatEoCurrent<floatEoPrev)
	    {
		    symboleEo='↓';
			<?php echo $esterOrEonia; ?>	    }
	    else if(floatEoCurrent>floatEoPrev)
	    {
		    symboleEo='↑';
	    }
	    var symboleEur1m='→';
	    if(floatEur1mCurrent<floatEur1mPrev)
	    {
		    symboleEur1m='↓';
	    }
	    else if(floatEur1mCurrent>floatEur1mPrev)
	    {
		    symboleEur1m='↑';
	    }
	    var symboleEur3m='→';
	    if(floatEur3mCurrent<floatEur3mPrev)
	    {
		    symboleEur3m='↓';
	    }
	    else if(floatEur3mCurrent>floatEur3mPrev)
	    {
		    symboleEur3m='↑';
	    }
	    var symboleEur6m='→';
	    if(floatEur6mCurrent<floatEur6mPrev)
	    {
		    symboleEur6m='↓';
	    }
	    else if(floatEur6mCurrent>floatEur6mPrev)
	    {
		    symboleEur6m='↑';
	    }
	    var symboleEur12m='→';
	    if(floatEur12mCurrent<floatEur12mPrev)
	    {
		    symboleEur12m='↓';
	    }
	    else if(floatEur12mCurrent>floatEur12mPrev)
	    {
		    symboleEur12m='↑';
	    }
	    var symboleTEC10='→';
	    if(floatTEC10Current<floatTEC10Prev)
	    {
		    symboleTEC10='↓';
	    }
	    else if(floatTEC10Current>floatTEC10Prev)
	    {
		    symboleTEC10='↑';
	    }
	    var symboleTEC15='→';
	    if(floatTEC15Current<floatTEC15Prev)
	    {
		    symboleTEC15='↓';
	    }
	    else if(floatTEC15Current>floatTEC15Prev)
	    {
		    symboleTEC15='↑';
	    }
	    var symboleTEC20='→';
	    if(floatTEC20Current<floatTEC20Prev)
	    {
		    symboleTEC20='↓';
	    }
	    else if(floatTEC20Current>floatTEC20Prev)
	    {
		    symboleTEC20='↑';
	    }
	    var symboleTEC25='→';
	    if(floatTEC25Current<floatTEC25Prev)
	    {
		    symboleTEC25='↓';
	    }
	    else if(floatTEC25Current>floatTEC25Prev)
	    {
		    symboleTEC25='↑';
	    }
	    var symboleTEC30='→';
	    if(floatTEC30Current<floatTEC30Prev)
	    {
		    symboleTEC30='↓';
	    }
	    else if(floatTEC30Current>floatTEC30Prev)
	    {
		    symboleTEC30='↑';
	    }



	    var gEonia=new JustGage({
		id:"jaugeEonia",
			startAnimationTime:0,
			refreshAnimationTime:0,
		value:floatEoCurrent,
		decimals:3,
		min:mesData.eonia.min,
		max:mesData.eonia.max,
		relativeGaugeSize:true,
		label:"<?php echo ucfirst($esterOrEonia); ?> "+symboleEo

    });
	    var gEur1m=new JustGage({
		id:"jaugeEur1m",
		value:floatEur1mCurrent,
			startAnimationTime:0,
			refreshAnimationTime:0,
		decimals:3,
		min:mesData.euribors.unMois.min,
		max:mesData.euribors.unMois.max,
		relativeGaugeSize:true,
		label:"Eur-1 mois "+symboleEur1m

    });
	    var gEur3m=new JustGage({
		id:"jaugeEur3m",
		value:floatEur3mCurrent,
			startAnimationTime:0,
			refreshAnimationTime:0,
		decimals:3,
		min:mesData.euribors.troisMois.min,
		max:mesData.euribors.troisMois.max,
		relativeGaugeSize:true,
		label:"Eur-3 mois "+symboleEur3m

    });
	    var gEur6m=new JustGage({
		id:"jaugeEur6m",
		value:floatEur6mCurrent,
			startAnimationTime:0,
			refreshAnimationTime:0,
		decimals:3,
		min:mesData.euribors.sixMois.min,
		max:mesData.euribors.sixMois.max,
		relativeGaugeSize:true,
		label:"Eur-6 mois "+symboleEur6m

    });
	    var gEur12m=new JustGage({
		id:"jaugeEur12m",
		value:floatEur12mCurrent,
			startAnimationTime:0,
			refreshAnimationTime:0,
		decimals:3,
		min:mesData.euribors.douzeMois.min,
		max:mesData.euribors.douzeMois.max,
		relativeGaugeSize:true,
		label:"Eur-12 mois "+symboleEur12m

    });
	    var gTec10=new JustGage({
		id:"jaugeTEC10",
			startAnimationTime:0,
			refreshAnimationTime:0,
		value:floatTEC10Current,
		decimals:3,
		min:mesData.tecs.tec10.min,
		max:mesData.tecs.tec10.max,
		relativeGaugeSize:true,
		label:"TEC-10 "+symboleTEC10
    });
	    var gTec15=new JustGage({
		id:"jaugeTEC15",
		value:floatTEC15Current,
			startAnimationTime:0,
			refreshAnimationTime:0,
		decimals:3,
		min:mesData.tecs.tec15.min,
		max:mesData.tecs.tec15.max,
		relativeGaugeSize:true,
		label:"TEC-15 "+symboleTEC15
    });
	    var gTec20=new JustGage({
		id:"jaugeTEC20",
		value:floatTEC20Current,
			startAnimationTime:0,
			refreshAnimationTime:0,
		decimals:3,
		min:mesData.tecs.tec20.min,
		max:mesData.tecs.tec20.max,
		relativeGaugeSize:true,
		label:"TEC-20 "+symboleTEC20
    });
	    var gTec25=new JustGage({
		id:"jaugeTEC25",
		value:floatTEC25Current,
			startAnimationTime:0,
			refreshAnimationTime:0,
		decimals:3,
		min:mesData.tecs.tec25.min,
		max:mesData.tecs.tec25.max,
		relativeGaugeSize:true,
		label:"TEC-25 "+symboleTEC25
    });
	    var gTec30=new JustGage({
		id:"jaugeTEC30",
		value:floatTEC30Current,
			startAnimationTime:0,
			refreshAnimationTime:0,
		decimals:3,
		min:mesData.tecs.tec30.min,
		max:mesData.tecs.tec30.max,
		relativeGaugeSize:true,
		label:"TEC-30 "+symboleTEC30
    });

}).fail(function(msg){
      alert(msg.responseText);});
});
</script>


<div class="row">
	<div class="col m6 s12">
		<h4>Jauges de pression conjoncturelle, exercice&nbsp;<?php echo $annee;?></h4>
	</div>
	<div style="margin-top:1em" class="col m6 s12">
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="GET">
		<!-- hardcoded page number, ugly but easy xss prevention -->
		<input type="hidden" name="page" value="7" />
			<label>Choisir l'exercice :</label>
			<span id="valueRange"><?php echo $annee;?></span>
			<div class="range-field">
				<input style="width:100%" type="range" min="1999" max="<?php echo date('Y');?>" value="<?php echo $annee;?>" onInput="document.getElementById('valueRange').innerHTML=this.value;" name="annee">
				<input class="btn" style="width:100%" type="submit" value="Valider" />
			</div>
		</form>
	</div>
</div>



<div class="row">

	<div class="col s12" id="toutesJauges">

<div class="row">

<div class="col l6 s12">
<div class="card-panel">
	<div class="row">
		
		<div class="col s12">
			<p class="center-align flow-text">TAUX VARIABLES</p>
		</div>
	</div>
	<div class="row">
		<div id="jaugeEonia" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltipeonia"></span></div>
		<div id="jaugeEur1m" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltipeur1m"></span></div>
	</div>
	<div class="row">
		<div id="jaugeEur3m" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltipeur3m"></span></div>
		<div id="jaugeEur6m" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltipeur6m"></span></div>
	</div>
	<div class="row">
		<div id="jaugeEur12m" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltipeur12m"></span></div>
	</div>
</div>
</div>

<div class="col l6 s12">
<div class="card-panel">
	<div class="row">
		<div class="col s12">
			<p class="center-align flow-text">TAUX FIXES</p>
		</div>
	</div>
	<div class="row">
		<div id="jaugeTEC10" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltiptec10"></span></div>
		<div id="jaugeTEC15" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltiptec15"></span></div>
	</div>
	<div class="row">
		<div id="jaugeTEC20" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltiptec20"></span></div>
		<div id="jaugeTEC25" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltiptec25"></span></div>
	</div>
	<div class="row">
		<div id="jaugeTEC30" style="height:150px" class="tooltip col s6"><span class="tooltiptext" id="tooltiptec30"></span></div>
	</div>
</div>
</div>
</div>



	</div>


</div>


