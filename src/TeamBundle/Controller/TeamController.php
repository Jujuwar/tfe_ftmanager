<?php

namespace TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TeamBundle\Form\Type\TeamType;
use TeamBundle\Entity\Team;

class TeamController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $team = $em->getRepository( 'TeamBundle:Team' )->findOneBy( array( 'manager' => $user->getId() ) );

        if( $team )
            return $this->render( 'TeamBundle:Default:index.html.twig', array( 'team' => $team ) );
        else // Si aucune équipe inscrite, on redirige sur la page pour en inscrire une
            return $this->redirectToRoute( 'team_registration' );

    }

    public function registrationAction( Request $request ) {
        $user = $this->getUser();

        if( !empty( $user ) && empty( $user->getTeam() ) ) {
            $em = $this->getDoctrine()->getManager();

            $team = new Team();

            $team->setManager( $user );
            $team->setValid( false );

            $form = $this->createForm( TeamType::class, $team );

            $form->handleRequest( $request );

            if ($form->isValid()) {
                $team->setRegistrationDate( new \DateTime() );

                $em->persist( $team );
                $em->flush();

                return $this->redirectToRoute( 'team_homepage' );
            }

            return $this->render( 'TeamBundle:Default:registration.html.twig', array( 'form' => $form->createView() ) );
        } else
            return $this->redirectToRoute( 'team_homepage' );
    }

    public function ajaxSetRegisteredAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $user= $this->get( 'security.token_storage' )->getToken()->getUser();

                $em = $this->getDoctrine()->getManager();

                $team = $em->getRepository( 'TeamBundle:Team' )->findOneBy( array( 'id' => $request->get( 'id' ) ) );

                if( $team && $team->getManager() == $user ) {
                    $team->setRegistered( true );
                    $em->flush();

                    $response = new Response( json_encode( array( 'status' => 'ok' ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Vous n\'avez pas la permission de modifier cette équipe', 'debug' => 'Vous n\'avez pas la permission de modifier cette équipe' ) ) );
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

    public function frontIndexAction( $id, $slugTeam ) {
        if( empty( $id ) || empty( $slugTeam ) ) {
            $em = $this->getDoctrine()->getManager();

            $teams = $em->getRepository( 'TeamBundle:Team' )->findBy( array( 'registered' => true ) );

            return $this->render( 'TeamBundle:Front:index.html.twig', array( 'teams' => $teams ) );
        } else
            return $this->frontViewTeamAction( $id, $slugTeam );
    }

    public function frontViewTeamAction( $id, $slugTeam ) {
        $em = $this->getDoctrine()->getManager();

        $team = $em->getRepository( 'TeamBundle:Team' )->findOneBy( array( 'id' => $id ) );

        if( $this->get( 'cocur_slugify' )->slugify( $team->getName() ) == $slugTeam )
            return $this->render( 'TeamBundle:Front:team.html.twig', array( 'team' => $team ) );
        else
            return $this->redirectToRoute( 'team_front_homepage', array( 'id' => $team->getId(), 'slugTeam' => $this->get( 'cocur_slugify' )->slugify( $team->getName() ) ) );
    }

    public function frontViewTeamMatchsAction( $id ) {
        $em = $this->getDoctrine()->getManager();

        $matchs = $em->getRepository( 'MatchBundle:Matchs' )->findByTeam( $id );
        
        return $this->render( 'TeamBundle:Front:match.html.twig', array( 'matchs' => $matchs ) );
    }
}
