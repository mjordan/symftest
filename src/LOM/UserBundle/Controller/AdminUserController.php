<?php

/*
 * Copyright (C) 2014 mjoyce
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace LOM\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LOM\UserBundle\Entity\User;
use LOM\UserBundle\Form\AdminUserType;
use LOM\UserBundle\Form\Model\AdminChangePassword;
use LOM\UserBundle\Form\AdminChangePasswordType;

/**
 * User controller.
 *
 */
class AdminUserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @return Response A Response instance
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOMUserBundle:User')->findAll();

        return $this->render('LOMUserBundle:AdminUser:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Creates a new User entity.
     * @param Request $request the request being processed
     *
     * @return Response A Response instance
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = md5(time() . rand() . "not a valid password.");
            $entity->setPassword($password);
            $entity->generateSalt();

            $resetCode = sha1(time() . rand() . "some salty string.");
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $resetHash = $encoder->encodePassword($resetCode, $entity->getSalt());

            $entity->setResetCode($resetHash);
            $dt = new \DateTime();
            $dt->add(new \DateInterval('P1D'));
            $entity->setResetExpires($dt);

            $em->persist($entity);
            $em->flush();

            $message = \Swift_Message::newInstance()
                    ->setSubject("Welcome to LOCKS-O-MATTIC.")
                    ->setFrom("mjoyce@sfu.ca")
                    ->setTo($entity->getUsername())
                    ->setBody(
                    $this->renderView(
                            'LOMUserBundle:AdminUser:welcome_newuser.txt.twig', array(
                        'user' => $entity,
                        'reset_code' => $resetCode
            )));
            $this->get('mailer')->send($message);

            $this->get('session')->getFlashBag()->add(
                    'notice', 'The user account has been created.'
            );

            return $this->redirect($this->generateUrl('admin_user_show', array('id' => $entity->getId())));
        }

        return $this->render('LOMUserBundle:AdminUser:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new AdminUserType(), $entity, array(
            'action' => $this->generateUrl('admin_user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @return Response A Response instance
     */
    public function newAction()
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);

        return $this->render('LOMUserBundle:AdminUser:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     * @param int $id The id of the entity to display.
     *
     * @return Response A Response instance
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $roles = $em->getRepository("LOMUserBundle:Role")->findAll();
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOMUserBundle:AdminUser:show.html.twig', array(
                    'entity' => $entity,
                    'allRoles' => $roles,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     * @param int $id the ID of the entity to edit.
     *
     * @return Response A Response instance
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOMUserBundle:AdminUser:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new AdminUserType(), $entity, array(
            'action' => $this->generateUrl('admin_user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User entity.
     * @param Request $request the request being processed
     * @param int     $id      the id of the user to edit
     *
     * @return Response              A Response instance
     * @throws NotFoundHttpException
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                    'notice', 'The user information has been updated.'
            );

            return $this->redirect($this->generateUrl('admin_user_show', array(
                'id' => $id
            )));
        }

        return $this->render('LOMUserBundle:AdminUser:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Change a user's password
     *
     * @param Request $request the request being processed
     * @param int     $id      the id of the user to edit
     *
     * @return Response              A Response instance
     * @throws NotFoundHttpException
     */
    public function passwordAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOMUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $changePasswordModel = new AdminChangePassword();
        $form = $this->createForm(new AdminChangePasswordType(), $changePasswordModel, array(
            'method' => 'POST'
        ));
        $form->add('submit', 'submit', array(
            'label' => 'Change password',
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $newPassword = $form->get('newPassword')->getData();

            $entity->generateSalt();
            $newHash = $encoder->encodePassword($newPassword, $entity->getsalt());
            $entity->setPassword($newHash);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                    'notice', 'The password has been changed.'
            );

            return $this->redirect($this->generateUrl('admin_user_show', array(
                                'id' => $entity->getId()
            )));
        }

        return $this->render('LOMUserBundle:AdminUser:password.html.twig', array(
                    'entity' => $entity,
                    'password_form' => $form->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     * @param Request $request the request being processed
     * @param int     $id      the id of the entity to delete
     *
     * @return Response              A Response instance
     * @throws NotFoundHttpException
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOMUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('admin_user_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm();
    }

}
