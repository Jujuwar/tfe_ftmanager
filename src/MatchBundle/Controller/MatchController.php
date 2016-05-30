<?php

namespace MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MatchController extends Controller
{
    public function resultsAction( $id ) {
        $em = $this->getDoctrine()->getManager();

        $match = $em->getRepository( 'MatchBundle:Matchs' )->findOneBy( array( 'id' => $id ) );

        return $this->render( 'MatchBundle:Match:results.html.twig', array( 'match' => $match ) );
    }

    public function calendarAction()
    {
        return $this->render('MatchBundle:Match:calendar.html.twig');
    }
}
