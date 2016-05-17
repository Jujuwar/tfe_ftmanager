<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamController extends Controller
{
    public function indexAction()
    {
        $rep = $this->getDoctrine()->getRepository( 'TeamBundle:Team' );

        $array_teams = $rep->findAll();

        return $this->render( 'AdminBundle:Team:index.html.twig', array( 'array_teams' => $array_teams ) );
    }

    public function ajaxDeleteAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $team = $em->getRepository( 'TeamBundle:Team' )->findOneById( $request->get( 'id' ) );

                if( $team ) {
                    $em->remove( $team );
                    $em->flush();

                    $response = new Response( json_encode( array( 'status' => 'ok' ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'La team n\'existe pas', 'debug' => 'La team n\'existe pas' ) ) );
            }
            catch( \Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Impossible de supprimer une équipe ayant déjà participé à un tournoi ou possédant des joueurs', 'debug' => $e->getMessage() ) ) );
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

    public function ajaxValidateAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $team = $em->getRepository( 'TeamBundle:Team' )->findOneById( $request->get( 'id' ) );

                if( $team ) {
                    $team->setValid( !$team->getValid() );
                    $em->flush();

                    $response = new Response( json_encode( array( 'status' => 'ok', 'return' => $this->render( 'AdminBundle:Team:teamRow.html.twig', array( 'team' => $team ) )->getContent() ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'La team n\'existe pas', 'debug' => 'La team n\'existe pas' ) ) );
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
}
