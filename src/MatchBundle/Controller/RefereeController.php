<?php

namespace MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RefereeController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $matchs = $em->getRepository( 'MatchBundle:Matchs' )->findBy( array( 'referee' => $this->getUser() ) );

        return $this->render( 'MatchBundle:Referee:index.html.twig', array( 'matchs' => $matchs ) );
    }
}
