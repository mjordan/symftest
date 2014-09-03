<?php

namespace LOM\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LOM\UserBundle\Entity\User;
use LOM\UserBundle\Form\UserType;
use LOM\UserBundle\Form\UserChangePasswordType;
use LOM\UserBundle\Form\Model\UserChangePassword;

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

    public function passwordAction(Request $request) {

        $changePasswordModel = new UserChangePassword();
        $form = $this->createForm(new UserChangePasswordType(), $changePasswordModel, array(
            'method' => 'POST'
        ));
        $form->add('submit', 'submit', array(
            'label' => 'Change password',
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity = $this->get('security.context')->getToken()->getUser();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $newPassword = $form->get('newPassword')->getData();
            $newHash = $encoder->encodePassword($newPassword, $entity->getsalt());
            $entity->setPassword($newHash);
            $em->flush();

            return $this->redirect($this->generateUrl('user'));
        }
        return $this->render('LOMUserBundle:User:password.html.twig', array(
                    'password_form' => $form->createView(),
        ));
    }

}
