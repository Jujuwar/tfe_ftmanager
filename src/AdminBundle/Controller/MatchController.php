<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MatchController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $matchs = $em->getRepository( 'MatchBundle:Matchs' )->findBy( array(), array( 'date' => 'DESC' ));

        return $this->render( 'AdminBundle:Match:index.html.twig', array( 'matchs' => $matchs ) );
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

    public function ajaxEditAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $match = $em->getRepository( 'MatchBundle:Matchs' )->findOneBy( array( 'id' => $request->get( 'id' ) ) );

                if( $match ) {
                    if( !is_null( $request->get( 'date' ) ) )
                        $match->setDate( \DateTime::createFromFormat( 'd/m/Y H:i', $request->get( 'date' ) ) );

                    if( !is_null( $request->get( 'field' ) ) )
                        $match->setField( $em->getRepository( 'MatchBundle:Field' )->findOneBy( array( 'id' => $request->get( 'field' ) ) ) );

                    if( !is_null( $request->get( 'referee' ) ) )
                        $match->setReferee( $em->getRepository( 'UserBundle:User' )->findOneBy( array( 'id' => $request->get( 'referee' ) ) ) );

                    $em->flush();

                    $response = new Response( json_encode( array( 'status' => 'ok', 'return' => $this->render( 'AdminBundle:Match:matchRow.html.twig', array( 'match' => $match ) )->getContent() ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'message' => 'Le match n\'existe pas', 'debug' => 'Le match n\'existe pas' ) ) );
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

    public function ajaxGetFieldsAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $fields = $em->getRepository('MatchBundle:Field')->findAll();

                $normalizer  = new ObjectNormalizer();;
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $serializer = new Serializer( array( $normalizer ) );
                $fields = $serializer->normalize( $fields );

                $response = new Response( json_encode(array('status' => 'ok', 'values' => $fields)));
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

    public function ajaxGetRefereeAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $referees = $em->getRepository( 'UserBundle:User' )->findByRole( 'ROLE_ARBITRE' );
                $referees = array_merge( $em->getRepository( 'UserBundle:User' )->findByRole( 'ROLE_ADMIN' ), $referees );
                $referees = array_merge( $em->getRepository( 'UserBundle:User' )->findByRole( 'ROLE_SUPER_ADMIN' ), $referees );

                $normalizer  = new ObjectNormalizer();;
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $serializer = new Serializer( array( $normalizer ) );
                $referees = $serializer->normalize( $referees );

                $response = new Response( json_encode(array('status' => 'ok', 'values' => $referees)));
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
