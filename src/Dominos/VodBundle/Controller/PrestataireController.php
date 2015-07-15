<?php

namespace Dominos\VodBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Dominos\VodBundle\Entity\Prestataire;
use Dominos\VodBundle\Form\PrestataireType;
use Dominos\VodBundle\Entity\Menus;
use Dominos\VodBundle\Form\MenusType;
use Dominos\VodBundle\Entity\Code;
use Dominos\VodBundle\Form\CodeType;

/**
 * Prestataire controller.
 *
 */
class PrestataireController extends Controller
{
    const PATH_DIR_ROOT_TEMP ='../web/documents/';

    /**
     * Lists all Prestataire entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DominosVodBundle:Prestataire')->findAll();

        return $this->render('DominosVodBundle:Prestataire:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Prestataire prestataire.
     *
     */
    public function createAction(Request $request)
    {
        $prestataire = new Prestataire();
        $formpresta = $this->createPrestaForm($prestataire);
        $formpresta->handleRequest($request);

        if ($formpresta->isValid()) {
           if($this->checkPrestaPeriod($prestataire)){
                $em = $this->getDoctrine()->getManager();
                $em->persist($prestataire);
                $em->flush();

                return $this->redirect($this->generateUrl('prestataire_new', array('id' => $prestataire->getId())));
           }else {
              $this->get('session')
                    ->getFlashBag()
                    ->add('error', 'Inscription impossible : déjà deux prestataires sur cette période');
                    return $this->redirect($this->generateUrl('prestataire_new'));
           }
        }

        return $this->render('DominosVodBundle:Prestataire:new.html.twig', array(
            'prestataire' => $prestataire,
            'formpresta'   => $formpresta->createView(),
        ));
    }

    /**
     * Creates a formpresta to create a Prestataire prestataire.
     *
     * @param Prestataire $prestataire The prestataire
     *
     * @return \Symfony\Component\Form\Form The formpresta
     */
    private function createPrestaForm(Prestataire $prestataire)
    {
        if(is_null($prestataire->getId())) {
            $route = 'prestataire_create';
            $label = 'Ajouter';
        }else {
            $route = 'prestataire_update';
            $label = 'Modifier';
        }
        $formpresta = $this->createForm(new PrestataireType(), $prestataire, array(
            'action' => $this->generateUrl($route,array('id'=>$prestataire->getId())),
            'method' => 'POST',
        ));

        $formpresta->add('submit', 'submit', array('label' => $label));

        return $formpresta;
    }

    /**
     * Displays a formpresta to create a new Prestataire prestataire.
     *
     */
    public function newAction($id)
    {
        if($id){
            $em = $this->getDoctrine()->getManager();
            $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);
        }else { $prestataire = new Prestataire(); }
      
        $formpresta   = $this->createPrestaForm($prestataire);
        $formmenu = $this->createMenuCreateForm($prestataire);
        $formcode = $this->createCodeCreateForm($prestataire);

