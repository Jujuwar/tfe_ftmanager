<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\EditUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\User;

class UserController extends Controller
{
    public function indexAction() {
        $rep = $this->getDoctrine()->getRepository( 'UserBundle:User' );

        $array_users = $rep->findAll();

        return $this->render( 'AdminBundle:User:index.html.twig', array( 'array_users' => $array_users ) );
    }

    public function editAction( $id ) {
        $userManager = $this->get( 'fos_user.user_manager' );

        $user = $userManager->findUserBy( array( 'id' => $id ) );

        $form = $this->createForm( EditUserType::class, $user );

        if( $form->isSubmitted() ) {
            $userManager->updateUser( $user );
        }

        return $this->render( 'AdminBundle:User:edit.html.twig', array( 'user' => $user, 'form' => $form->createView() ) );
    }

    public function ajaxPromoteAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository( 'UserBundle:User' )->findOneById( $request->get( 'id' ) );

            if( $user->hasRole( 'ROLE_SUPER_ADMIN' ) && !$this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Vous n\'avez pas la permission pour cela' ) ) );
                $response->headers->set( 'Content-Type', 'application/json' ) ;

                return $response;
            } else if( $user->hasRole( 'ROLE_ADMIN' ) && !$this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Vous n\'avez pas la permission pour cela' ) ) );
                $response->headers->set( 'Content-Type', 'application/json' ) ;

                return $response;
            } else {
                foreach( $user->getRoles() as $k => $v )
                    $user->removeRole( $v );

                switch( $request->get( 'role' ) ) {
                    case 'sup_admin':
                        $user->addRole( 'ROLE_SUPER_ADMIN' );
                        break;

                    case 'admin':
                        $user->addRole( 'ROLE_ADMIN' );
                        break;

                    case 'arbitre':
                        $user->addRole( 'ROLE_ARBITRE' );
                        break;

                    case 'member':
                        $user->addRole( 'ROLE_USER' );
                        break;

                }

                $em->persist( $user );
                $em->flush();

                $response = new Response( json_encode( array( 'status' => 'ok', 'return' => $this->render( 'AdminBundle:User:userRow.html.twig', array( 'user' => $user ) )->getContent() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json') ;

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json');

        return $response;
    }

    public function ajaxDeleteAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            if( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
                try {
                    $em = $this->getDoctrine()->getManager();

                    $user = $em->getRepository( 'UserBundle:User' )->findOneById( $request->get( 'id' ) );

                    if( $user ) {
                        $em->remove($user);
                        $em->flush();

                        $response = new Response( json_encode( array( 'status' => 'ok' ) ) );
                    } else
                        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'L\'utilisateur n\'existe pas' ) ) );

                }
                catch( \Exception $e ) {
                    $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => $e->getMessage() ) ) );
                }
            } else {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Vous n\'avez pas la permission de supprimer des utilisateurs' ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }
}
