<?php if(!defined('GIPISITE'))exit();
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
$(document).ready(function(){

	var chart3,chart4;

   req='progs/taux/historique_json.php?mode=1&noDate=1';
  
  $.getJSON(req,function(data,status){

      var pieData_eonia=countOccByInterval(data["eonia"],5);
      var pieData_eur1m=countOccByInterval(data["eur1m"],5);
      var pieData_eur3m=countOccByInterval(data["eur3m"],5);
      var pieData_eur6m=countOccByInterval(data["eur6m"],5);
      var pieData_eur12m=countOccByInterval(data["eur12m"],5);

     
     chart4=new Highcharts.Chart({
      exporting:{
      	buttons:{
		contextButton:{
		menuItems:[{
		text:"Enregistrer au format PNG",
			
			onclick:function(){
				$("body").addClass("loading");	
				setTimeout(function(){chart4.exportChartLocal();$("body").removeClass("loading");},1000);
			}
		}],
		}
	}
  },

     credits:{enabled:true,
     		href:"",
	     text:"©2006-<?php echo date('Y');?> Laboratoire de Recherche pour le Développement Local"
      },
      title:{
	      text:"Répartition des taux variables"
      },

      subtitle:{
      	text:"Euribor 1 mois"
      },


      tooltip:{
	pointFormat: '{point.nbOcc} occurrences soit {point.percentage:.2f}%'
      },


      chart:{
	animation:false,
      renderTo:"graphique4",
	      type:"pie"
      },

      plotOptions:{
      pie:{
	animation:false,
      	dataLabels:{
	format: '{point.name}<br>{point.percentage:.2f}% ({point.nbOcc})'
      }
      }
      },

      series:[
      {
	      id:"eonia",
	      name:"Eonia",
	      data:pieData_eonia,
	      visible:false
      },      
      {
	      id:"eur1m",
	      name:"Euribor 1 mois",
	      data:pieData_eur1m

      },
      {
	      id:"eur3m",
	      name:"Euribor 3 mois",
	      data:pieData_eur3m,
	      visible:false
      },
      {
	      id:"eur6m",
	      name:"Euribor 6 mois",
	      data:pieData_eur6m,
	      visible:false
      },
      {
	      id:"eur12m",
	      name:"Euribor 12 mois",
	      data:pieData_eur12m,
	      visible:false
      }
     ]
      });

   $("#message4").hide();


      $('[name="group3"]').on("change",function(){
	      if(this.checked)
	      {
		      chart4.series.forEach(function(element){

			      if(element.options.id != this.value)
			      {
			      	element.hide();
			      }
		      });
		      var serie=chart4.get(this.value);
		      serie.show();
		      chart4.setTitle({text:"Répartition des taux variables"},{text:serie.name});
	      }
      });


      $("#numParts").on("input",function(){
	      $("#numPartsLabel").html($("#numParts").val());
      }
      );


      $("#numPartsButton").on("click",function(){

	      var numPartsValue=$("#numParts").val();
	      $("#graphique4").hide();

	      $("#message4").show(400,function(){
		      pieData_eonia=countOccByInterval(data["eonia"],parseInt(numPartsValue));
		      pieData_eur1m=countOccByInterval(data["eur1m"],parseInt(numPartsValue));
		      pieData_eur3m=countOccByInterval(data["eur3m"],parseInt(numPartsValue));
		      pieData_eur6m=countOccByInterval(data["eur6m"],parseInt(numPartsValue));
		      pieData_eur12m=countOccByInterval(data["eur12m"],parseInt(numPartsValue));

		      chart4.get("eonia").setData(pieData_eonia);
		      chart4.get("eur1m").setData(pieData_eur1m);
		      chart4.get("eur3m").setData(pieData_eur3m);
		      chart4.get("eur6m").setData(pieData_eur6m);
		      chart4.get("eur12m").setData(pieData_eur12m);
		      $("#message4").hide();
		      $("#graphique4").show();
	      });
      });

  })
    .fail(function(msg){
      $("#message4").text("Erreur de communication avec le serveur...");
      alert(msg.responseText);});



   var req='progs/taux/frequences_json.php?mode=1';
  
  var jqxhr=$.getJSON(req,function(data,status){
     
     chart3 = new Highcharts.Chart({
      exporting:{
      	buttons:{
		contextButton:{
		menuItems:[{
		text:"Enregistrer au format PNG",
			
			onclick:function(){
				$("body").addClass("loading");	
				setTimeout(function(){chart3.exportChartLocal();$("body").removeClass("loading");},1000);
			}
		}],
		}
	}
  },
      
     credits:{enabled:true,
     		href:"",
	     text:"©2006-<?php echo date('Y');?> Laboratoire de Recherche pour le Développement Local"
     },
      
      legend:{
        enabled:true
      },
    
      chart: {
	animation:false,
        renderTo: 'graphique3',
        defaultSeriesType: 'scatter',
         zoomType: 'xy'
      },
      
      title: {text: 'Volatilité des taux variables'},
    
          
      xAxis: {
        title:{text:"Taux"},
        maxZoom:0.2
      },
       
       tooltip:{
       formatter:function(){return "<b>"+this.series.name+" ("+this.series.options.nbOcc+" occurrences)</b><br />Taux : "+this.x.toFixed(3).replace('.',',')+"<br />Nombre d'occurrences : "+this.y+" (soit "+(this.y/this.series.options.nbOcc*100).toFixed(3).replace('.',',')+"%)";}
       },
      
      yAxis: {
        title:{text:"Nombre d'occurrences"},
        showFirstLabel:false,
        min:0,
        //type:'logarithmic',
        showLastLabel:false,
        maxZoom:2,
        allowDecimals:false
      },
       
      series:[
       {
	      name:"Eonia",
	      visible:false,
	      data:data["eonia"]["data"],
	      nbOcc:data["eonia"]["nbOcc"]
       },
       {
	      name:"Eur-1m",
	      data:data["eur-1_mois"]["data"],
	      nbOcc:data["eur-1_mois"]["nbOcc"]
       },
       {
	      name:"Eur-3m",
	      visible:false,
	      data:data["eur-3_mois"]["data"],
	      nbOcc:data["eur-3_mois"]["nbOcc"]
       },
       {
	      name:"Eur-6m",
	      visible:false,
	      data:data["eur-6_mois"]["data"],
	      nbOcc:data["eur-6_mois"]["nbOcc"]
       },
       {
	      name:"Eur-12m",
	      visible:false,
	      data:data["eur-12_mois"]["data"],
	      nbOcc:data["eur-12_mois"]["nbOcc"]
       }
       ],

       
      plotOptions:{       
          scatter:
          {
	animation:false,
		  stickyTracking:false
          }
     }
    });
   $("#message3").hide();

  })
    .fail(function(msg){
      $("#message3").text("Erreur de communication avec le serveur...");
      alert(msg.responseText);});
    
});
   </script>

