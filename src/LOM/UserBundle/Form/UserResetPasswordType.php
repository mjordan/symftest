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

namespace LOM\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form types for a user resetting their password.
 */
class UserResetPasswordType extends AbstractType
{

    private $username;

    private $resetcode;

    /**
     * Build a user password reset form
     * 
     * @param string $username
     * @param string $resetcode
     */
    public function __construct($username, $resetcode)
    {
        $this->username = $username;
        $this->resetcode = $resetcode;
    }

    /**
     * Build a form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array(
                    'label' => 'Username',
                    'data' => $this->username,
                ))
                ->add('resetcode', 'text', array(
                    'label' => 'Reset code',
                    'data' => $this->resetcode,
                ))
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'required' => true,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
        ));
    }

    /**
     * Name the form.
     *
     * @return string
     */
    public function getName()
    {
        return 'user_reset_password';
    }

}
