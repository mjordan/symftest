<?php

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
class AdminUserController extends Controller {

    /**
     * Lists all User entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOMUserBundle:User')->findAll();

        return $this->render('LOMUserBundle:AdminUser:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = md5(time() . rand() . "not a valid password.");
            $entity->setPassword($password);

            $resetCode = sha1(time() . rand() . "some salty string.");
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $resetHash = $encoder->encodePassword($resetCode, $entity->getSalt());

            $entity->setResetCode($resetHash);
            $entity->setResetExpires((new \DateTime())->add(new \DateInterval('P1D')));

            $em->persist($entity);
            $em->flush();

            $message = \Swift_Message::newInstance()
                    ->setSubject("LOCKSS-O-MATIC Password Reset")
                    ->setFrom("mjoyce@sfu.ca")
                    ->setTo($entity->getUsername())
                    ->setBody(
                    $this->renderView(
                            'LOMUserBundle:AdminUser:welcome_newuser.txt.twig', array(
                        'user' => $entity,
                        'reset_code' => $resetCode
            )));
            $this->get('mailer')->send($message);

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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity) {
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
     */
    public function newAction() {
        $entity = new User();
        $form = $this->createCreateForm($entity);

        return $this->render('LOMUserBundle:AdminUser:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOMUserBundle:AdminUser:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($id) {
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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity) {
        $form = $this->createForm(new AdminUserType(), $entity, array(
            'action' => $this->generateUrl('admin_user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id) {
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
            return $this->redirect($this->generateUrl('admin_user_edit', array('id' => $id)));
        }

        return $this->render('LOMUserBundle:AdminUser:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    public function passwordAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $passwordForm = $this->createPasswordForm($entity);

        return $this->render('LOMUserBundle:AdminUser:password.html.twig', array(
                    'entity' => $entity,
                    'password_form' => $passwordForm->createView(),
        ));
    }

    private function createPasswordForm(User $entity) {
        $form = $this->createForm(new AdminChangePasswordType, $entity, array(
            'action' => $this->generateUrl('admin_user_password_update', array(
                'id' => $entity->getId()
            )),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Update password'));
        return $form;
    }

    public function updatePasswordAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOMUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $passwordForm = $this->createPasswordForm($entity);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($password);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_user_show', array('id' => $id)));
        }

        return $this->render('LOMUserBundle:AdminUser:password.html.twig', array(
                    'entity' => $entity,
                    'password_form' => $passwordForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id) {
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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('admin_user_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}