<div class="row">
	<div id="variables" class="col s12">

<div class="row">
<div class="col l9 s12">
<div id="graphique3">
  <div id='message3' class="center-align">
	<img width="124" height="124"  src="gears.gif" alt="loading">
	<p>Traitement des données en cours, merci de patienter...</p>
</div>
</div>
</div>
<div class="col l3 s12">
<h5>Volatilité des taux</h5>
Ce graphique illustre la notion de <i>volatilité des taux</i>. Chaque valeur du taux est indiquée en abscisse, et en ordonnée le nombre de jours (pas nécessairement consécutifs) durant lesquels ce taux est apparu. Les taux qui apparaissent moins de trois fois ne sont pas affichés. On peut remarquer que certaines valeurs ont tendance à se répéter plus que d'autres et au contraire qu'il existe des taux qui n'apparaissent quasiment jamais.
</div>
</div>

<div class="section">

<div class="row">
<div class="col l4 s12" id="radioMode2">
<h5>Répartition des taux</h5>
<p>Ce graphique présente la répartition des occurrences des taux à l'intérieur de N catégories de tailles égales (N est réglable avec le curseur). Par exemple si N=3, que le taux minimum de la période historique vaut 0 et que le taux maximum vaut 3, le graphique comportera au plus trois zones : de 0(inclus) à 1(exclus), de 1(inclus) à 2(exclus) et de 2(inclus) à 3(inclus). La taille de chaque zone correspondra aux nombres de taux qui se situent dans l'intervalle. Les zones vides ne sont pas affichées</p>
<label for="numParts">Nombre de regroupements voulus :</label>
<span id="numPartsLabel">5</span>
<div class="range-field">
<input type="range" style="width:100%" id="numParts" min="2" max="10" value="5" />
<input style="width:100%" type="button" id="numPartsButton" class="btn" value="Valider" />
</div>
</div>
<div class="col l8 s12">
  <div id='message4' class="center-align">
	<img width="124" height="124"  src="gears.gif" alt="loading">
	<p>Traitement des données en cours, merci de patienter...</p>
</div>
<div id="graphique4">
</div>
<div class="center-align">
<input name="group3" type="radio" class="with-gap" id="eonia" value="eonia" />
<label for="eonia">Eonia</label>
<input name="group3" type="radio" class="with-gap"  checked id="eur1m" value="eur1m" />
<label for="eur1m">Eur-1m</label>
<input name="group3" type="radio" class="with-gap"  id="eur3m" value="eur3m" />
<label for="eur3m">Eur-3m</label>
<input name="group3" type="radio" class="with-gap"  id="eur6m" value="eur6m" />
<label for="eur6m">Eur-6m</label>
<input name="group3" type="radio" class="with-gap"  id="eur12m" value="eur12m" />
<label for="eur12m">Eur-12m</label>
</div>
</div>
</div>
</div>

</div>
</div>
