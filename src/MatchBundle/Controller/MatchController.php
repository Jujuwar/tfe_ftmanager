<?php

namespace MatchBundle\Controller;

use MatchBundle\Entity\Prestation;
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

    public function ajaxUpdatePrestationsAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $prestations = json_decode( $request->get( 'prestations' ) );
                $match = $em->getRepository( 'MatchBundle:Matchs' )->findOneBy( array( 'id' => $request->get( 'id' ) ) );

                foreach( $prestations as $k => $v ) {
                    $prestation = $em->getRepository( 'MatchBundle:Prestation' )->findOneBy( array( 'player' => $v->id, 'matchs' => $request->get( 'id' ) ) );

                    if( is_numeric( $v->enterTime ) && is_numeric( $v->leaveTime ) && is_numeric( $v->yellowCards ) && is_numeric( $v->redCards ) && is_numeric( $v->buts ) ) {
                        if ( !$prestation ) {
                           $prestation = new Prestation();
                        }

                        $prestation->setMatchs( $match );
                        $prestation->setPlayer( $em->getRepository( 'TeamBundle:Player' )->findOneBy( array( 'id' => $v->id ) ) );
                        $prestation->setTeam( $prestation->getPlayer()->getTeam() );
                        $prestation->setEnterTime( $v->enterTime );
                        $prestation->setLeaveTime( $v->leaveTime );
                        $prestation->setYellowCards( $v->yellowCards );
                        $prestation->setRedCards( $v->redCards );
                        $prestation->setButs( $v->buts );

                        $em->persist( $prestation );
                    } else {
                        if( $prestation )
                            $em->remove( $prestation );
                    }
                }
                $em->flush();

                $response = new Response( json_encode( array( 'status' => 'ok', 'return' => $this->render( 'MatchBundle:Referee:matchRow.html.twig', array( 'match' => $match ) )->getContent() ) ) );
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
