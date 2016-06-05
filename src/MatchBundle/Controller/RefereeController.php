<?php

namespace MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RefereeController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        if( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN' ) )
            $matchs = $em->getRepository( 'MatchBundle:Matchs' )->findAll();
        else
            $matchs = $em->getRepository( 'MatchBundle:Matchs' )->findBy( array( 'referee' => $this->getUser() ) );

        return $this->render( 'MatchBundle:Referee:index.html.twig', array( 'matchs' => $matchs ) );
    }
}
