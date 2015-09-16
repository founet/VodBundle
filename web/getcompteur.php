<?php
use Symfony\Component\Yaml\Parser;
require_once __DIR__.'/../app/bootstrap.php.cache';

$yaml = new Parser();

$value = $yaml->parse(file_get_contents('../app/config/parameters.yml'));

$dbname =   $value['parameters']['database_name'];
$username = $value['parameters']['database_user'];
$password = $value['parameters']['database_password'];
$host = $value['parameters']['database_host'];
$nbre_coupons_temporisation = $value['parameters']['nbre_coupons_temporisation'];

try
{

	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $username,$password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));


}
catch (Exception $e)
{
       print_r($e->getMessage());
}
$today = date('d-m-Y').' 23:59:59';
$req = $bdd->prepare('SELECT (c.nbrecodeday - c.nbrecodeused) AS compteur FROM vod_compteur AS c WHERE datepresta = :datepresta');
$req->execute(array('datepresta' => $today));

$compteur = $req->fetch(PDO::FETCH_OBJ);
$resultat ['compteur'] = $compteur->compteur - $nbre_coupons_temporisation;

echo json_encode($resultat);
