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
            try {
                $em = $this->getDoctrine()->getManager();

                $team = $em->getRepository('TeamBundle:Team')->findOneBy(array('id' => $request->get('team')));
                $player = new Player();

                $player->setTeam($team);
                $player->setNumber($request->get('number'));
                $player->setName($request->get('name'));
                $player->setSurname($request->get('surname'));
                $player->setBirthday(\DateTime::createFromFormat('d/m/Y', $request->get('birthday')));

                $errors = $this->get( 'validator' )->validate( $player );
                
                if( count( $errors ) == 0 ) {
                    $em->persist($player);
                    $em->flush();

                    $response = new Response(json_encode(array('status' => 'ok', 'return' => $this->render('TeamBundle:Default:playerRow.html.twig', array('player' => $player))->getContent())));
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Impossible d\'ajouter le joueur', 'errors' => $this->render( 'TeamBundle:Default:validation.html.twig', array( 'errors' => $errors ) )->getContent(), 'debug' => $errors ) ) );
            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Une erreur inconnue s\'est produite', 'debug' => $e->getMessage() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Accès non autorisé', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json');

        return $response;
    }

    public function ajaxDeleteAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $user= $this->get( 'security.token_storage' )->getToken()->getUser();

                $em = $this->getDoctrine()->getManager();

                $player = $em->getRepository( 'TeamBundle:Player' )->findOneBy( array( 'id' => $request->get( 'id' ) ) );

                if( $player && $player->getTeam()->getManager() == $user ) {
                    $player->setTeam( null );
                    $em->flush();

                    $response = new Response( json_encode( array( 'status' => 'ok' ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Vous n\'avez pas la permission de supprimer ce joueur', 'debug' => 'Vous n\'avez pas la permission de supprimer ce joueur' ) ) );

            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Une erreur inconnue s\'est produite', 'debug' => $e->getMessage() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Accès non autorisé', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }

    public function ajaxGetAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $player = $em->getRepository( 'TeamBundle:Player' )->findOneBy( array( 'id' => $request->get( 'id' ) ) );

                $normalizer  = new ObjectNormalizer();;
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $serializer = new Serializer( array( $normalizer ) );
                $player = $serializer->normalize( $player );

                $response = new Response( json_encode( array( 'status' => 'ok', 'player' => $player ) ) );
            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Une erreur inconnue s\'est produite', 'debug' => $e->getMessage() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Accès non autorisé', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }

    public function ajaxEditAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $player = $em->getRepository( 'TeamBundle:Player' )->findOneBy( array( 'id' => $request->get( 'id' ) ) );

                $player->setName( $request->get( 'name' ) );
                $player->setSurname( $request->get( 'surname' ) );
                $player->setNumber( $request->get( 'number' ) );
                $player->setBirthday( \DateTime::createFromFormat( 'd/m/Y', $request->get( 'birthday' ) ) );

                $errors = $this->get( 'validator' )->validate( $player );

                if( count( $errors ) == 0 ) {
                    $em->flush();

                    $response = new Response(json_encode(array('status' => 'ok', 'return' => $this->render( 'TeamBundle:Default:playerRow.html.twig', array( 'player' => $player ) )->getContent() ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Impossible de modifier le joueur', 'errors' => $this->render( 'TeamBundle:Default:validation.html.twig', array( 'errors' => $errors ) )->getContent(), 'debug' => $errors ) ) );
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
