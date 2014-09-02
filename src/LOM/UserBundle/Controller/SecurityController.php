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
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use LOM\UserBundle\Form\UserResetPasswordType;

class SecurityController extends Controller {

    /**
     * Show the login form. Actual authentication is handled internally by
     * symfony.
     *
     * @param Request $request
     * @return type rendered page
     */
    public function loginAction(Request $request) {
        $session = $request->getSession();
        $error = "";

        // check for login errors
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render(
                        "LOMUserBundle:Security:login.html.twig", array(
                    'last_username' => $lastUsername,
                    'error' => $error,
        ));
    }

    /**
     * show the lost password form
     */
    public function lostPasswordAction(Request $request) {
        $session = $request->getSession();
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render(
                        "LOMUserBundle:Security:lost_password.html.twig", array(
                    'last_username' => $lastUsername,
        ));
    }

    /**
     * Accept the form post, mangle the user in the db, send the email.
     */
    public function sendPasswordAction(Request $request) {
        $username = $request->request->get('username');

        $em = $this->getDoctrine()->getManager();

        try {
            $entity = $em->getRepository('LOMUserBundle:User')->loadUserByUsername($username);

            $resetCode = md5(time() . rand() . "some salty string.");
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $resetHash = $encoder->encodePassword($resetCode, $entity->getSalt());

            $entity->setResetCode($resetHash);
            $entity->setResetExpires((new \DateTime())->add(new \DateInterval('P1D')));
            $em->flush();

            $message = \Swift_Message::newInstance()
                    ->setSubject("LOCKSS-O-MATIC Password Reset")
                    ->setFrom("mjoyce@sfu.ca")
                    ->setTo($entity->getUsername())
                    ->setBody(
                    $this->renderView(
                            'LOMUserBundle:Security:password_email.txt.twig', array(
                        'user' => $entity,
                        'reset_code' => $resetCode
            )));
            $this->get('mailer')->send($message);
        } catch (Exception $ex) {
            // do some loging here, but don't tell the user - that's a security error.
        }



        // mangle the user here.
        // send the token.

        return $this->render(
                        "LOMUserBundle:Security:password_sent.html.twig", array(
                    'username' => $username
        ));
    }

    private function createPasswordResetForm(Request $request) {
        $username = $request->query->get('username');
        $resetcode = $request->query->get('resetcode');

        $logger = $this->get('logger');
        $logger->debug('Request parameters: ' . $username . ' ' . $resetcode);

        $form = $this->createForm(new UserResetPasswordType($username, $resetcode), null, array(
            'action' => $this->generateUrl('password_changed'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Reset password'));
        return $form;
    }

    /**
     * Show the reset password form
     */
    public function confirmPasswordAction(Request $request) {
        $form = $this->createPasswordResetForm($request);
        return $this->render('LOMUserBundle:Security:password_reset.html.twig', array(
                    'reset_form' => $form->createView(),
        ));
    }

    /**
     * Do the password reset.
     */
    public function newPasswordAction(Request $request) {
        
    }

}
