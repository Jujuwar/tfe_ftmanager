<?php

namespace TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TeamBundle\Entity\Player;
use TeamBundle\Form\TeamType;
use TeamBundle\Entity\Team;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $team = $em->getRepository( 'TeamBundle:Team' )->findOneByManager( $user->getId() );

        if($team) {
            $players = $em->getRepository('TeamBundle:Player')->findBy(array('team' => $team->getId()));

            return $this->render('TeamBundle:Default:index.html.twig', array('team' => $team, 'players' => $players));
        } else // Si aucune équipe inscrite, on redirige sur la page pour en inscrire une
            return $this->redirectToRoute( 'team_registration' );

    }

    public function registrationAction( Request $request ) {
        $user = $this->getUser();

        if( !empty( $user ) && empty( $this->getDoctrine()->getRepository( 'UserBundle:User' )->getTeam( $user->getId() ) ) ) {
            $em = $this->getDoctrine()->getManager();

            $team = new Team();

            $team->setManager($user);
            $team->setValid(false);

            $form = $this->createForm(TeamType::class, $team);

            $form->handleRequest($request);

            if ($form->isValid()) {
                $team->setRegistrationDate(new \DateTime());

                $em->persist($team);
                $em->flush();

                $this->redirectToRoute('team_homepage');
            }

            return $this->render('TeamBundle:Default:registration.html.twig', array('form' => $form->createView()));
        } else
            return $this->redirectToRoute( 'team_homepage' );
    }
    
    public function ajaxPlayerAddAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            // TODO : Meilleure validation des données

            $em = $this->getDoctrine()->getManager();

            $team = $em->getRepository( 'TeamBundle:Team' )->findOneById( $request->get( 'team' ) );
            $player = new Player();

            $player->setTeam( $team );
            $player->setNumber( $request->get( 'number' ) );
            $player->setName( $request->get( 'name' ) );
            $player->setSurname( $request->get( 'surname' ) );
            // TODO : Datetime
            $player->setBirthday( new \DateTime() );

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

    public function ajaxPlayerDeleteAction( Request $request ) {
        // TODO : On supprime le joueur ou on lui enlève juste son équipe ?
        if( $request->isXmlHttpRequest() ) {
            try {
                $user= $this->get( 'security.token_storage' )->getToken()->getUser();

                $em = $this->getDoctrine()->getManager();

                $player = $em->getRepository( 'TeamBundle:Player' )->findOneById( $request->get( 'id' ) );

                if( $player->getTeam()->getManager() == $user ) {

                    if ($player) {
                        $em->remove($player);
                        $em->flush();

                        $response = new Response(json_encode(array('status' => 'ok')));
                    } else
                        $response = new Response(json_encode(array('status' => 'ko', 'debug' => 'Le joueur n\'existe pas')));
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
}