        return $this->render('DominosVodBundle:Prestataire:new.html.twig', array(
            'prestataire' => $prestataire,
            'formpresta'   => $formpresta->createView(),
            'formmenu'     =>$formmenu->createView(),
            'formcode'     =>$formcode->createView(),
        ));
    }

    /**
     * Finds and displays a Prestataire prestataire.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

        if (!$prestataire) {
            throw $this->createNotFoundException('Unable to find Prestataire prestataire.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:Prestataire:show.html.twig', array(
            'prestataire'      => $prestataire,
            'delete_formpresta' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a formpresta to edit an existing Prestataire prestataire.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

        if (!$prestataire) {
            throw $this->createNotFoundException('Unable to find Prestataire prestataire.');
        }

        $editForm = $this->createPrestaForm($prestataire);
        $formmenu = $this->createMenuCreateForm($prestataire);
        $formcode = $this->createCodeCreateForm($prestataire);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:Prestataire:edit.html.twig', array(
            'prestataire'      => $prestataire,
            'formpresta'   => $editForm->createView(),
            'delete_formpresta' => $deleteForm->createView(),
            'formmenu'     =>$formmenu->createView(),
            'formcode'     =>$formcode->createView(),
        ));
    }


    /**
     * Edits an existing Prestataire prestataire.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

        if (!$prestataire) {
            throw $this->createNotFoundException('Unable to find Prestataire prestataire.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createPrestaForm($prestataire);
        $formmenu = $this->createMenuCreateForm($prestataire);
        $formcode = $this->createCodeCreateForm($prestataire);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($this->checkPrestaPeriod($prestataire)){
               $em->flush();  
           }else {
                die("Inscription impossible : déjà deux prestataires sur cette période");
           }
           
            return $this->redirect($this->generateUrl('prestataire_edit', array('id' => $id)));
        }

        return $this->render('DominosVodBundle:Prestataire:edit.html.twig', array(
            'prestataire'      => $prestataire,
            'edit_formpresta'   => $editForm->createView(),
            'delete_formpresta' => $deleteForm->createView(),
            'formmenu'     =>$formmenu->createView(),
            'formcode'     =>$formcode->createView(),
        ));
    }
    /**
     * Deletes a Prestataire prestataire.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $formpresta = $this->createDeleteForm($id);
        $formpresta->handleRequest($request);

        if ($formpresta->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

            if (!$prestataire) {
                throw $this->createNotFoundException('Unable to find Prestataire prestataire.');
            }

            $em->remove($prestataire);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('prestataire'));
    }

    /**
     * Creates a formpresta to delete a Prestataire prestataire by id.
     *
     * @param mixed $id The prestataire id
     *
     * @return \Symfony\Component\Form\Form The formpresta
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('prestataire_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    /**
     * Vérifie que la période du Prestataire ne chevauche pas avec plus d'un prestataire.
     *
     * @param Prestataire $prestataire 
     *
     * @return Boolean
     */
    private function checkPrestaPeriod(Prestataire $prestataire){
         $em = $this->getDoctrine()->getManager();
         $nbrePresta = $em->getRepository('DominosVodBundle:Prestataire')->checkPrestaPeriod($prestataire);

         if($nbrePresta < 2 ){
             return true;
         } 
         else {
            return false;
        }
    }

    /**
     * Retourne toutes les dates d'un prestataire sur une période.
     *
     * @param mixed $id The prestataire id
     *
     * @return JSON 
     */

    public function prestaPeriodAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);
        $date = $prestataire->getStartpresta();
        $end_date = $prestataire->getEndpresta();
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($date, $interval ,$end_date);
        $dates = array();
        foreach($daterange as $date){
           $dates[] = $date->format('Y-m-d H:i:s');
        }
        
        $response = new JsonResponse();
        $response->setData($dates);
        return $response;
    }


        /**
     * Creates a formmenu to create a Menus prestataire.
     *
     *
     * @return \Symfony\Component\Form\Form The formmenu
     */
    private function createMenuCreateForm(Prestataire $prestataire)
    {
        $id = (is_null($prestataire->getId())) ? 0 : $prestataire->getId();


        $formmenu = $this->createForm(new MenusType(), null, array(
            'action' => $this->generateUrl('menus_create',array('idpresta'=>$id)),
            'method' => 'POST',
        ));

        if($prestataire->haveMenus() != "OK"){
           $label = "Ajouter"; 
        }else {
            $label = "Remplacer";
            $formmenu->add('replace','hidden',array('data'=>'replace'));
        }

        $formmenu->add('submit', 'submit', array('label' => $label));

        return $formmenu;
    }

        /**
     * Creates a form to create a Code entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCodeCreateForm(Prestataire $prestataire)
    {
        $id = (is_null($prestataire->getId())) ? 0 : $prestataire->getId();

        $form = $this->createForm(new CodeType(), null, array(
            'action' => $this->generateUrl('code_create',array('idpresta'=>$id)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates Menus entities.
     *
     */
    public function createMenuAction(Request $request,$idpresta)
    {
        $em = $this->getDoctrine()->getManager();
        $prestataire =  $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($idpresta);
        $form = $this->createMenuCreateForm($prestataire);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $file = $form->get('menufile')->getData();
            $replace = $form->has('replace');
            if ($replace){
                $menus_presta = $prestataire->getMenusPresta();
                foreach ($menus_presta as $menu_presta) {
                    $em->remove($menu_presta);
                }
                $em->flush();
            }
            
            $nameFile = sha1(uniqid(mt_rand(), true)).'.'.$file->guessExtension();
            $docPath = self::PATH_DIR_ROOT_TEMP;
            $file->move($docPath, $nameFile);
            $filepath = $docPath.$nameFile;
            $csv= file_get_contents($filepath);
        
            $presta_menus = array_map("str_getcsv", explode("\n", $csv));

           for ($i=1; $i < count($presta_menus); $i++) { 
              if(!empty($presta_menus[$i][0])){
                   $entity = new Menus();
                   $entity->setMagnum($presta_menus[$i][0]);
                   $entity->setPrestataire($prestataire);
                   $entity->setMenu1($presta_menus[$i][1]);
                   $entity->setMenu2($presta_menus[$i][2]);
                   $entity->setMenu3($presta_menus[$i][3]);
                   $entity->setMenu4($presta_menus[$i][4]);
                   $em->persist($entity);
             }
           }
           $em->flush();

            return $this->redirect($this->generateUrl('prestataire_new', array('id' => $prestataire->getId())));
        }

        
    }


        /**
     * Creates Codes entities.
     *
     */
    public function createCodeAction(Request $request,$idpresta)
    {
        $em = $this->getDoctrine()->getManager();
        $prestataire =  $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($idpresta);
        $form = $this->createCodeCreateForm($prestataire);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $file = $form->get('codefile')->getData();

            if ($request->request->has('replace')){
                $codes = $prestataire->getCodes();
                foreach ($codes as $code) {
                    $em->remove($code);
                }
                $em->flush();
            }
            
            $nameFile = sha1(uniqid(mt_rand(), true)).'.'.$file->guessExtension();
            $docPath = self::PATH_DIR_ROOT_TEMP;
            $file->move($docPath, $nameFile);
            $filepath = $docPath.$nameFile;
            $csv= file_get_contents($filepath);
        
            $codes = array_map("str_getcsv", explode("\n", $csv));

           for ($i=1; $i < count($codes); $i++) { 
              if(!empty($codes[$i][0])){
                    $entity = new Code();
                    $entity->setCode($codes[$i][0]);
                    $entity->setPrestataire($prestataire);
                    $entity->setDateused(null);

                    $em->persist($entity);
             }
           }
           $em->flush();

             return $this->redirect($this->generateUrl('prestataire_new', array('id' => $prestataire->getId())));
        }

        
    }
}
