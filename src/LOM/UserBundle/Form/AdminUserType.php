<?php

namespace LOM\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form types for an admin to edit a user.
 */
class AdminUserType extends AbstractType {

    /**
     * Build a user edit form.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username')
                ->add('isActive')
                ->add('fullname')
                ->add('institution')
                ->add('roles');
    }

    /**
     * Set the default options for the form - the form expects a User entity.
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
