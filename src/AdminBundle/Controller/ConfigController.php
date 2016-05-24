<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConfigController extends Controller
{
    public function indexAction()
    {
        return $this->render( 'AdminBundle:Config:index.html.twig' );
    }
}
