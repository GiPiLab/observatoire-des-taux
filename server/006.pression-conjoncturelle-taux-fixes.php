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

$connect=connect_base();
?>
<script type="text/javascript" language="javascript">
$(document).ready(function(){

	var chartFixe;

	var reqFixe='progs/taux/getTauxJSON.php?annee=<?php echo $annee;?>';

	$.getJSON(reqFixe,function(data,status){

	chartFixe = new Highcharts.chart({
      exporting:{
      	buttons:{
		contextButton:{
		menuItems:[{
		text:"Enregistrer au format PNG",
			
			onclick:function(){
				$("body").addClass("loading");	
				setTimeout(function(){chartFixe.exportChartLocal();$("body").removeClass("loading");},1000);
			}
		}],
		}
	}
  },

	credits:{enabled:true,
     		href:"",
		text:"©2006-<?php echo date('Y');?> Laboratoire de Recherche pour le Développement Local"
	},

	chart: {
	type:'scatter',
	animation:false,
		renderTo: 'graphiqueFixe',
		zoomType:"xy"
	},

	title: {text: 'Pression conjoncturelle des taux fixes'},

	xAxis: {
	categories:['TEC-10','TEC-15','TEC-20','TEC-25','TEC-30']
	},

	legend:{
	enabled:false
	},

	yAxis: {
	title:{text:"Taux"}
	},

	tooltip:{
         formatter:function(){return "<b>"+this.series.name+"</b><br />Jauge du "+this.series.options.laDate+"<br />"}
	},


	plotOptions: {
            scatter: {
	animation:false,
	    	lineWidth:2,
	     	dataLabels: {
                    enabled: true,
			    format: '{point.y:,.3f}'
		},
		marker:{
		radius:6,
		symbol:'circle'
	}
	

            }
        },

	series:[
	{
		name:"TEC-10",
		data:[[0,data["tecs"]["tec10"]["min"]],[0,data["tecs"]["tec10"]["max"]],{x:0,y:data["tecs"]["tec10"]["current"],marker:{fillColor:'#ff0000'}}],
		laDate:data["tecs"]["date"]
	    },
	{
		name:"TEC-15",
		data:[[1,data["tecs"]["tec15"]["min"]],[1,data["tecs"]["tec15"]["max"]],{x:1,y:data["tecs"]["tec15"]["current"],marker:{fillColor:'#ff0000'}}],
		laDate:data["tecs"]["date"]
		},
	{
		name:"TEC-20",
		data:[[2,data["tecs"]["tec20"]["min"]],[2,data["tecs"]["tec20"]["max"]],{x:2,y:data["tecs"]["tec20"]["current"],marker:{fillColor:'#ff0000'}}],
		laDate:data["tecs"]["date"]
		},
	{
		name:"TEC-25",
		data:[[3,data["tecs"]["tec25"]["min"]],[3,data["tecs"]["tec25"]["max"]],{x:3,y:data["tecs"]["tec25"]["current"],marker:{fillColor:'#ff0000'}}],
		laDate:data["tecs"]["date"]
		},
	{
		name:"TEC-30",
		data:[[4,data["tecs"]["tec30"]["min"]],[4,data["tecs"]["tec30"]["max"]],{x:4,y:data["tecs"]["tec30"]["current"],marker:{fillColor:'#ff0000'}}],
		laDate:data["tecs"]["date"]
		}
		] 
	});

	$("#messageFixe").hide();

	}).fail(function(msg){
		$("#messageFixe").text("Erreur de communication avec le serveur...");
		alert(msg.responseText);});

});
</script>


<div class="row">
	<div class="col m6 s12">
		<h4>Pression conjoncturelle des taux fixes, exercice&nbsp;<?php echo $annee;?></h4>
	</div>
	<div style="margin-top:1em" class="col m6 s12">
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="GET">
		<!-- hardcoded page number, ugly but easy xss prevention -->
		<input type="hidden" name="page" value="6" />
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

	<div class="col s12" id="fixes">
<?php

//Recuperer le dernier connu
$dernier_connu_sql=tableau_variations_veille($annee,$connect,0,1);
list($y,$m,$d)=explode('-',$dernier_connu_sql[0]);
$derniere_date="$d-$m-$y";

echo "<div class='row'>";

echo "<div class='center-align col s12'>";
echo "<div id='graphiqueFixe'>";
echo " <div id='messageFixe' class='center-align'><img width='124' height='124' src='gears.gif' alt='loading'><p>Traitement des données en cours, merci de patienter...</p></div>";
echo "</div></div></div>";
echo "<div class='row'><div class='col l6 m12 s12'>";

  tableau_plus_annuel_tec($annee,$dernier_connu_sql,$connect);
echo "</div><div class='col l6 m12 s12'>";
tableau_plus_tout_tec($dernier_connu_sql,$connect);
echo "</div></div>";

echo "<div class='row'>";
echo "<div class='col s12'>";
  echo '<span id="explicationjauges"></span>La théorie des jauges de pression conjoncturelle© avance que l\'émergence d\'une inversion de tendance (haussière ou baissière) du cycle TEC (ou de tout autre type de grandeur) doit donner lieu à une analyse de la position du point mesuré lorsqu\'il est placé sur une échelle (la jauge) dont les limites correspondent aux extrêmes historiques de la grandeur mesurée.';
  echo '<p>La théorie des jauges de pression conjoncturelle© est exploitable sous forme graphique et chiffrée.</p>';
  echo '<p><strong>Sous forme graphique, elle offre un visuel clair de l\'espace séparant le point observé des limites hautes et basses de la jauge à une date donnée. Elle fournit un indicateur des marges haussières et baissières disponibles avant franchissement des limites historiques.</strong></p>';
  echo '<p>Dans sa forme chiffrée, elle permet de connaître le rang occupé par le point observé dans la hiérarchie des valeurs enregistrées depuis l\'origine et depuis le début de l\'exercice en cours. Par convention, une valeur de rang 1 est la plus élevée de la période.</p>';
  echo '<p>La théorie des jauges de pression conjoncturelle© est un outil d\'aide à l\'anticipation. A ce titre, il n\'est pas utile d\'appliquer l\'analyse chiffrée à une date antérieure. Par contre, les analyses chiffrées et graphiques doivent impérativement être exploitées en parallèle avant toute décision susceptible d\'interférer avec la structure d\'adossement de la dette locale.</p>';
echo "</div></div>";
?>

	</div>
</div>
