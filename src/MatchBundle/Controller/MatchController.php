<?php

namespace MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MatchController extends Controller
{
    public function resultsAction( $id ) {
        $em = $this->getDoctrine()->getManager();

        $match = $em->getRepository( 'MatchBundle:Matchs' )->findOneBy( array( 'id' => $id ) );

        if( $match && count( $match->getPrestations() ) > 0 )
            return $this->render( 'MatchBundle:Match:results.html.twig', array( 'match' => $match ) );
        else {
            $this->addFlash( 'danger', 'Le match n\'a pas encore été joué ou n\'existe pas' );
            
            return $this->redirectToRoute('match_calendar');
        }
    }

    public function calendarAction()
    {
        return $this->render('MatchBundle:Match:calendar.html.twig');
    }
}
