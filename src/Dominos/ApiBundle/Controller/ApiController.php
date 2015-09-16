<?php

namespace Dominos\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Dominos\VodBundle\Entity\Prestataire;
use Dominos\VodBundle\Entity\Menus;
use Dominos\VodBundle\Entity\Code;
use Dominos\VodBundle\Entity\Compteur;

use Dominos\VodBundle\Repository\PrestataireRepository;
use Dominos\VodBundle\Repository\MenusRepository;
use Dominos\VodBundle\Repository\CodeRepository;
use Dominos\VodBundle\Repository\CompteurRepository;


/**
 * Api controller.
 *
 */
class ApiController extends Controller
{
	// clé c'encryptage du code
    const PASSWORD = "ymog0Ni6ct5w2jI9zF4RNwANqxWbDda8137bpTsS";
    
    // vecteur d'initialisation
    const IV = "f9de5c9e446b06bc";

	public function isOperationAction(){
	 $nbre_coupons_temporisation = $this->container->getParameter('nbre_coupons_temporisation');
	  $em = $this->getDoctrine()->getManager();
	  $operation = $em->getRepository('DominosVodBundle:Compteur')->getIsOperation($nbre_coupons_temporisation);
	  $iv = substr(md5(rand(1,10000)), 0, openssl_cipher_iv_length('aes-256-cbc'));
	  $response = [];
	  if(is_null($operation) ){
	  	$response['code'] = 500;
	  	$response['message'] = "notok";
	  }else {
	  	$response['code'] = 200;
	  	$response['message'] = "ok";
	  	$response['payload'] = openssl_encrypt(json_encode([
									        'prestataire_id' => $operation->getPrestataire()->getId(),
									        'prestataire_type' => $operation->getPrestataire()->getType(),
									    ]), 'aes-256-cbc', md5('868ff34788fccade13a9'),0,$iv). ':' . $iv;

	  }
	  //$payload_decrypt = json_decode(openssl_decrypt(explode(':', $response['payload'])[0],'aes128', md5('868ff34788fccade13a9'),0,explode(':', $response['payload'])[1]),true);
	  //echo '<pre>'.var_dump($response).'</pre><br>';
	  //echo '<pre>'.var_dump($payload_decrypt).'</pre>';

	  $jsonResponse = new JsonResponse();
	  $jsonResponse->setData($response);

	  return $jsonResponse;
	}
	public function menusAction($idmag){
	 // Nombre de coupons pour la temporisation
	  $nbre_coupons_temporisation = $this->container->getParameter('nbre_coupons_temporisation');
	  $em = $this->getDoctrine()->getManager();
	  $menus = $em->getRepository('DominosVodBundle:Menus')->getMenusMagByPresta($idmag,$nbre_coupons_temporisation);
	  $iv = substr(md5(rand(1,10000)), 0, openssl_cipher_iv_length('aes-256-cbc'));
	  $response = [];
	  if(is_null($menus) ){
	  	$response['code'] = 204;
	  	$response['message'] = "notok";
	  	$response['payload']['idmag'] = $idmag;
	  }else {
	  	$response['code'] = 200;
	  	$response['message'] = "ok";
	  	$response['payload']['idmag'] = $idmag;
	  	$response['payload']['prestataire_id'] = $menus->getPrestataire()->getId();
	  	$response['payload']['prestataire_type'] = $menus->getPrestataire()->getType();
	  	$response['payload']['menus']['menu1'] = $menus->getMenu1();
	  	$response['payload']['menus']['menu2'] = $menus->getMenu2();
	  	$response['payload']['menus']['menu3'] = $menus->getMenu3();
	  	$response['payload']['menus']['menu4'] = $menus->getMenu4();
	  	$response['payload'] = openssl_encrypt(json_encode($response['payload']),'aes-256-cbc', md5('868ff34788fccade13a9'),0,$iv). ':' . $iv;
	  }

	  $jsonResponse = new JsonResponse();
	  $jsonResponse->setData($response);

	  return $jsonResponse;
	}


