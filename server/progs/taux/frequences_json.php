<?php

require "fonctions_taux.php";

//0=TEC, 1=euribor/eonia, 2=les deux
$mode=0;

if(isset($_GET['mode']))
{
	if($_GET['mode']!=0 && $_GET['mode']!=1 && $_GET['mode']!=2)$mode=0;
	else $mode=$_GET['mode'];
}
$connect=connect_base();

$vals=array();
$alldatas=array();

if($mode==0 || $mode==2)
{
	$tocheck=array("TEC10","TEC15","TEC20","TEC25","TEC30");
	$tecs=array();
	foreach($tocheck as $oneval)
	{
		$vals[$oneval]=array();
		$tecs[$oneval]=array();
		$res=mysqli_query($connect,"select $oneval from TEC where $oneval is not null") or die(mysqli_error($connect));

		while(($result=mysqli_fetch_array($res))!=NULL)
		{
			if(array_key_exists($result[$oneval],$vals[$oneval]))$vals[$oneval][$result[$oneval]]++;
			else $vals[$oneval][$result[$oneval]]=1;
		}
		foreach($vals[$oneval] as $key=>$val)
		{
			if($val>2)$tecs[$oneval][]=array(doubleval($key),$val);
		}
		$alldata[$oneval]["data"]=$tecs[$oneval];
		$alldata[$oneval]["nbOcc"]=mysqli_num_rows($res);
	}
}

if($mode==1 || $mode==2)
{
	$vals['eonia']=array();
	//$res=mysqli_query($connect,"select round(eonia,2) as eonia from eonia") or die(mysqli_error($connect));
	$res=mysqli_query($connect,"select eonia from eonia where eonia is not null") or die(mysqli_error($connect));
	$countEo=0;
	while(($result=mysqli_fetch_array($res))!=NULL)
	{
		$countEo++;
		if(array_key_exists($result['eonia'],$vals['eonia']))$vals['eonia'][$result['eonia']]++;
		else $vals['eonia'][$result['eonia']]=1;
	}

	$eonias=array();
	foreach($vals['eonia'] as $key=>$val)
	{
		$eonia=array(doubleval($key),$val);
		if($val>2)
			$eonias[]=$eonia;
	}

	$alldata["eonia"]["data"]=$eonias;
	$alldata["eonia"]["nbOcc"]=mysqli_num_rows($res);

	$euribors=array();

	$tocheck=array("1_mois","3_mois","6_mois","12_mois");

	foreach($tocheck as $oneval)
	{
		$count=0;
		$vals[$oneval]=array();
		$euribors[$oneval]=array();

		$requete="select $oneval from euribor where $oneval is not null";
		$res=mysqli_query($connect,$requete)or die("Requête impossible : ".mysqli_error($connect));
		while($result=mysqli_fetch_array($res))
		{
			if(array_key_exists($result[$oneval],$vals[$oneval]))$vals[$oneval][$result[$oneval]]++;
			else $vals[$oneval][$result[$oneval]]=1;
		}

		foreach($vals[$oneval] as $key=>$val)
		{
			if($val>2)$euribors[$oneval][]=array(doubleval($key),$val);
		}
		$alldata["eur-".$oneval]["data"]=$euribors[$oneval];
		$alldata["eur-".$oneval]["nbOcc"]=mysqli_num_rows($res);
	}
	
}
mysqli_close($connect);
echo json_encode($alldata,JSON_NUMERIC_CHECK);

?>
