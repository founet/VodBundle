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
	// Nombre de coupons pour la temposrisation
	const NBRE_COUPONS_TEMPORISATION = 0;

	// Nombre limite de coupons pour envoi alert email
	const NBRE_LIMIT_COUPONS = 1;


	public function menusAction($idmag){

	  $em = $this->getDoctrine()->getManager();
	  $menus = $em->getRepository('DominosVodBundle:Menus')->getMenusMagByPresta($idmag,self::NBRE_COUPONS_TEMPORISATION);

	  $response = [];
	  if(is_null($menus) ){
	  	$response['code'] = 204;
	  	$response['message'] = "notok";
	  	$response['payload']['idmag'] = $idmag;
	  }else {
	  	$response['code'] = 200;
	  	$response['message'] = "ok";
	  	$response['payload']['idmag'] = $idmag;
	  	$response['payload']['prestataire'] = $menus->getPrestataire()->getId();
	  	$response['payload']['menus']['menu1'] = $menus->getMenu1();
	  	$response['payload']['menus']['menu2'] = $menus->getMenu2();
	  	$response['payload']['menus']['menu3'] = $menus->getMenu3();
	  	$response['payload']['menus']['menu4'] = $menus->getMenu4();

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
	 

	  }

	  $jsonResponse = new JsonResponse();
	  $jsonResponse->setData($response);

	  return $jsonResponse;
		
	}

	/*
	* Grillé un code 
	*/
	public function burnCodeAction($payload){

		$em = $this->getDoctrine()->getManager();

		// Décryptage du payload à faire 

		//$payload = json_decode($payload);
		//$code = $payload->code;
		$codeid = $payload;
		$code = $em->getRepository('DominosVodBundle:Code')->findOneByCode($codeid);
		$today = date('d-m-Y').' 23:59:59';
		$compteur = $em->getRepository('DominosVodBundle:Compteur')->findOneByDatepresta($today);
		$nbrecodeused = $compteur->getNbrecodeused() + 1;
		$compteur->setNbrecodeused($nbrecodeused);

		$response = [];

		try {

			if(is_null($code->getDateused())){

				$code->setDateused(new \DateTime());
				$em->flush();

				if($compteur->getNbreCodeRestants() == self::NBRE_LIMIT_COUPONS){
					$sujet = "Test";
					$From = "aobmilan@gmail.com";
					$recipients = array('bah.founet@gmail.com','amadou@madmedia.fr');
					$content = "Message alert";
					$message = \Swift_Message::newInstance()
						        ->setSubject($sujet)
						        ->setFrom($From)
						        ->setTo($recipients)
						        ->setBody($content);
				    $this->get('mailer')->send($message);

				}

				$apimessage = "ok";
			} else {
				$apimessage = "already burn";
			}
			
			$response['code'] = 200;
			$response['message'] = $apimessage;
			$response['payload']['code'] = $code->getCode();
			$response['payload']['action'] = "burned";
			
		}catch(Exception $e) {

			$response['code'] = 500;
			$response['message'] = "notok";
	 	}

	  $jsonResponse = new JsonResponse();
	  $jsonResponse->setData($response);

	  return $jsonResponse;
		
	}

}