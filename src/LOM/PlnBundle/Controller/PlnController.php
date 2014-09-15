<?php

namespace LOM\PlnBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use LOM\PlnBundle\Entity\Pln;
use LOM\PlnBundle\Entity\Box;
use LOM\PlnBundle\Form\PlnType;
use LOM\PlnBundle\Form\AddBoxType;

/**
 * Pln controller.
 *
 */
class PlnController extends Controller
{

    /**
     * Lists all Pln entities.
     *
     */
    public function indexAction()
    {        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOMPlnBundle:Pln')->findAll();

        return $this->render('LOMPlnBundle:Pln:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Pln entity.
     *
     */
    public function createAction(Request $request)
    {
        $securityContext = $this->get('security.context');
        $objectId = new ObjectIdentity('class', 'LOM\\PlnBundle\\Entity\\Pln');
        if(false === $securityContext->isGranted('CREATE', $objectId)) {
            throw new AccessDeniedException("You do not have permission to create Plns.");
        }
        
        $entity = new Pln();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pln_show', array('id' => $entity->getId())));
        }

        return $this->render('LOMPlnBundle:Pln:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Pln entity.
     *
     * @param Pln $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pln $entity)
    {
        $form = $this->createForm(new PlnType(), $entity, array(
            'action' => $this->generateUrl('pln_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pln entity.
     *
     */
    public function newAction()
    {
        $securityContext = $this->get('security.context');
        $objectId = new ObjectIdentity('class', 'LOM\\PlnBundle\\Entity\\Pln');
        if(false === $securityContext->isGranted('CREATE', $objectId)) {
            throw new AccessDeniedException("You do not have permission to create Plns.");
        }
        
        $entity = new Pln();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOMPlnBundle:Pln:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Pln entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMPlnBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOMPlnBundle:Pln:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Pln entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMPlnBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOMPlnBundle:Pln:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Pln entity.
    *
    * @param Pln $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pln $entity)
    {
        $form = $this->createForm(new PlnType(), $entity, array(
            'action' => $this->generateUrl('pln_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pln entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMPlnBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pln_edit', array('id' => $id)));
        }

        return $this->render('LOMPlnBundle:Pln:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Pln entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOMPlnBundle:Pln')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pln entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pln'));
    }

    /**
     * Creates a form to delete a Pln entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pln_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    private function createAddBoxForm(Pln $pln, Box $box) {
        $form = $this->createForm(new AddBoxType(), $box, array(
            'action' => $this->generateUrl('pln_addbox', array(
                'id' => $pln->getId(),
            )),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Add box'));
        return $form;
    }
    
    public function addBoxAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOMPlnBundle:Pln')->find($id);
        if( ! $pln) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
        }
        
        $box = new Box();
        $box->setPln($pln);
        
        $form = $this->createAddBoxForm($pln, $box);
        $form->handleRequest($request);
        
        if($form->isValid()) {
            $em->persist($box);
            $em->flush();
            return $this->redirect($this->generateUrl('pln_show', array('id' => $id)));
        }
        
        return $this->render('LOMPlnBundle:Pln:addbox.html.twig', array(
            'pln' => $pln,
            'box' => $box,
            'form' => $form->createView(),
        ));
    }
}
