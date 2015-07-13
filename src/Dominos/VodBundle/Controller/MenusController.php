<?php

namespace Dominos\VodBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Dominos\VodBundle\Entity\Menus;
use Dominos\VodBundle\Form\MenusType;

/**
 * Menus controller.
 *
 */
class MenusController extends Controller
{

     const PATH_DIR_ROOT_TEMP ='../web/documents/';
    /**
     * Lists all Menus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DominosVodBundle:Menus')->findAll();

        return $this->render('DominosVodBundle:Menus:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Menus entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
       
        $form = $this->createCreateForm();
        $form->handleRequest($request);
        if ($form->isValid()) {

            $file = $form->get('menufile')->getData();
            $prestataire = $form->get('prestataires')->getData();

            if ($request->request->has('replace')){
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

            return $this->redirect($this->generateUrl('menus'));
        }

        return $this->render('DominosVodBundle:Menus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Menus entity.
     *
     * @param Menus $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm()
    {
        $form = $this->createForm(new MenusType(), null, array(
            'action' => $this->generateUrl('menus_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Menus entity.
     *
     */
    public function newAction()
    {
        $form   = $this->createCreateForm();

        return $this->render('DominosVodBundle:Menus:new.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to replace a  Menus entity.
     *
     */
    public function replaceAction()
    {
        $form   = $this->createCreateForm();

        return $this->render('DominosVodBundle:Menus:replace.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Menus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DominosVodBundle:Menus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DominosVodBundle:Menus:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a Menus entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DominosVodBundle:Menus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Menus entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('menus'));
    }

    /**
     * Creates a form to delete a Menus entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('menus_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
