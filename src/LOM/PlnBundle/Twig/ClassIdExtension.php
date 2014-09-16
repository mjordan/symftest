<?php

namespace LOM\PlnBundle\Twig;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;


class ClassIdExtension extends \Twig_Extension {
    
 
    public function getFunctions()
    {
        return array(
            'classId' => new \Twig_SimpleFunction('classId', array($this, 'classId'))
        );     
    }
    
    public function classId($class) {
        return new ObjectIdentity('class', $class);
    }
    
     public function getName()
    {
        return 'lom_classidextension';
    }

}