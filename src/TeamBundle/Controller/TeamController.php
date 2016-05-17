<?php

namespace TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TeamBundle\Entity\Player;
use TeamBundle\Form\Type\TeamType;
use TeamBundle\Entity\Team;

class TeamController extends Controller
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

        if( !empty( $user ) && empty( $user->getTeam() ) ) {
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

                return $this->redirectToRoute('team_homepage');
            }

            return $this->render('TeamBundle:Default:registration.html.twig', array('form' => $form->createView()));
        } else
            return $this->redirectToRoute( 'team_homepage' );
    }
}
