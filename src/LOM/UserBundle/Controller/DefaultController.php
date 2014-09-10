<?php

namespace LOM\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LOMUserBundle:Default:index.html.twig', array(
                // ...
            ));    }

}
