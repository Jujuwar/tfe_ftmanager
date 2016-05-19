<?php

namespace TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use TeamBundle\Entity\Player;

class PlayerController extends Controller
{
    public function ajaxAddAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            // TODO : Meilleure validation des données

            $em = $this->getDoctrine()->getManager();

            $team = $em->getRepository( 'TeamBundle:Team' )->findOneById( $request->get( 'team' ) );
            $player = new Player();

            $player->setTeam( $team );
            $player->setNumber( $request->get( 'number' ) );
            $player->setName( $request->get( 'name' ) );
            $player->setSurname( $request->get( 'surname' ) );
            $player->setBirthday( \DateTime::createFromFormat( 'd/m/Y', $request->get( 'birthday' ) ) );
            
            $em->persist($player);
            $em->flush();

            $response = new Response( json_encode( array( 'status' => 'ok', 'return' => $this->render( 'TeamBundle:Default:playerRow.html.twig', array( 'player' => $player ) )->getContent() ) ) ) ;
            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json');

        return $response;
    }

    public function ajaxDeleteAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $user= $this->get( 'security.token_storage' )->getToken()->getUser();

                $em = $this->getDoctrine()->getManager();

                $player = $em->getRepository( 'TeamBundle:Player' )->findOneById( $request->get( 'id' ) );

                if( $player->getTeam()->getManager() == $user ) {

                    if( $player ) {
                        $player->setTeam( null );
                        $em->flush();

                        $response = new Response( json_encode( array( 'status' => 'ok' ) ) );
                    } else
                        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Le joueur n\'existe pas' ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Vous n\'avez pas la permission de supprimer ce joueur' ) ) );

            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => $e->getMessage() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }

    public function ajaxGetAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $player = $em->getRepository( 'TeamBundle:Player' )->findOneById( $request->get( 'id' ) );

                $normalizer  = new ObjectNormalizer();;
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $serializer = new Serializer( array( $normalizer ) );
                $player = $serializer->normalize( $player );

                $response = new Response( json_encode( array( 'status' => 'ok', 'player' => $player ) ) );
            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => $e->getMessage() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }
}
