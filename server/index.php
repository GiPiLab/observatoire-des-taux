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

define('GIPISITE',true);

$pageCourante=filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);
if($pageCourante===false)
{
	header("Status: 403 Forbidden",false,403);
	die("Bad format");
}
elseif($pageCourante===NULL)
{
	$pageCourante=1;
}
elseif($pageCourante<=0 || $pageCourante>12)
{
	header("Status: 404 Not Found",false,404);
	die("Invalid page number");
}

try
{
	$db=new SQLite3("pages.sqlite3",SQLITE3_OPEN_READONLY);
}
catch(Exception $e)
{
	header("Status: 500 Internal Server Error",false,500);
	die($e->getMessage());
}

$res=$db->querySingle("select * from pages where id='$pageCourante'",true);
if($res===FALSE)
{
	header("Status: 500 Internal Server Error",false,500);
	$db->close();
	die("Error prepare");
}
if(!empty($res))
{
	$fileName=$res['file'];
	$pageTitle=$res['title'];
}
else
{
	header("Status: 404 Not Found",false,404);
	$db->close();
	die("Page not found");
}

$db->close();

if(!file_exists("$fileName"))
{
	header("Status: 500 Internal Server Error",false,500);
	die("Invalid index entry $pageCourante");
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <title><?php echo htmlentities($pageTitle); ?></title>
  <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <link rel="icon" href="favicon.ico" />

<link rel="stylesheet" href="libs/materialize/css/materialize.min.css" />


<style type="text/css">


/* Tooltip container */
.tooltip {
    position: relative;
    display: inline-block;
}

/* Tooltip text */
.tooltip .tooltiptext {
    visibility: hidden;
	padding:8px;
    background-color: rgba(0,0,0,0.7); 
    color: #fff;
    text-align: center;
    border-radius: 6px;
    position: absolute;
    z-index: 1;
    bottom: 10%;
    left: 25%; 
}



.tooltip:hover .tooltiptext {
    visibility: visible;
}


/* Waiting overlay */
.modal-waiting-overlay{
	display:none;
	position:fixed;
	z-index:1000;
	top:0;
	left:0;
	height:100%;
	width:100%;
	background:rgba(0,0,0,0.4)
		url("gears.gif")
		50% 50%
		no-repeat;
}

body.loading{
	overflow:hidden;
}

body.loading .modal-waiting-overlay{
	display:block;
}



.faq-question{
	cursor:pointer;
}

.faq-answer {
    display: none;
    padding:0.5em 1em 0.5em 1.3em;
    background-color: rgb(220,220,220);
}


dt{
	font-weight:bold;
	padding-top:1rem;
}

h4.lexique{
	padding-top:2rem;

}


.body-uniform{
	background-color:white;
}


td.petit
{
	padding:5px;

}

.collapsible-body li 
{
list-style-type:inherit !important;

}

.collection-item
{
	padding:5px 10px 5px 10px !important;
}

input.highcharts-range-selector {
	background:white;
	transition:none;
}


</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>



<?php if($pageCourante==7){?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.7/raphael.min.js" integrity="sha256-67By+NpOtm9ka1R6xpUefeGOY8kWWHHRAKlvaTJ7ONI=" crossorigin="anonymous"></script>
<script type="text/javascript" src="libs/justgage.js"></script>
<?php }?>

<?php if($pageCourante!=7 && $pageCourante!=12){?>
<script type="text/javascript" src="https://code.highcharts.com/stock/6.0.4/highstock.js"></script>
<script type="text/javascript" src="https://code.highcharts.com/stock/6.0.4/highcharts-more.js"></script>
<script type="text/javascript" src="progs/taux/js_thib_pour_taux.js"></script>
<?php }?>

<?php if($pageCourante==8||$pageCourante==9){?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/big.js/5.0.3/big.min.js"></script>
<?php }?>


</head>

<body class='body-uniform'>

<main>
<?php
include("$fileName");
?>
</main>

<div class="modal-waiting-overlay"></div>

</body>
</html>
