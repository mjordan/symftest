<?php

namespace LOM\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LOM\UserBundle\Entity\User;
use LOM\UserBundle\Form\UserType;
use LOM\UserBundle\Form\UserChangePasswordType;

/**
 * User controller.
 *
 */
class UserController extends Controller {

    /**
     * Lists all User entities.
     *
     */
    public function indexAction() {
        $entity = $this->get('security.context')->getToken()->getUser();
        return $this->render('LOMUserBundle:User:index.html.twig', array(
                    'entity' => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction() {
        $entity = $this->get('security.context')->getToken()->getUser();
        $editForm = $this->createEditForm($entity);
        return $this->render('LOMUserBundle:User:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity) {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_update'),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->get('security.context')->getToken()->getUser();
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('user_edit'));
        }

        return $this->render('LOMUserBundle:User:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
        ));
    }

    public function createPasswordForm(User $entity) {
        $form = $this->createForm(new UserChangePasswordType(), $entity, array(
            'action' => $this->generateUrl('user_password_update'),
            'method' => 'PUT'
        ));
        $form->add('submit', 'submit', array('label' => 'Update password'));
        return $form;
    }

    public function passwordAction() {
        $entity = $this->get('security.context')->getToken()->getUser();
        $passwordForm = $this->createPasswordForm($entity);
        return $this->render('LOMUserBundle:User:password.html.twig', array(
                    'entity' => $entity,
                    'password_form' => $passwordForm->createView(),
        ));
    }

    public function passwordUpdateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->get('security.context')->getToken()->getUser();
        $passwordForm = $this->createPasswordForm($entity);
        if ($passwordForm->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($password);
            $em->flush();

            return $this->redirect($this->generateUrl('user'));
        }
        return $this->render('LOMUserBundle:User:password.html.twig', array(
                    'entity' => $entity,
                    'password_form' => $passwordForm->createView(),
        ));
    }

}
