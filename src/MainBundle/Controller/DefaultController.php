<?php

namespace MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MainBundle:Default:index.html.twig');
    }

    public function ladderAction() {
        $em = $this->getDoctrine()->getManager();

        $teams = $em->getRepository( 'TeamBundle:Team' )->findBy( array( 'valid' => true ) );
        
        $classement = array();
        
        foreach( $teams as $k => $v ) {
            $matchs = $em->getRepository( 'MatchBundle:Matchs' )->findByTeam( $v->getId() );
            $classement['name'][$k] = $v->getName();
            $classement['points'][$k] = 0;
            $classement['bp'][$k] = 0;
            $classement['bc'][$k] = 0;
            
            foreach( $matchs as $k2 => $v2) {
                if( count( $v2->getPrestations() ) > 0 ) {
                    $res = $v2->getPoints($v);

                    $classement['points'][$k] += $res['points'];
                    $classement['bp'][$k] += $res['bp'];
                    $classement['bc'][$k] += $res['bc'];
                    $classement['diff'][$k] = $classement['bp'][$k] - $classement['bc'][$k];
                }
            }
        }

        array_multisort( $classement['points'], SORT_DESC, $classement['diff'], SORT_DESC, $classement['bp'] );

        return $this->render( 'MainBundle:Default:classement.html.twig', array( 'teams' => $classement ) );
    }
}
