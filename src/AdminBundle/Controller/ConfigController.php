<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $configs = $em->getRepository( 'AdminBundle:Config' )->getAll();

        return $this->render( 'AdminBundle:Config:index.html.twig', array( 'configs' => $configs ) );
    }

    public function ajaxEditDateAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $config = $em->getRepository( 'AdminBundle:Config' )->findOneByName( $request->get( 'column' ) );

                if($config->getValue() != $request->get( 'value' ) ) {
                    $config->setValue($request->get('value'));

                    $em->flush();

                    $response = new Response( json_encode( array( 'status' => 'ok', 'return' => true ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ok', 'return' => false ) ) );
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
