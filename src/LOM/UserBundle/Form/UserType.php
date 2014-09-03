<?php

namespace LOM\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form types for a user editing their own information.
 *
 * Users cannot change their roles or password with this form.
 */
class UserType extends AbstractType {

    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username')
                ->add('fullname')
                ->add('institution');
    }

    /**
     * The form expects a User entity.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'LOM\UserBundle\Entity\User'
        ));
    }

    /**
     * Name the form.
     * 
     * @return string
     */
    public function getName() {
        return 'lom_userbundle_user';
    }

}
