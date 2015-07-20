<?php

namespace Dominos\VodBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Dominos\VodBundle\Entity\Prestataire;
use Dominos\VodBundle\Form\CompteurPrestataireType;
use Dominos\VodBundle\Entity\Code;
use Dominos\VodBundle\Entity\Compteur;

/**
 * Compteur controller.
 *
 */
class CompteurPrestataireController extends Controller
{

    /**
     * Lists all Compteur entities.
     *
     */
    public function indexAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $compteur = new Compteur();
        $entities = $em->getRepository('DominosVodBundle:Compteur')->findByPrestataire($id,array('datepresta'=>'ASC'));
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);
        $compteur->setPrestataire($prestataire);
        $formAdd = $this->createCreateForm($compteur);
        $formsEdit = array();
        foreach ($entities as $entity) {
            $formEdit = $this->createEditForm($entity)->createView();
            $deleteForm = $this->createDeleteForm($entity->getId())->createView();
            $formsEdit[] = array($formEdit,$deleteForm);
        }
       
        return $this->render('DominosVodBundle:CompteurPrestataire:index.html.twig', array(
            'prestataire' => $prestataire,
            'formAdd'=>$formAdd->createView(),
            'formsEdit'=>$formsEdit,
        ));
    }
    /**
     * Creates a new Compteur compteur.
     *
     */
    public function createAction(Request $request,$id)
    {
        $compteur = new Compteur();
        $em = $this->getDoctrine()->getManager();
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);
        $compteur->setPrestataire($prestataire);
        $form = $this->createCreateForm($compteur);
        $form->handleRequest($request);
       
        // Vérifie nbre codes à ventiller est différent de zéro
        if($compteur->getNbrecodeday() == 0){
            $this->get('session')
            ->getFlashBag()
            ->add('error', 'Le nombre codes à ventiller doit être supérieur à zéro');
            return $this->redirect($this->generateUrl('compteur_index_by_presta',array('id'=>$id)));
        }

        // Vérifie nbre codes à ventiller < nbre codes restants
        if($this->checkNbreCodesRestants($compteur) == false){
            $this->get('session')
            ->getFlashBag()
            ->add('error', 'Le nombre de codes à ventiller est supérieur au nombre de codes restants');
            return $this->redirect($this->generateUrl('compteur_index_by_presta',array('id'=>$id)));
        }
        // Vérifie qu'il n'ya qu'un prestataire par jour 
        if($this->checkPrestataireByDay($compteur) == false){
             $this->get('session')
            ->getFlashBag()
            ->add('error', 'Il y a déjà un prestataire pour ce jour');
             return $this->redirect($this->generateUrl('compteur_index_by_presta',array('id'=>$id)));
        }

        if ($form->isValid()) {

            $em->persist($compteur);
            $em->flush();

            return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId(),
                                                                                        
                                                                                        
                                                                                        )));
        }
        $formsEdit = array();
        $entities = $em->getRepository('DominosVodBundle:Compteur')->findByPrestataire($id,array('datepresta'=>'ASC'));
        foreach ($entities as $compteur) {
            $formEdit = $this->createEditForm($compteur)->createView();
            $deleteForm = $this->createDeleteForm($compteur->getId())->createView();
           $formsEdit[] = array($formEdit,$deleteForm);
        }
        $formAdd = $this->createCreateForm($compteur);
        

        return $this->render('DominosVodBundle:CompteurPrestataire:index.html.twig', array(
            'prestataire' => $prestataire,
            'formAdd'=>$formAdd->createView(),
            'formsEdit'=>$formsEdit,
        ));
    }

    /**
     * Creates a form to create a Compteur compteur.
     *
     * @param Compteur $compteur The compteur
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Compteur $compteur)
    {

        $form = $this->createForm(new CompteurPrestataireType($compteur->getPrestataire()->getPrestaPeriod()), $compteur, array(
            'action' => $this->generateUrl('compteur_create_by_presta',array('id'=>$compteur->getPrestataire()->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Ajouter','attr'=>array('class'=>'btn btn-info')));

        return $form;
    }


    /**
    * Creates a form to edit a Compteur compteur.
    *
    * @param Compteur $compteur The compteur
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Compteur $compteur)
    {

        $form = $this->createForm(new CompteurPrestataireType($compteur->getPrestataire()->getPrestaPeriod()), $compteur, array(
            'action' => $this->generateUrl('compteur_update_by_presta', array('idpresta'=>$compteur->getPrestataire()->getId(),
                                                                              'id' => $compteur->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Modifier','attr'=>array('class'=>'btn btn-info')));

        return $form;
    }
    /**
     * Edits an existing Compteur compteur.
     *
     */
    public function updateAction(Request $request, $id,$idpresta)
    {

        $em = $this->getDoctrine()->getManager();
        $compteur = $em->getRepository('DominosVodBundle:Compteur')->find($id);
       
        // Vérifie nbre codes à ventiller est différent de zéro
        if($compteur->getNbrecodeday() == 0){
            $this->get('session')
            ->getFlashBag()
            ->add('error', 'Le nombre codes à ventiller doit être supérieur à zéro');
            return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId()
                                                                                        
                                                                                        )));
        }
        
       // Vérifie nbre codes à ventiller < nbre codes restants
        if($this->checkNbreCodesRestants($compteur) == false){

            $this->get('session')
            ->getFlashBag()
            ->add('error', 'Le nombre de codes à ventiller est supérieur au nombre de codes restants');
            return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId()
                                                                                        )));
        }
        // Vérifie qu'il n'ya qu'un prestataire par jour 
        if($this->checkPrestataireByDay($compteur) == false){
             $this->get('session')
            ->getFlashBag()
            ->add('error', 'Il y a déjà un prestataire pour ce jour');
             return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId()
                                                                                        
                                                                                        )));
        }

        if (!$compteur) {
            throw $this->createNotFoundException('Unable to find Compteur compteur.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($compteur);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId(),
                                                                                       )));
        }

        $formsEdit = array();
        $entities = $em->getRepository('DominosVodBundle:Compteur')->findByPrestataire($idpresta,array('datepresta'=>'ASC'));
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($idpresta);
        foreach ($entities as $compteur) {
            $formEdit = $this->createEditForm($compteur)->createView();
            $deleteForm = $this->createDeleteForm($compteur->getId())->createView();
            $formsEdit[] = array($formEdit,$deleteForm);
        }

        $formAdd = $this->createCreateForm($compteur);
        

        return $this->render('DominosVodBundle:CompteurPrestataire:index.html.twig', array(
            'prestataire' => $prestataire,
            'compteur'=> $compteur,
            'formAdd'=>$formAdd->createView(),
            'formsEdit'=>$formsEdit,
        ));
    }
    /**
     * Deletes a Compteur compteur.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $compteur = $em->getRepository('DominosVodBundle:Compteur')->find($id);

        if (!$compteur) {
            throw $this->createNotFoundException('Unable to find Compteur compteur.');
        }

        $em->remove($compteur);
        $em->flush();
  

     $response = new Response('ok');

     return $response ;
    }

    /**
     * Creates a form to delete a Compteur compteur by id.
     *
     * @param mixed $id The compteur id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('compteur_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer','attr'=>array('class'=>'btn btn-info compteurdelete','data-id'=>$id)))
            ->getForm()
        ;
    }
     /**
     * Vérifie que le nombre de codes à ventiller est inférieur ou égal au nombre de codes restants.
     * @param Compteur $compteur
     * @return Boolean 
     */
    private function checkNbreCodesRestants(Compteur $compteur){

        $nbrecodesrestants = $compteur->getPrestataire()->getNbreCodesDispo();
        if(!is_null($compteur->getId())){
             $nbrecodesrestants = $nbrecodesrestants + $compteur->getNbreCodeRestants();
        }
        if($compteur->getNbreCodeRestants() > $nbrecodesrestants  ){
            return false;
        }else {
            return true;
        }
    }
    /**
     * Vérifie qu'il n'y a qu'un prestataire par jour 
     * @param Compteur $compteur
     * @return Boolean 
     */
    private function checkPrestataireByDay(Compteur $compteur){
        $em = $this->getDoctrine()->getManager();
        $nbrepresta = $em->getRepository('DominosVodBundle:Compteur')->NbrePrestaByDay($compteur);
        if($compteur->getId() == null){
            $flag = ($nbrepresta == 0) ? true : false;
        }else {
          $flag = ($nbrepresta == 1) ? true : false;
        }
        
        return $flag;
    }


}
