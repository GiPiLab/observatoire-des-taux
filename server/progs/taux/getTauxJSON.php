<?php
//N'utilise pas de SESSION car peut être appellé depuis le simulateur d'emprunts sur android
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
require_once 'fonctions_taux.php';

if(isset($_GET['annee']))
{
	$annee=htmlentities($_GET['annee']);
	if(!is_numeric($annee) || $annee>date("Y") || $annee <1999)die("Ann&eacute;e incorrecte !");
	$annee=(int)$annee;
}
else $annee=date("Y");


$connect=connect_base();

$requete="select round(max(1_mois),3) as max1,round(max(3_mois),3) as max3,round(max(6_mois),3) as max6, round(max(12_mois),3) as max12, round(min(1_mois),3) as min1,round(min(3_mois),3) as min3,round(min(6_mois),3) as min6,round(min(12_mois),3) as min12 from euribor where year(date)<='$annee' limit 1";
$result=mysqli_query($connect,$requete)or maildie(mysqli_error($connect));
while($euribor=mysqli_fetch_assoc($result))
{
	$euribors['unMois']['min']=$euribor['min1'];
	$euribors['troisMois']['min']=$euribor['min3'];
	$euribors['sixMois']['min']=$euribor['min6'];
	$euribors['douzeMois']['min']=$euribor['min12'];
	$euribors['unMois']['max']=$euribor['max1'];
	$euribors['troisMois']['max']=$euribor['max3'];
	$euribors['sixMois']['max']=$euribor['max6'];
	$euribors['douzeMois']['max']=$euribor['max12'];
}

$requete="select * from euribor where year(date)='$annee' order by date desc limit 2";
$result=mysqli_query($connect,$requete)or maildie(mysqli_error($connect));
$date_euribor=0;
$i=0;
while($euribor=mysqli_fetch_assoc($result))
{
	if($i==0)
	{
		$euribors['unMois']['current']=$euribor['1_mois'];
		$euribors['troisMois']['current']=$euribor['3_mois'];
		$euribors['sixMois']['current']=$euribor['6_mois'];
		$euribors['douzeMois']['current']=$euribor['12_mois'];
	}
	else
	{
		$euribors['unMois']['prev']=$euribor['1_mois'];
		$euribors['troisMois']['prev']=$euribor['3_mois'];
		$euribors['sixMois']['prev']=$euribor['6_mois'];
		$euribors['douzeMois']['prev']=$euribor['12_mois'];

	}
	list($annee,$mois,$jour)=explode('-',$euribor['date']);
	if($date_euribor==0)$date_euribor="$jour/$mois/$annee";
	$i++;
}
$euribors['date']=$date_euribor;

$requete="select round(max(TEC10),3) as max10,round(max(TEC15),3) as max15,round(max(TEC20),3) as max20, round(max(TEC25),3) as max25, round(max(TEC30),3) as max30, round(min(TEC10),3) as min10,round(min(TEC15),3) as min15,round(min(TEC20),3) as min20,round(min(TEC25),3) as min25, round(min(TEC30),3) as min30 from TEC where year(date)<='$annee' limit 1";
$result=mysqli_query($connect,$requete)or maildie(mysqli_error($connect));
while($tec=mysqli_fetch_assoc($result))
{
	$tecs['tec10']['min']=$tec['min10'];
	$tecs['tec15']['min']=$tec['min15'];
	$tecs['tec20']['min']=$tec['min20'];
	$tecs['tec25']['min']=$tec['min25'];
	$tecs['tec30']['min']=$tec['min30'];
	$tecs['tec10']['max']=$tec['max10'];
	$tecs['tec15']['max']=$tec['max15'];
	$tecs['tec20']['max']=$tec['max20'];
	$tecs['tec25']['max']=$tec['max25'];
	$tecs['tec30']['max']=$tec['max30'];
}

$requete="select * from TEC where year(date)='$annee' order by date desc limit 2";
$result=mysqli_query($connect,$requete)or maildie(mysqli_error($connect));
$date_tec=0;
$i=0;

while($tec=mysqli_fetch_assoc($result))
{
	if($i==0)
	{
		$tecs['tec10']['current']=$tec['TEC10'];
		$tecs['tec15']['current']=$tec['TEC15'];
		$tecs['tec20']['current']=$tec['TEC20'];
		$tecs['tec25']['current']=$tec['TEC25'];
		$tecs['tec30']['current']=$tec['TEC30'];
	}
	else
	{
		$tecs['tec10']['prev']=$tec['TEC10'];
		$tecs['tec15']['prev']=$tec['TEC15'];
		$tecs['tec20']['prev']=$tec['TEC20'];
		$tecs['tec25']['prev']=$tec['TEC25'];
		$tecs['tec30']['prev']=$tec['TEC30'];
	}
	$i++;
	list($annee,$mois,$jour)=explode('-',$tec['date']);
	if($date_tec==0)$date_tec="$jour/$mois/$annee";
}
$tecs['date']=$date_tec;



$requete="select round(min(eonia),3) as mineo,round(max(eonia),3) as maxeo from eonia where year(date)<='$annee' limit 1";
$result=mysqli_query($connect,$requete)or maildie(mysqli_error());
while($eo=mysqli_fetch_assoc($result))
{
	$eonia['min']=$eo['mineo'];
	$eonia['max']=$eo['maxeo'];
}

$requete="select * from eonia where year(date)='$annee' order by date desc limit 2";
$result=mysqli_query($connect,$requete)or maildie(mysqli_error());
$date_eonia=0;
$i=0;
while($eo=mysqli_fetch_assoc($result))
{
	if($i==0)
	{
		$eonia['current']=$eo['eonia'];
	}
	else
	{
		$eonia['prev']=$eo['eonia'];
	}
	list($annee,$mois,$jour)=explode('-',$eo['date']);
	if($date_eonia==0)$date_eonia="$jour/$mois/$annee";
	$i++;
}
$eonia['date']=$date_eonia;
$data['eonia']=$eonia;
$data['euribors']=$euribors;
$data['tecs']=$tecs;
mysqli_close($connect);
echo json_encode($data,JSON_NUMERIC_CHECK);
?>
