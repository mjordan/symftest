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

namespace LOM\PlnBundle\Twig;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;

/**
 * Simple Twig extension to to get the ACL security object identity for a
 * class name. Register it as a service, then use it in a twig template like
 * this:
 * 
 * {% if is_granted('CREATE', classId('LOM\\PlnBundle\\Entity\\Pln')) %} ...  {% endif %}
 */
class ClassIdExtension extends \Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'classId' => new \Twig_SimpleFunction('classId', array($this, 'classId'))
        );
    }

    /**
     * The extension function callable from Twig.
     * 
     * @param string $class
     * 
     * @return ObjectIdentity
     */
    public function classId($class)
    {
        return new ObjectIdentity('class', $class);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'lom_classidextension';
    }

}
