<?php

/*
 * Copyright (C) Error: on line 4, column 33 in Templates/Licenses/license-gpl20.txt
  The string doesn't match the expected date/time format. The string to parse was: "27-Aug-2014". The expected format was: "MMM d, yyyy". mjoyce
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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LOM\UserBundle\Entity\User;
use LOM\UserBundle\Form\Type\RegistrationType;
use LOM\UserBundle\Form\Model\Registration;

class AdminController extends Controller {

    public function indexAction() {
        return $this->render(
                        "LOMUserBundle:Admin:index.html.twig", array('users' => $this->getDoctrine()->getRepository('LOMUserBundle:User')->findAll())
        );
    }

    public function registerAction(Request $request) {
        $user = new User();
        $form = $this->createFormBuilder($user, array('action' => $this->generateUrl('user_register')))
                ->add('username', 'text')
                        ->add('password', 'repeated', array(
                            'first_name' => 'password',
                            'second_name' => 'confirm',
                            'type' => 'password'))
                ->add('Register', 'submit')
                ->getForm();

        if ($request->getMethod() === 'POST') {
            $form->submit($request);
            if ($form->isValid()) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);
                $user->save($this);
                return $this->redirect($this->generateUrl('user_registered'));
            }
        }

        return $this->render('LOMUserBundle:Admin:register.html.twig', array('form' => $form->createView())
        );
    }

    public function userRegisteredAction() {
        return $this->render('LOMUserBundle:Admin:user_registered.html.twig');
    }

}
