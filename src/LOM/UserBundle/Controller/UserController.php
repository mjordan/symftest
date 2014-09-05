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
use LOM\UserBundle\Form\UserType;
use LOM\UserBundle\Form\UserChangePasswordType;
use LOM\UserBundle\Form\Model\UserChangePassword;

/**
 * User controller. Lets users edit their own information.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $entity = $this->get('security.context')->getToken()->getUser();

        return $this->render('LOMUserBundle:User:index.html.twig', array(
                    'entity' => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @return Response A Response instance
     */
    public function editAction()
    {
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
    private function createEditForm(User $entity)
    {
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
     * @param Request $request the request instance
     *
     * @return Response A Response instance
     */
    public function updateAction(Request $request)
    {
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

    /**
     * Manage a password change for a user.
     *
     * @param Request $request the request instance
     *
     * @return Response A Response instance
     */
    public function passwordAction(Request $request)
    {
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

            $this->get('session')->getFlashBag()->add(
                    'notice', 'Your password has been changed.'
            );

            return $this->redirect($this->generateUrl('user'));
        }

        return $this->render('LOMUserBundle:User:password.html.twig', array(
                    'password_form' => $form->createView(),
        ));
    }

}