	/*
	* Récuperer un code non grillé
	*/
	public function getCodeAction($idpresta){

		$em = $this->getDoctrine()->getManager();
		$code = $em->getRepository('DominosVodBundle:Code')->getOneCodeByPresta($idpresta);
        $response = [];
      if(is_null($code) ){
        $response['code'] = 204;
        $response['message'] = "notok";
      }else {
        $code->setDatetemp(new \DateTime());
        $em->flush();
        $response['code'] = 200;
        $response['message'] = "ok";
        $response['payload']['code'] = $code->getCode();
        $response['payload']['action'] = "init";

		$iv = substr(md5(rand(1,10000)), 0, openssl_cipher_iv_length('aes-256-cbc'));
	 	$response['payload'] = openssl_encrypt(json_encode($response['payload']),'aes-256-cbc', md5('868ff34788fccade13a9'),0,$iv). ':' . $iv;

	  }
	  $jsonResponse = new JsonResponse();
	  $jsonResponse->setData($response);

	  return $jsonResponse;

	}

	/*
	* Griller un code
	*/
	public function burnCodeAction($payload)
    {

        $codeid = substr($payload, 5) . substr($payload, 0, 5);
        $codeid = openssl_encrypt($codeid, 'aes-256-cbc',self::PASSWORD,0,self::IV);
        // Nombre limite de coupons pour envoi alert email
        $nbre_limit_coupons = $this->container->getParameter('nbre_limit_coupons');
        $nbre_coupons_temporisation = $this->container->getParameter('nbre_coupons_temporisation');
		$nbre_truelimit = $nbre_limit_coupons+$nbre_coupons_temporisation;
        $em = $this->getDoctrine()->getManager();


        $code = $em->getRepository('DominosVodBundle:Code')->findOneByCode($codeid);
        $today = date('d-m-Y') . ' 23:59:59';
        $compteur = $em->getRepository('DominosVodBundle:Compteur')->findOneByDatepresta($today);
        $nbrecodeused = $compteur->getNbrecodeused() + 1;
        $compteur->setNbrecodeused($nbrecodeused);

        $response = [];


        if(!is_null($code)) {

            if(is_null($code->getDateused())){

                $code->setDateused(new \DateTime());
                $em->flush();

                if($compteur->getNbreCodeRestants() == $nbre_truelimit){

   					$content = "Aujourd'hui : " . date('d-m-Y H:i:s') . "<br />Il reste ".$nbre_limit_coupons." coupons.<br /><a href=\"http://www.dominos.fr/op/vod/dominos/web/login\">>>Connexion</a>";
					$to      = 'melanie.dolle@dominos.fr,loic.michel@dominos.fr';
					$subject = 'Alerte VOD 100 coupons';
					$headers  = 'MIME-Version: 1.0' . "\r\n";
	     			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	     			$headers .= 'From: Domino\'s Pizza <administrationvod@dominos.fr>' . "\r\n" ;
	     			$headers .= 'Reply-To: Paul Mad Media <paul@madmedia.fr>' . "\r\n" ;
	     			$headers .= 'Bcc: paul@madmedia.fr' . "\r\n" ;
					mail($to, $subject, $content,$headers);
                }

                $apimessage = "ok";
            } else {
                $apimessage = "already burn";
            }

            $response['code'] = 200;
            $response['message'] = $apimessage;
            $response['payload']['code'] = $code->getCode();
            $response['payload']['action'] = "burned";
		    $iv_encrypt = substr(md5(rand(1,10000)), 0, openssl_cipher_iv_length('aes-256-cbc'));
			$response['payload'] = openssl_encrypt(json_encode($response['payload']), 'aes-256-cbc', md5('868ff34788fccade13a9'), 0, $iv_encrypt) . ':' . $iv_encrypt;

		}else {


			$response['code'] = 500;
			$response['message'] = "notok";
	 	}

	  $jsonResponse = new JsonResponse();
	  $jsonResponse->setData($response);

	  return $jsonResponse;

	}

	public function pushAction(){
		$entryData = array(
		    'category' => "kittensCategory"
		  , 'title'    => "Test titre"
		  , 'article'  => "Contenu"
		  , 'when'     => time()
		);

	    // This is our new stuff
	    $context = new \ZMQContext();
	    $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
	    $socket->connect("tcp://localhost:5557");

	    $socket->send(json_encode($entryData));

	    return $this->render('DominosApiBundle::push.html.twig');
	}


	public function clientAction(){
		
	    return $this->render('DominosApiBundle::client.html.twig');
	}
}