<?php

namespace Dominos\VodBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Dominos\VodBundle\Entity\Compteur;
use Dominos\VodBundle\Form\CompteurType;

/**
 * Compteur controller.
 *
 */
class CompteurController extends Controller
{

    /**
     * Lists all Compteur entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DominosVodBundle:Compteur')->findAll();

        return $this->render('DominosVodBundle:Compteur:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Compteur entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Compteur();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        // Vérifie nbre codes à ventiller < nbre codes restants
        if($this->checkNbreCodesRestants($entity) == false){
            die("nbre codes à ventiller > nbre codes restants");
            return $this->redirect($this->generateUrl('compteur_new'));
        }
        // Vérifie qu'il n'ya qu'un prestataire par jour 
        if($this->checkPrestataireByDay($entity) == false){
            die("il y a déja un prestataire par ce jour");
             return $this->redirect($this->generateUrl('compteur_new'));
        }

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('compteur_show', array('id' => $entity->getId())));
        }

        return $this->render('DominosVodBundle:Compteur:new.html.twig', array(
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
        $form = $this->createForm(new CompteurType(), $entity, array(
            'action' => $this->generateUrl('compteur_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Compteur entity.
     *
     */
    public function newAction()
    {
        $entity = new Compteur();
        $form   = $this->createCreateForm($entity);

        return $this->render('DominosVodBundle:Compteur:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Compteur entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Compteur')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Compteur entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:Compteur:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Compteur entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Compteur')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Compteur entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:Compteur:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
        $form = $this->createForm(new CompteurType(), $entity, array(
            'action' => $this->generateUrl('compteur_update', array('id' => $entity->getId())),
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

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Compteur entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('compteur_edit', array('id' => $id)));
        }

        return $this->render('DominosVodBundle:Compteur:edit.html.twig', array(
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
        $em = $this->getDoctrine()->getManager();
        $prestataire = $compteur->getPrestataire();
        $nbreCodeTotal = $em->getRepository('DominosVodBundle:Code')->NbreTotalDispo($prestataire);
        $nbrecodesventilles = $em->getRepository('DominosVodBundle:Compteur')->NbreCodesVentillesBefore($compteur->getDatepresta());
        $nbrecodesrestants = $nbreCodeTotal - $nbrecodesventilles;

        if($nbrecodesrestants < $compteur->getNbrecodeday()){
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
        $datepresta = $compteur->getDatepresta();
        $em = $this->getDoctrine()->getManager();
        $nbrepresta = $em->getRepository('DominosVodBundle:Compteur')->NbrePrestaByDay($datepresta);
        if ($nbrepresta !=0) {
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
    private function getNbreCodesForEdit(Compteur $compteur){
        $em = $this->getDoctrine()->getManager();
        $datepresta = $compteur->getDatepresta();
        $nbrecodesnotused = $em->getRepository('DominosVodBundle:Compteur')->NbreCodesNotUsed($datepresta);

        return $nbrecodesnotused + $compteur->getNbrecodeday();
    }


}
