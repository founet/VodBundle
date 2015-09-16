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
        $em->getRepository('DominosVodBundle:Code')->RAZDateTemp();

        $compteur = new Compteur();
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);
        $entities = $em->getRepository('DominosVodBundle:Compteur')->findByPresta($prestataire);
        $compteur->setPrestataire($prestataire);
        $formAdd = $this->createCreateForm($compteur);

        return $this->render('DominosVodBundle:CompteurPrestataire:index.html.twig', array(
            'prestataire' => $prestataire,
            'formAdd'=>$formAdd->createView(),
            'entities'=>$entities,
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
            ->add('error', 'Le nombre de codes à ventiller est supérieur au nombre de codes disponibles');
            return $this->redirect($this->generateUrl('compteur_index_by_presta',array('id'=>$id)));
        }
        // Vérifie qu'il n'ya qu'un prestataire par jour 
        if($this->checkPrestataireByDay($compteur) == false){
             $this->get('session')
            ->getFlashBag()
            ->add('error', 'Des codes ont déjà été ventillés pour ce jour : Opération impossible');
             return $this->redirect($this->generateUrl('compteur_index_by_presta',array('id'=>$id)));
        }

        if ($form->isValid()) {

            $em->persist($compteur);
            $em->flush();
             $this->get('session')
                ->getFlashBag()
                ->add('success', 'Ventillation de codes reussie');
            return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId(),
                                                                                        
                                                                                        
                                                                                        )));
        }
        $entities = $em->getRepository('DominosVodBundle:Compteur')->findByPrestataire($id,array('datepresta'=>'ASC'));
        
        $formAdd = $this->createCreateForm($compteur);
        

        return $this->render('DominosVodBundle:CompteurPrestataire:index.html.twig', array(
            'prestataire' => $prestataire,
            'formAdd'=>$formAdd->createView(),
            'entities'=>$entities,
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

        $form = $this->createForm(new CompteurPrestataireType(), $compteur, array(
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

        $form = $this->createForm(new CompteurPrestataireType(), $compteur, array(
            'action' => $this->generateUrl('compteur_update_by_presta', array('idpresta'=>$compteur->getPrestataire()->getId(),
                                                                              'id' => $compteur->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Modifier','attr'=>array('class'=>'btn btn-info nomargin')));

        return $form;
    }
    /**
    * Creates a form edit render Action 
    *
    * @param Compteur $compteur The compteur
    *
    * @return \Symfony\Component\Form\Form The form
    */
    public function editFormAction(Compteur $compteur)
    {

       $editForm = $this->createEditForm($compteur);

         return $this->render('DominosVodBundle:CompteurPrestataire:editform.html.twig', array(
            'formEdit'=>$editForm->createView(),
            'compteur'=>$compteur,
        ));
    }


    /**
     * Edits an existing Compteur compteur.
     *
     */
    public function updateAction(Request $request, $id,$idpresta)
    {

        $em = $this->getDoctrine()->getManager();
        $compteur = $em->getRepository('DominosVodBundle:Compteur')->find($id);
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($idpresta);
        $compteur->setPrestataire($prestataire);
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($compteur);
        $editForm->handleRequest($request);

        // Vérifie qu'il reste des codes à griller
        if($compteur->getNbreCodeRestants() < 0){
            $this->get('session')
            ->getFlashBag()
            ->add('error', 'Tous les codes disponibles pour ce jour ont déjà été utilisés');
            return $this->redirect($this->generateUrl('compteur_index_by_presta',array('id'=>$idpresta)));
        }

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
            ->add('error', 'Le nombre de codes à ventiller est supérieur au nombre de codes disponibles');
            return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId()
                                                                                        )));
        }
        // Vérifie qu'il n'ya qu'un prestataire par jour 
        if($this->checkPrestataireByDay($compteur) == false){
             $this->get('session')
            ->getFlashBag()
           ->add('error', 'Des codes ont déjà été ventillés pour ce jour : Opération impossible');
             return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId()
                                                                                        
                                                                                        )));
        }

        if (!$compteur) {
            throw $this->createNotFoundException('Unable to find Compteur compteur.');
        }


        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')
                ->getFlashBag()
                ->add('success', 'Ventillation de codes reussie');
            return $this->redirect($this->generateUrl('compteur_index_by_presta', array('id'=>$compteur->getPrestataire()->getId(),
                                                                                       )));
        }

        $formsEdit = array();
        $entities = $em->getRepository('DominosVodBundle:Compteur')->findByPrestataire($idpresta,array('datepresta'=>'ASC'));
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($idpresta);

        $formAdd = $this->createCreateForm($compteur);
        

        return $this->render('DominosVodBundle:CompteurPrestataire:index.html.twig', array(
            'prestataire' => $prestataire,
            'formAdd'=>$formAdd->createView(),
            'entities'=>$entities,
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
    * Creates a form delete render Action 
    *
    * @param Compteur $id The compteur id
    *
    * @return \Symfony\Component\Form\Form The form
    */
    public function deleteFormAction($id)
    {

       $deleteForm = $this->createDeleteForm($id);

         return $this->render('DominosVodBundle:CompteurPrestataire:deleteForm.html.twig', array(
            'deleteForm'=>$deleteForm->createView(),
        ));
    }

     /**
     * Vérifie que le nombre de codes à ventiller est inférieur ou égal au nombre de codes restants.
     * @param Compteur $compteur
     * @return Boolean 
     */
    private function checkNbreCodesRestants(Compteur $compteur){

        $nbrecodesrestants = $compteur->getPrestataire()->getNbreCodesDispo();

        if(!is_null($compteur->getId())){
            if($nbrecodesrestants < 0){
                return false;
            }else {
                return true;
            }
        }else {
        
            if($nbrecodesrestants < $compteur->getNbrecodeday()){
                return false;
            }else {
                return true;
            }
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
       if($nbrepresta == 0) {
        return true;
       }
        if($nbrepresta == 1) {
            if($compteur->getId()!= null){
                return true;
            }else {
                return false;
            }
        }
    
    }


}
