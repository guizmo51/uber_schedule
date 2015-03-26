<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \DateTime;
use \DateTimeZone;

class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {	
    	/*var_dump("HELLO");
    	$date = new DateTime('2015-01-01 13:00:00', new DateTimeZone('America/Los_Angeles'));
    	var_dump($date->format("H:i:s"));
    	$date->setTimezone(new DateTimeZone('America/New_York'));
    	var_dump($date->format("H:i:s"));
    	$date->setTimezone(new DateTimeZone('Europe/Paris'));
    	var_dump($date->format("H:i:s")); */

       
        $client = $this->get('guzzle.service_builder')->get('uber.client');
        $request = $client->get('http://www.google.fr/');
        var_dump($request);

        return $this->render('default/index.html.twig');
    }
    public static function factory($config = array()) {
    // ...
    $client->setDescription(__DIR__ . '/client.json');

    //$authPlugin = new \Guzzle\Plugin\CurlAuth\CurlAuthPlugin('username', 'password');
    

    }   
    public function testAction(){
    	var_dump("test");
    	die;
    }
}
