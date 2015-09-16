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
       $message = $e->getMessage();
}

$today = date('d-m-Y').' 23:59:59';
$req = $bdd->prepare('SELECT (c.nbrecodeday - c.nbrecodeused) AS compteur FROM vod_compteur AS c WHERE datepresta = :datepresta');
$req->execute(array('datepresta' => $today));

$compteur = $req->fetch(PDO::FETCH_OBJ);


if($compteur->compteur > $nbre_coupons_temporisation) {
	$resultat ['compteur'] = $compteur->compteur - $nbre_coupons_temporisation;
       
}else {
	$resultat ['compteur'] = 0;
}

$file = "compteur.php";
$header = '<?php header("Access-Control-Allow-Origin: *");';
$json = "echo '".json_encode($resultat)."';";

if(!is_null($resultat ['compteur'])){

	$content = $header.$json;
	file_put_contents($file,$content);
}else {
   $content = $header;
   file_put_contents($file,$content );
 
}




