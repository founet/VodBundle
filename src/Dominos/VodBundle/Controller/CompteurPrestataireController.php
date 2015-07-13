<?php

namespace Dominos\VodBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Dominos\VodBundle\Entity\Compteur;
use Dominos\VodBundle\Entity\Prestataire;
use Dominos\VodBundle\Form\CompteurPrestataireType;

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

        $entities = $em->getRepository('DominosVodBundle:Compteur')->findByPrestataire($id,array('datepresta'=>'ASC'));
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

        return $this->render('DominosVodBundle:CompteurPrestataire:index.html.twig', array(
            'entities' => $entities,
            'prestataire' => $prestataire
        ));
    }
    /**
     * Creates a new Compteur entity.
     *
     */
    public function createAction(Request $request,$id)
    {
        $entity = new Compteur();
        $em = $this->getDoctrine()->getManager();
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);
        $entity->setPrestataire($prestataire);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        dump($entity);
        var_dump($this->getNbreCodesDispo($entity));
        die;
        // Vérifie nbre codes à ventiller < nbre codes restants
        if($this->checkNbreCodesRestants($entity) == false){
            die("nbre codes à ventiller > nbre codes restants");
            return $this->redirect($this->generateUrl('compteur_new'));
        }
        // Vérifie qu'il n'ya qu'un prestataire par jour 
        if($this->checkPrestataireByDay($entity) == false){
            die("il y a déja un prestataire pour ce jour");
             return $this->redirect($this->generateUrl('compteur_new'));
        }

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('compteur_show_by_presta', array('idpresta'=>$entity->getPrestataire()->getId(),
                                                                                        'id' => $entity->getId(),
                                                                                        
                                                                                        )));
        }

        return $this->render('DominosVodBundle:CompteurPrestataire:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Compteur entity.
     *
     * @param Compteur $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Compteur $entity)
    {
        
        $form = $this->createForm(new CompteurPrestataireType(), $entity, array(
            'action' => $this->generateUrl('compteur_create_by_presta',array('id'=>$entity->getPrestataire()->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Compteur entity.
     *
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Compteur();
        $prestataire = $em->getRepository('DominosVodBundle:Prestataire')->find($id);
        $entity->setPrestataire($prestataire);
        $form   = $this->createCreateForm($entity);
        return $this->render('DominosVodBundle:CompteurPrestataire:new.html.twig', array(
            'entity' => $entity,
            'prestataire' => $prestataire,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Compteur entity.
     *
     */
    public function showAction($idpresta, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Compteur')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Compteur entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:CompteurPrestataire:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Compteur entity.
     *
     */
    public function editAction($idpresta,$id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Compteur')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Compteur entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:CompteurPrestataire:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'nbrecodesrestants' => $this->getNbreCodesDispo($entity)
        ));
    }

    /**
    * Creates a form to edit a Compteur entity.
    *
    * @param Compteur $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Compteur $entity)
    {

        $form = $this->createForm(new CompteurPrestataireType(), $entity, array(
            'action' => $this->generateUrl('compteur_update_by_presta', array('idpresta'=>$entity->getPrestataire()->getId(),
                                                                              'id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Compteur entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('DominosVodBundle:Compteur')->find($id);
        // Vérifie nbre codes à ventiller < nbre codes restants
        if($this->checkNbreCodesRestants($entity) == false){
            die("nbre codes à ventiller > nbre codes restants");
            return $this->redirect($this->generateUrl('compteur_new'));
        }

        // Vérifie qu'il n'ya qu'un prestataire par jour 
        if($this->checkPrestataireByDay($entity) == false){
            die("il y a déja un prestataire pour ce jour");
             return $this->redirect($this->generateUrl('compteur_new'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Compteur entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('compteur_show_by_presta', array('idpresta'=>$entity->getPrestataire()->getId(),
                                                                                       'id' => $entity->getId())));
        }

        return $this->render('DominosVodBundle:CompteurPrestataire:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Compteur entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DominosVodBundle:Compteur')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Compteur entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('compteur'));
    }

    /**
     * Creates a form to delete a Compteur entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('compteur_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
     /**
     * Vérifie que le nombre de codes à ventiller est inférieur ou égal au nombre de codes restants.
     * @param Compteur $compteur
     * @return Boolean 
     */
    private function checkNbreCodesRestants(Compteur $compteur){

        $nbrecodesrestants = $this->getNbreCodesDispo($compteur);

        if($compteur->getNbreCodeRestants() > $nbrecodesrestants ){
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
        if ($nbrepresta != 0) {
            return false;
        }else {
            return true;
        }
    }

    /**
     * Recupère le nombre de codes dispo à l'édition
     * @param Compteur $compteur
     * @return Integer 
     */
    private function getNbreCodesDispo(Compteur $compteur){
        $em = $this->getDoctrine()->getManager();
        $nbrecodesnotused = $em->getRepository('DominosVodBundle:Compteur')->NbreCodesNotUsed($compteur);
        if($compteur->getId()!= null) {
            return $nbrecodesnotused + $compteur->getNbreCodeRestants() + $this->getNbreCodesNonVentilles($compteur->getPrestataire());
        }
        else {
            return  $nbrecodesnotused + $this->getNbreCodesNonVentilles($compteur->getPrestataire());
        }
    }

    /**
     * Recupère le nombre de codes non ventillés
     * @param Compteur $compteur
     * @return Integer 
     */
    private function getNbreCodesNonVentilles(Prestataire $prestataire){
        $em = $this->getDoctrine()->getManager();
        $nbreCodeTotal = $em->getRepository('DominosVodBundle:Code')->NbreTotalDispo($prestataire);
        $nbreCodesVentilles = $em->getRepository('DominosVodBundle:Compteur')->NbreCodesVentillesByPresta($prestataire);
        $NbreCodesNonVentilles = $nbreCodeTotal - $nbreCodesVentilles;
        
        return $NbreCodesNonVentilles;
    }

}
