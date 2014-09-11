<?php

namespace LOM\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Default controller, just shows a static page for logged in users.
 */
class DefaultController extends Controller
{
    /**
     * Show the static home page.
     *
     * @return Response response object
     */
    public function indexAction()
    {
        return $this->render('LOMUserBundle:Default:index.html.twig');
    }

}
