<?php

namespace Dominos\VodBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Dominos\VodBundle\Entity\Prestataire;
use Dominos\VodBundle\Form\PrestataireType;

/**
 * Prestataire controller.
 *
 */
class PrestataireController extends Controller
{

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
     * Creates a new Prestataire entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Prestataire();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
           if($this->checkPrestaPeriod($entity)){
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('prestataire_show', array('id' => $entity->getId())));
           }else {
            die("Inscription impossible : déjà deux prestataires sur cette période");
           }
        }

        return $this->render('DominosVodBundle:Prestataire:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Prestataire entity.
     *
     * @param Prestataire $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Prestataire $entity)
    {
        $form = $this->createForm(new PrestataireType(), $entity, array(
            'action' => $this->generateUrl('prestataire_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Prestataire entity.
     *
     */
    public function newAction()
    {
        $entity = new Prestataire();
        $form   = $this->createCreateForm($entity);

        return $this->render('DominosVodBundle:Prestataire:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Prestataire entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Prestataire entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:Prestataire:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Prestataire entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Prestataire entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:Prestataire:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Prestataire entity.
    *
    * @param Prestataire $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Prestataire $entity)
    {
        $form = $this->createForm(new PrestataireType(), $entity, array(
            'action' => $this->generateUrl('prestataire_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Prestataire entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Prestataire entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($this->checkPrestaPeriod($entity)){
               $em->flush();  
           }else {
                die("Inscription impossible : déjà deux prestataires sur cette période");
           }
           
            return $this->redirect($this->generateUrl('prestataire_edit', array('id' => $id)));
        }

        return $this->render('DominosVodBundle:Prestataire:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Prestataire entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DominosVodBundle:Prestataire')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Prestataire entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('prestataire'));
    }

    /**
     * Creates a form to delete a Prestataire entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
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
     * @param mixed $id The entity id
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
}
