<?php

try
{
	$bdd = new PDO('mysql:host=localhost;dbname=dominos_vod;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

}
catch (Exception $e)
{
       $message = $e->getMessage();
}
$today = date('d-m-Y').' 23:59:59';
$req = $bdd->prepare('SELECT (c.nbrecodeday - c.nbrecodeused) AS compteur FROM vod_compteur AS c WHERE datepresta = :datepresta');
$req->execute(array('datepresta' => $today));

$compteur = $req->fetch(PDO::FETCH_OBJ);
$resultat ['compteur'] = $compteur->compteur;

echo json_encode($resultat);







