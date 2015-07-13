<?php

namespace Dominos\VodBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Dominos\VodBundle\Entity\Code;
use Dominos\VodBundle\Form\CodeType;

/**
 * Code controller.
 *
 */
class CodeController extends Controller
{
    const PATH_DIR_ROOT_TEMP ='../web/documents/';
    /**
     * Lists all Code entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DominosVodBundle:Code')->findAll();

        return $this->render('DominosVodBundle:Code:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Code entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
       
        $form = $this->createCreateForm();
        $form->handleRequest($request);
        if ($form->isValid()) {

            $file = $form->get('codefile')->getData();
            $prestataire = $form->get('prestataires')->getData();

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

            return $this->redirect($this->generateUrl('code'));
        }

        return $this->render('DominosVodBundle:Code:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Code entity.
     *
     * @param Code $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm()
    {
        $form = $this->createForm(new CodeType(), null, array(
            'action' => $this->generateUrl('code_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Code entity.
     *
     */
    public function newAction()
    {
        $form   = $this->createCreateForm();

        return $this->render('DominosVodBundle:Code:new.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to replace a  Code entity.
     *
     */
    public function replaceAction()
    {
        $form   = $this->createCreateForm();

        return $this->render('DominosVodBundle:Code:replace.html.twig', array(
            'form'   => $form->createView(),
        ));
    }
    /**
     * Finds and displays a Code entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Code')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Code entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:Code:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a Code entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DominosVodBundle:Code')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Code entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('code'));
    }

    /**
     * Creates a form to delete a Code entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('code_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
