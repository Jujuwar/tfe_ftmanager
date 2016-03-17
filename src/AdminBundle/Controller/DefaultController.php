<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    public function newsAction()
    {
        $rep = $this->getDoctrine()->getRepository('MainBundle:News');

        $array_news = $rep->findAll();

        return $this->render('AdminBundle:News:index.html.twig', array( 'array_news' => $array_news ));
    }
}
