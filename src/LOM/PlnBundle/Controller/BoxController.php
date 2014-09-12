<?php

namespace LOM\PlnBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOM\PlnBundle\Entity\Box;
use LOM\PlnBundle\Form\BoxType;

/**
 * Box controller.
 *
 */
class BoxController extends Controller
{

    /**
     * Lists all Box entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOMPlnBundle:Box')->findAll();

        return $this->render('LOMPlnBundle:Box:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Box entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Box();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('box_show', array('id' => $entity->getId())));
        }

        return $this->render('LOMPlnBundle:Box:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Box entity.
     *
     * @param Box $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Box $entity)
    {
        $form = $this->createForm(new BoxType(), $entity, array(
            'action' => $this->generateUrl('box_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Box entity.
     *
     */
    public function newAction()
    {
        $entity = new Box();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOMPlnBundle:Box:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Box entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMPlnBundle:Box')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOMPlnBundle:Box:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Box entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMPlnBundle:Box')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOMPlnBundle:Box:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Box entity.
    *
    * @param Box $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Box $entity)
    {
        $form = $this->createForm(new BoxType(), $entity, array(
            'action' => $this->generateUrl('box_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Box entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMPlnBundle:Box')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('box_edit', array('id' => $id)));
        }

        return $this->render('LOMPlnBundle:Box:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Box entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOMPlnBundle:Box')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Box entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('box'));
    }

    /**
     * Creates a form to delete a Box entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('box_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
