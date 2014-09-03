<?php

namespace LOM\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form types for data entry.
 */
class AdminRoleType extends AbstractType {

    /**
     * Build a form.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name')
                ->add('role')
                ->add('description')
                ->add('parent')
                ->add('users');
    }

    /**
     * Set the default options. The form expects a Role entity.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'LOM\UserBundle\Entity\Role'
        ));
    }

    /**
     * Name the form.
     * 
     * @return string
     */
    public function getName() {
        return 'lom_userbundle_role';
    }

}
