<?php

require_once "fonctions_taux.php";

if(isset($_GET['annee']))
{
	$annee=htmlentities($_GET['annee']);
	if(!is_numeric($annee) || $annee>date("Y") || $annee <1999)die("Ann&eacute;e incorrecte !");
	$annee=(int)$annee;
}
else $annee=date("Y");

$mode=0;

//Mode = 1 : TEC
if(isset($_GET['mode']) && $_GET['mode']==="1")$mode=1;


$connect=connect_base();

if($mode==0)
{
	$requete="select * from euribor,eonia where euribor.date=eonia.date and year(euribor.date)='$annee' order by euribor.date limit 1";
}

else
{
	$requete="select * from TEC where year(date)='$annee' order by date limit 1";
}

$result=mysqli_query($connect,$requete)or die(mysqli_error($connect));
$premier_jour_annee=mysqli_fetch_assoc($result);

if(empty($premier_jour_annee['date']))die("Erreur impr&eacute;vue !");

$premier_jour_annee["date"]=date("d/m/Y",strtotime($premier_jour_annee["date"]));

if($mode==0)
{
	$requete="select * from euribor,eonia where euribor.date=eonia.date and year(euribor.date)='$annee' order by euribor.date desc limit 1";
}
else
{
	$requete="select * from TEC where year(date)='$annee' order by date desc limit 1";
}

$result=mysqli_query($connect,$requete)or die(mysqli_error($connect));
while(($res=mysqli_fetch_assoc($result))!=NULL)
{
	$res["date"]=date("d/m/Y",strtotime($res["date"]));
	$data["dernierJourConnu"]=$res;
}
mysqli_close($connect);

if(empty($data["dernierJourConnu"]))die("Erreur impr&eacute;vue !");


$data['premierJourAnnee']=$premier_jour_annee;

echo json_encode($data,JSON_NUMERIC_CHECK);
?>
