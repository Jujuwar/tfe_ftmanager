<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MatchController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $matchs = $em->getRepository( 'MatchBundle:Matchs' )->findAll( '', 'date DESC');

        return $this->render( 'AdminBundle:Match:index.html.twig', array( 'matchs' => $matchs ) );
    }
}
