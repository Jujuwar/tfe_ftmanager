<?php

namespace MatchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class MatchController extends Controller
{
    public function resultsAction( $id ) {
        $em = $this->getDoctrine()->getManager();

        $match = $em->getRepository( 'MatchBundle:Matchs' )->findOneBy( array( 'id' => $id ) );

        if( $match && count( $match->getPrestations() ) > 0 )
            return $this->render( 'MatchBundle:Match:results.html.twig', array( 'match' => $match ) );
        else {
            $this->addFlash( 'danger', 'Le match n\'a pas encore été joué ou n\'existe pas' );
            
            return $this->redirectToRoute( 'match_calendar' );
        }
    }

    public function calendarAction()
    {
        return $this->render( 'MatchBundle:Match:calendar.html.twig' );
    }
    
    public function ajaxGetAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $match = $em->getRepository( 'MatchBundle:Matchs' )->findOneBy( array( 'id' => $request->get( 'id' ) ) );

                $normalizer  = new ObjectNormalizer();;
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $serializer = new Serializer( array( $normalizer ) );
                $match = $serializer->normalize( $match );

                $response = new Response( json_encode( array( 'status' => 'ok', 'value' => $match ) ) );
            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Une erreur inconnue s\'est produite', 'debug' => $e->getMessage() ) ) );
            }
            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Accès refusé', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }
}
